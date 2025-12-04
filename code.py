# mic_bridge_final.py - Micro Bridge Server với từ chối rõ ràng
import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import wave
import io
import json
import sys
from dataclasses import dataclass
from typing import Optional
from datetime import datetime, timedelta
import queue

# ========================
#  KIỂM TRA SPEECH-TO-TEXT
# ========================
try:
    import speech_recognition as sr
    SPEECH_RECOGNITION_AVAILABLE = True
    print("✅ SpeechRecognition đã sẵn sàng")
except ImportError:
    SPEECH_RECOGNITION_AVAILABLE = False
    print("❌ Speech-to-text không khả dụng. Cài đặt: pip install SpeechRecognition pyaudio")

# ========================
#  CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
BUFFER_SIZE = 256
VERBOSE = True
SAVE_INCOMING = False
SAVE_SECONDS = 4

# Timeout settings
CONNECTION_TIMEOUT = 300  # 5 phút
INACTIVITY_TIMEOUT = 180  # 3 phút không có audio

# ========================
#  BIẾN TOÀN CỤC - QUAN TRỌNG
# ========================
current_audio_data = None
is_playing = False
audio_lock = threading.Lock()
current_connection: Optional['Connection'] = None
connection_lock = threading.Lock()
active_peer_call = None

# Queue cho STT
stt_queue = queue.Queue()

# ========================
#  DATA CLASSES
# ========================
@dataclass
class Connection:
    websocket: websockets.WebSocketServerProtocol
    connected_at: datetime
    last_activity: datetime
    client_ip: str
    peer_id: str = ""
    is_active: bool = True

# ========================
#  CHỌN THIẾT BỊ PHONE ẢO
# ========================
def select_audio_device():
    """Chọn thiết bị audio output đúng cho phone ảo"""
    
    devices = sd.query_devices()
    print("\n" + "=" * 70)
    print("🎯 CHỌN THIẾT BỊ CHO PHONE ẢO")
    print("=" * 70)
    
    # Hiển thị tất cả thiết bị có output
    available_devices = []
    
    for i, d in enumerate(devices):
        if d['max_output_channels'] > 0:
            device_name = d['name']
            name_lower = device_name.lower()
            
            # Đánh dấu các thiết bị quan trọng
            if 'cable' in name_lower:
                marker = "🎯 [VB-CABLE - NÊN CHỌN]"
            elif 'virtual' in name_lower:
                marker = "🔌 [VIRTUAL - TỐT]"
            elif 'voicemeeter' in name_lower:
                marker = "🎚️ [VOICEMEETER]"
            elif 'array' in name_lower:
                marker = "⚠️ [MICROPHONE - KHÔNG NÊN CHỌN]"
            elif 'mic' in name_lower:
                marker = "⚠️ [MICROPHONE - KHÔNG NÊN CHỌN]"
            elif 'speaker' in name_lower:
                marker = "🔈 [SPEAKER]"
            elif 'headphone' in name_lower or 'headset' in name_lower:
                marker = "🎧 [HEADPHONE]"
            else:
                marker = "💻 [AUDIO DEVICE]"
            
            available_devices.append((i, d, marker))
    
    # Hiển thị
    for i, d, marker in available_devices:
        default_text = " (Mặc định)" if d['name'] == sd.default.device[1] else ""
        print(f"{marker}")
        print(f"   [{i}] {d['name']}{default_text}")
        print(f"   Output channels: {d['max_output_channels']}")
        print()
    
    # Tự động tìm và chọn VB-CABLE
    for i, d, marker in available_devices:
        if 'cable' in d['name'].lower():
            print(f"\n✅ TỰ ĐỘNG CHỌN VB-CABLE: [{i}] {d['name']}")
            return i
    
    # Nếu không có VB-CABLE, chọn thiết bị đầu tiên không phải mic
    for i, d, marker in available_devices:
        if 'array' not in d['name'].lower() and 'mic' not in d['name'].lower():
            print(f"\n⚡ Chọn tự động: [{i}] {d['name']}")
            return i
    
    # Nếu tất cả đều là mic, chọn đầu tiên
    if available_devices:
        print(f"\n⚠️  Cảnh báo: Chọn microphone làm output: [{available_devices[0][0]}] {available_devices[0][1]['name']}")
        return available_devices[0][0]
    
    return 0

DEVICE_ID = select_audio_device()
device_name = sd.query_devices(DEVICE_ID)['name']

