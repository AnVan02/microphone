# mic_bridge.py - WebSocket Audio Server với quản lý kết nối chặt chẽ
import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import queue
import uuid
import json
import logging

# ========================
#  CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
BUFFER_SIZE = int(os.getenv('MIC_BRIDGE_BUFFER', '256'))
SESSION_TIMEOUT = 300  # 5 phút timeout
RECONNECT_TIMEOUT = 30  # 30 giây cho phép reconnect

# Cấu hình logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    datefmt='%H:%M:%S'
)
logger = logging.getLogger(__name__)

# ========================
#  QUẢN LÝ KẾT NỐI
# ========================
class ConnectionManager:
    def __init__(self):
        self.current_connection = None
        self.session_id = None
        self.expected_token = None
        self.last_activity = None
        self.audio_queue = queue.Queue(maxsize=20)
        self.active_tokens = set()
        self.connection_start_time = None
        
    def is_connected(self):
        return self.current_connection is not None and not self.audio_queue.empty()
    
    def can_connect(self, session_id, token):
        """Kiểm tra có thể kết nối không"""
        if not self.is_connected():
            return True
            
        # Cho phép reconnect với cùng session_id
        if self.session_id == session_id:
            return True
            
        # Kiểm tra token hợp lệ
        if token in self.active_tokens:
            return True
            
        return False
    
    def register_connection(self, websocket, session_id, token):
        """Đăng ký kết nối mới"""
        if self.is_connected() and self.session_id != session_id and token not in self.active_tokens:
            return False, "Another user is already connected"
            
        self.current_connection = websocket
        self.session_id = session_id
        self.expected_token = token
        self.last_activity = time.time()
        self.connection_start_time = time.time()
        self.active_tokens.add(token)
        
        logger.info(f"✅ Đăng ký kết nối: Session={session_id}, Token={token[:8]}...")
        return True, "Connection registered successfully"
    
    def unregister_connection(self, session_id):
        """Hủy đăng ký kết nối"""
        if self.session_id == session_id:
            logger.info(f"🧹 Hủy đăng ký: Session={session_id}")
            self.current_connection = None
            self.session_id = None
            self.expected_token = None
            self.connection_start_time = None
            # Dọn dẹp queue
            while not self.audio_queue.empty():
                try:
                    self.audio_queue.get_nowait()
                except:
                    break
            return True
        return False
    
    def update_activity(self):
        """Cập nhật thời gian hoạt động"""
        if self.is_connected():
            self.last_activity = time.time()
    
    def check_timeout(self):
        """Kiểm tra timeout"""
        if self.is_connected() and self.last_activity:
            elapsed = time.time() - self.last_activity
            if elapsed > SESSION_TIMEOUT:
                logger.warning(f"⏰ Timeout phiên {self.session_id} sau {elapsed:.1f}s")
                self.unregister_connection(self.session_id)
                return True
        return False
    
    def get_connection_info(self):
        """Lấy thông tin kết nối"""
        if self.is_connected():
            elapsed = time.time() - self.connection_start_time
            last_activity = time.time() - self.last_activity
            return {
                'session_id': self.session_id,
                'connected_time': f"{elapsed:.1f}s",
                'last_activity': f"{last_activity:.1f}s",
                'queue_size': self.audio_queue.qsize()
            }
        return None

# Khởi tạo manager
connection_manager = ConnectionManager()

# ========================
#  XỬ LÝ AUDIO
# ========================
def optimize_audio_quality(audio_data):
    """Tối ưu chất lượng audio"""
    if len(audio_data) == 0:
        return audio_data
        
    audio_data = audio_data.astype(np.float32)
    new_max = np.max(np.abs(audio_data))
    target_max = 0.9999

    if new_max > 0.01 and new_max < target_max:
        audio_data = np.clip(audio_data * (target_max / new_max), -1.0, 1.0)
    
    return audio_data