print(f"\n✅ THIẾT BỊ ĐÃ CHỌN: {device_name}")

# ========================
#  XỬ LÝ SPEECH-TO-TEXT (Background Thread)
# ========================
def stt_worker():
    """Xử lý nhận dạng giọng nói trong background"""
    if not SPEECH_RECOGNITION_AVAILABLE:
        return

    print("🗣️ STT Worker đang chạy...")
    recognizer = sr.Recognizer()
    
    # Buffer để tích lũy audio
    audio_buffer = []
    buffer_duration = 0
    MAX_BUFFER_DURATION = 5.0  # Xử lý mỗi 5 giây
    
    while True:
        try:
            # Lấy chunk audio từ queue
            chunk = stt_queue.get()
            
            if chunk is None:
                continue
                
            audio_buffer.append(chunk)
            
            # Tính thời lượng buffer (ước lượng)
            # chunk là numpy array float32
            duration = len(chunk) / SAMPLE_RATE
            buffer_duration += duration
            
            if buffer_duration >= MAX_BUFFER_DURATION:
                # Gộp các chunk lại
                full_audio = np.concatenate(audio_buffer)
                
                # Convert float32 [-1, 1] to int16
                audio_int16 = (full_audio * 32767).astype(np.int16)
                
                # Tạo AudioData cho speech_recognition
                audio_bytes = audio_int16.tobytes()
                audio_data = sr.AudioData(audio_bytes, SAMPLE_RATE, 2) # 2 bytes width (16-bit)
                
                try:
                    # Nhận dạng
                    text = recognizer.recognize_google(audio_data, language="vi-VN")
                    print(f"🗣️ STT: {text}")
                    
                    # Gửi kết quả về client qua WebSocket
                    if current_connection and current_connection.is_active:
                        asyncio.run_coroutine_threadsafe(
                            current_connection.websocket.send(json.dumps({
                                'type': 'STT_RESULT',
                                'text': text
                            })),
                            asyncio.get_event_loop()
                        )
                except sr.UnknownValueError:
                    pass # Không nghe rõ
                except Exception as e:
                    print(f"❌ Lỗi STT: {e}")
                
                # Reset buffer
                audio_buffer = []
                buffer_duration = 0
                
        except Exception as e:
            print(f"❌ Lỗi STT Worker: {e}")
            time.sleep(1)

# ========================
#  TIMEOUT CHECKER (Background Task)
# ========================
async def check_timeouts():
    """Kiểm tra và ngắt kết nối quá hạn"""
    while True:
        await asyncio.sleep(10) # Kiểm tra mỗi 10 giây
        
        with connection_lock:
            if current_connection and current_connection.is_active:
                now = datetime.now()
                
                # 1. Kiểm tra tổng thời gian kết nối
                conn_duration = (now - current_connection.connected_at).total_seconds()
                if conn_duration > CONNECTION_TIMEOUT:
                    print(f"⏱️ HẾT GIỜ: Kết nối đã vượt quá {CONNECTION_TIMEOUT}s. Ngắt kết nối.")
                    asyncio.create_task(disconnect_client(current_connection.websocket, "TIMEOUT_MAX_DURATION"))
                    continue
                
                # 2. Kiểm tra thời gian không hoạt động
                inactive_duration = (now - current_connection.last_activity).total_seconds()
                if inactive_duration > INACTIVITY_TIMEOUT:
                    print(f"💤 INACTIVE: Không hoạt động quá {INACTIVITY_TIMEOUT}s. Ngắt kết nối.")
                    asyncio.create_task(disconnect_client(current_connection.websocket, "TIMEOUT_INACTIVITY"))
                    continue

async def disconnect_client(ws, reason_code):
    """Ngắt kết nối client an toàn"""
    try:
        await ws.send(json.dumps({
            'type': 'DISCONNECT',
            'reason': reason_code,
            'message': 'Kết nối đã hết hạn hoặc không hoạt động.'
        }))
        await ws.close(1000, reason_code)
    except:
        pass