def audio_playback_loop(device_id):
    """Luồng phát audio liên tục"""
    try:
        with sd.OutputStream(
            samplerate=SAMPLE_RATE,
            channels=CHANNELS,
            dtype=np.float32,
            device=device_id,
            blocksize=BUFFER_SIZE,
            latency='low'
        ) as stream:
            logger.info(f"🎵 Luồng phát audio sẵn sàng (Device: {sd.query_devices(device_id)['name']})")
            
            while True:
                try:
                    # Kiểm tra timeout
                    if connection_manager.check_timeout():
                        time.sleep(0.1)
                        continue
                    
                    if connection_manager.is_connected():
                        try:
                            # Lấy audio từ queue
                            audio_data = connection_manager.audio_queue.get(timeout=0.1)
                            if audio_data is not None and len(audio_data) > 0:
                                # Tối ưu và phát audio
                                optimized = optimize_audio_quality(audio_data)
                                chunk_size = BUFFER_SIZE
                                
                                for i in range(0, len(optimized), chunk_size):
                                    chunk = optimized[i:i+chunk_size]
                                    if len(chunk) > 0:
                                        stream.write(chunk.reshape(-1, 1).astype(np.float32))
                        except queue.Empty:
                            # Queue rỗng, phát silence
                            silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                            stream.write(silence)
                    else:
                        # Không có kết nối, phát silence nhẹ
                        silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                        stream.write(silence)
                        time.sleep(0.1)
                        
                except Exception as e:
                    logger.error(f"❌ Lỗi phát audio: {e}")
                    time.sleep(0.01)
                    
    except Exception as e:
        logger.error(f"❌ Lỗi khởi tạo audio stream: {e}")

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    """Xử lý kết nối WebSocket"""
    session_id = str(uuid.uuid4())[:8]
    remote_addr = f"{websocket.remote_address[0]}:{websocket.remote_address[1]}"
    
    logger.info(f"🔗 Thử kết nối từ: {remote_addr} (Session: {session_id})")
    
    try:
        # Đọc message đầu tiên (chứa token)
        initial_message = await asyncio.wait_for(websocket.recv(), timeout=10.0)
        
        if isinstance(initial_message, str) and initial_message.startswith('AUTH:'):
            token = initial_message.replace('AUTH:', '')
            
            # Kiểm tra có thể kết nối
            if not connection_manager.can_connect(session_id, token):
                rejection_msg = json.dumps({
                    'type': 'CONNECTION_REFUSED',
                    'message': 'Another user is already connected. Please try again later.'
                })
                await websocket.send(rejection_msg)
                await websocket.close()
                logger.warning(f"🚫 Từ chối kết nối: {remote_addr} - Đã có user khác")
                return
            
            # Đăng ký kết nối
            success, message = connection_manager.register_connection(websocket, session_id, token)
            if not success:
                rejection_msg = json.dumps({
                    'type': 'CONNECTION_REFUSED', 
                    'message': message
                })
                await websocket.send(rejection_msg)
                await websocket.close()
                return
            
            # Gửi xác nhận kết nối thành công
            welcome_msg = json.dumps({
                'type': 'CONNECTION_ACCEPTED',
                'session_id': session_id,
                'message': 'Connected successfully. You can now send audio data.'
            })
            await websocket.send(welcome_msg)
            logger.info(f"✅ Chấp nhận kết nối: {remote_addr} (Session: {session_id})")
            
            # Xử lý audio data
            async for message in websocket:
                connection_manager.update_activity()
                
                # Xử lý message JSON (control messages)
                if isinstance(message, str) and message.startswith('{'):
                    try:
                        data = json.loads(message)
                        if data.get('type') == 'HEARTBEAT':
                            # Phản hồi heartbeat
                            response = json.dumps({'type': 'HEARTBEAT_ACK', 'timestamp': time.time()})
                            await websocket.send(response)
                        continue
                    except json.JSONDecodeError:
                        pass
                
                # Xử lý audio data binary
                audio_data = np.frombuffer(message, dtype=np.float32)
                
                # Chuyển stereo sang mono nếu cần
                if len(audio_data) > 0 and len(audio_data) % 2 == 0:
                    audio_data = audio_data[::2]
                
                # Đưa vào queue để phát
                try:
                    connection_manager.audio_queue.put_nowait(audio_data)
                except queue.Full:
                    # Queue đầy, bỏ qua frame này
                    pass
                    
        else:
            # Message đầu tiên không hợp lệ
            await websocket.close()
            logger.warning(f"🚫 Message đầu tiên không hợp lệ từ: {remote_addr}")
            
    except asyncio.TimeoutError:
        logger.warning(f"⏰ Timeout chờ auth từ: {remote_addr}")
        await websocket.close()
    except websockets.exceptions.ConnectionClosed:
        logger.info(f"⚠️ Ngắt kết nối: {remote_addr} (Session: {session_id})")
    except Exception as e:
        logger.error(f"❌ Lỗi xử lý WebSocket: {e}")
    finally:
        # Dọn dẹp kết nối
        connection_manager.unregister_connection(session_id)
        logger.info(f"🧹 Đã dọn dẹp phiên {session_id}. Sẵn sàng cho kết nối mới.")

# ========================
#  TIMEOUT CHECKER
# ========================
async def timeout_checker():
    """Định kỳ kiểm tra timeout"""
    while True:
        try:
            connection_manager.check_timeout()
            
            # Log trạng thái mỗi 30s
            info = connection_manager.get_connection_info()
            if info:
                logger.info(f"📊 Trạng thái: {info}")
            else:
                logger.info("📊 Trạng thái: Đang chờ kết nối...")
                
        except Exception as e:
            logger.error(f"❌ Lỗi timeout checker: {e}")
        
        await asyncio.sleep(10)

# ========================
#  TÌM VB-CABLE
# ========================
def find_vb_cable():
    """Tìm thiết bị VB-CABLE"""
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        if 'cable input' in d['name'].lower() and d['max_output_channels'] > 0:
            logger.info(f"🎯 Tìm thấy VB-CABLE: {d['name']} (ID: {i})")
            return i
    
    # Thử tìm các thiết bị cable khác
    for i, d in enumerate(devices):
        if 'cable' in d['name'].lower() and d['max_output_channels'] > 0:
            logger.info(f"🎯 Tìm thấy audio cable: {d['name']} (ID: {i})")
            return i
            
    logger.error("❌ Không tìm thấy VB-CABLE hoặc audio cable tương tự")
    return None

# ========================
#  MAIN SERVER
# ========================
async def main():
    """Khởi chạy server chính"""
    device_id = find_vb_cable()
    if device_id is None:
        logger.error("❌ Không tìm thấy VB-CABLE. Vui lòng cài đặt VB-CABLE trước.")
        return

    # Khởi động luồng phát audio
    audio_thread = threading.Thread(
        target=audio_playback_loop, 
        args=(device_id,), 
        daemon=True,
        name="AudioPlaybackThread"
    )
    audio_thread.start()
    logger.info("🎵 Đã khởi động luồng phát audio")

    # Khởi động timeout checker
    asyncio.create_task(timeout_checker())
    logger.info("⏰ Đã khởi động timeout checker")

    # Khởi động WebSocket server
    server = await websockets.serve(
        handle_audio, 
        "0.0.0.0", 
        8765,
        ping_interval=20,
        ping_timeout=10
    )

    logger.info("🚀 WebSocket Audio Server đã khởi động!")
    logger.info(f"📍 Địa chỉ: ws://0.0.0.0:8765")
    logger.info(f"⏰ Timeout: {SESSION_TIMEOUT} giây")
    logger.info(f"🎯 VB-CABLE: {sd.query_devices(device_id)['name']}")
    logger.info("=" * 50)

    try:
        await asyncio.Future()  # Chạy vô hạn
    except KeyboardInterrupt:
        logger.info("🛑 Nhận tín hiệu dừng...")
    finally:
        server.close()
        await server.wait_closed()
        logger.info("👋 Server đã dừng")

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n🛑 Đã dừng server")
    except Exception as e:
        logger.error(f"❌ Lỗi khởi chạy server: {e}")