# ========================
#  WEBSOCKET HANDLER - TỪ CHỐI RÕ RÀNG
# ========================
async def handle_audio(websocket):
    global current_audio_data, current_connection, active_peer_call
    
    client_addr = f"{websocket.remote_address[0]}:{websocket.remote_address[1]}"
    print(f"\n" + "=" * 60)
    print(f"🔗 CLIENT THỬ KẾT NỐI: {client_addr}")
    print("=" * 60)
    
    # ⭐⭐⭐ KIỂM TRA NẾU ĐÃ CÓ KẾT NỐI - TỪ CHỐI NGAY ⭐⭐⭐
    with connection_lock:
        if current_connection is not None and current_connection.is_active:
            print(f"🚨 ĐÃ CÓ KẾT NỐI TỪ: {current_connection.client_ip}")
            print(f"   👤 Người đang dùng: {current_connection.peer_id}")
            print(f"   ⛔ TỪ CHỐI client mới: {client_addr}")
            print("=" * 60)
            
            try:
                # Gửi thông báo từ chối CHÍNH XÁC
                rejection_message = json.dumps({
                    'type': 'CONNECTION_REJECTED',
                    'reason': 'PHONE_ẢO_ĐANG_BẬN',
                    'message': 'Phone ảo đang được sử dụng bởi người khác. Vui lòng thử lại sau.',
                    'timestamp': datetime.now().isoformat()
                })
                await websocket.send(rejection_message)
                
                # Đợi một chút để đảm bảo message được gửi
                await asyncio.sleep(0.5)
                
                # Đóng kết nối với code lỗi rõ ràng
                await websocket.close(1008, "Phone ảo đang bận. Chỉ cho phép một kết nối.")
                
                print(f"   ✅ Đã gửi thông báo từ chối đến {client_addr}")
                print("=" * 60)
            except Exception as e:
                print(f"   ❌ Lỗi khi từ chối: {e}")
            
            return  # ⭐ QUAN TRỌNG: DỪNG NGAY TẠI ĐÂY
        
        # ✅ CHẤP NHẬN KẾT NỐI MỚI
        print(f"✅ CHẤP NHẬN kết nối từ: {client_addr}")
        
        # Lấy peer_id từ query string (nếu có)
        query = websocket.request_line.split(' ')[1] if hasattr(websocket, 'request_line') else ''
        peer_id = ""
        if '?' in query:
            from urllib.parse import parse_qs, urlparse
            try:
                parsed = urlparse(query)
                params = parse_qs(parsed.query)
                peer_id = params.get('peer_id', [''])[0]
            except:
                pass
        
        current_connection = Connection(
            websocket=websocket,
            connected_at=datetime.now(),
            last_activity=datetime.now(),
            client_ip=client_addr,
            peer_id=peer_id,
            is_active=True
        )
        
        print(f"   👤 Peer ID: {peer_id}")
        print(f"   🕐 Thời gian: {current_connection.connected_at.strftime('%H:%M:%S')}")
        print("=" * 60)
    
    try:
        # Gửi thông báo chấp nhận
        welcome_msg = json.dumps({
            'type': 'CONNECTION_ACCEPTED',
            'message': 'Kết nối thành công đến Phone ảo',
            'timestamp': datetime.now().isoformat(),
            'max_users': 1,
            'current_user': 1
        })
        await websocket.send(welcome_msg)
        
        print("🎤 Đang chờ nhận âm thanh từ điện thoại...")
        
        # Nhận và xử lý audio
        async for message in websocket:
            try:
                with connection_lock:
                    if current_connection and current_connection.is_active:
                        current_connection.last_activity = datetime.now()
                
                if isinstance(message, bytes):
                    # Audio data
                    audio_data = np.frombuffer(message, dtype=np.float32)
                    
                    if audio_data.size > 0:
                        with audio_lock:
                            current_audio_data = audio_data
                        
                        # Gửi vào queue STT
                        if SPEECH_RECOGNITION_AVAILABLE:
                            stt_queue.put(audio_data)
                        
                        if VERBOSE and time.time() % 5 < 0.1:
                            amp = np.max(np.abs(audio_data))
                            print(f"📱 Nhận audio: {len(audio_data)} samples, vol: {amp:.4f}")
                
                elif isinstance(message, str):
                    # Text message
                    try:
                        data = json.loads(message)
                        if data.get('type') == 'HEARTBEAT':
                            # Phản hồi heartbeat
                            await websocket.send(json.dumps({
                                'type': 'HEARTBEAT_ACK',
                                'timestamp': datetime.now().isoformat()
                            }))
                        elif data.get('type') == 'DISCONNECT':
                            print(f"📴 Client yêu cầu ngắt kết nối: {client_addr}")
                            break
                    except:
                        pass
                
            except Exception as e:
                print(f"❌ Lỗi xử lý từ {client_addr}: {e}")
                break
    
    except websockets.exceptions.ConnectionClosed as e:
        print(f"\n🔌 Client ngắt kết nối: {client_addr}")
        print(f"   Code: {e.code}, Reason: {e.reason}")
        
        if e.code == 1008:  # Busy code từ client
            print("   📞 Client đã nhận được thông báo 'đang bận'")
    
    except Exception as e:
        print(f"\n❌ Lỗi WebSocket từ {client_addr}: {e}")
    
    finally:
        # ⭐⭐⭐ DỌN DẸP KHI KẾT NỐI ĐÓNG ⭐⭐⭐
        print(f"\n🧹 Đang dọn dẹp kết nối từ {client_addr}...")
        
        with connection_lock:
            if current_connection and current_connection.websocket == websocket:
                current_connection.is_active = False
                print(f"   🔓 Đã giải phóng kết nối từ {current_connection.client_ip}")
                current_connection = None
        
        with audio_lock:
            current_audio_data = None
        
        print(f"✅ Đã dọn dẹp hoàn toàn kết nối từ {client_addr}")
        print("🔄 Phone ảo sẵn sàng cho người dùng tiếp theo\n")

# ========================
#  VÒNG LẶP PHÁT ÂM THANH
# ========================
def audio_playback_loop():
    global current_audio_data, is_playing
    print("▶️ Bắt đầu phát âm thanh qua PHONE ẢO...")
    
    try:
        with sd.OutputStream(
            samplerate=SAMPLE_RATE,
            channels=CHANNELS,
            dtype=np.float32,
            device=DEVICE_ID,
            blocksize=BUFFER_SIZE,
            latency='low'
        ) as stream:
            print("🔊 Stream audio sẵn sàng")
            
            while True:
                with audio_lock:
                    data = current_audio_data
                    current_audio_data = None
                
                if data is not None and len(data) > 0:
                    try:
                        audio_to_play = data.reshape(-1, 1)
                        stream.write(audio_to_play.astype(np.float32))
                        is_playing = True
                        
                        if VERBOSE and time.time() % 10 < 0.1:
                            print(f"📤 Đang phát âm thanh: {len(audio_to_play)} samples")
                    
                    except Exception as e:
                        print(f"❌ Lỗi phát audio: {e}")
                        is_playing = False
                else:
                    silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                    stream.write(silence)
                    is_playing = False
                
                time.sleep(0.001)
    
    except Exception as e:
        print(f"❌ Lỗi stream audio: {e}")

# ========================
#  MAIN SERVER
# ========================
async def main():
    # Khởi động thread phát âm thanh
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()
    
    # Khởi động thread STT
    if SPEECH_RECOGNITION_AVAILABLE:
        stt_thread = threading.Thread(target=stt_worker, daemon=True)
        stt_thread.start()
    
    # Khởi động task kiểm tra timeout
    asyncio.create_task(check_timeouts())
    
    time.sleep(1)
    
    print("\n" + "=" * 70)
    print("🚀 PHONE ẢO SERVER - CHỈ 1 NGƯỜI/KẾT NỐI")
    print("=" * 70)
    print(f"🌐 WebSocket Server: ws://0.0.0.0:8766")
    print(f"🔊 Output Device: {device_name}")
    print(f"👤 Số kết nối: 1 người tại 1 thời điểm")
    print("=" * 70)
    print("📱 HƯỚNG DẪN:")
    print("1. Mở trình duyệt máy tính: microphone_final.php")
    print("2. Quét QR code bằng điện thoại")
    print("3. Nếu đã có người dùng → ĐIỆN THOẠI 2 sẽ thấy LỖI RÕ RÀNG")
    print("4. Chỉ khi người 1 thoát → người 2 mới kết nối được")
    print("=" * 70)
    print("🛑 Nhấn Ctrl+C để dừng server")
    print("=" * 70 + "\n")
    
    # Khởi động WebSocket server
    server = await websockets.serve(
        handle_audio, 
        "0.0.0.0", 
        8766,
        ping_interval=20, 
        ping_timeout=10,
        max_size=10 * 1024 * 1024
    )
    
    print(f"✅ Server đang chạy trên port 8766")
    print(f"📡 Đang chờ kết nối từ điện thoại...\n")
    
    await server.wait_closed()

# ========================
#  ENTRY POINT
# ========================
if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n\n🛑 Đã dừng server")
        print("👋 Tạm biệt!")
    except Exception as e:
        print(f"\n❌ Lỗi server: {e}")
        import traceback
        traceback.print_exc()