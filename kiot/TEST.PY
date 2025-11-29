import asyncio
import websockets
import numpy as np
import sounddevice as sd
import queue
import threading
import time
import sys
import argparse
import wave
import os 


# ========================
# ⚙️ CẤU HÌNH NÂNG CAO
# ========================
# SAMPLE_RATE = 16000  # Google Speech yêu cầu 16kHz
# CHANNELS = 1
# CHUNK_SIZE = 512  # Giảm để giảm latency
# BUFFER_SIZE = 100  # Tăng buffer để ổn định hơn
SAMPLE_RATE = 48000
CHANNELS = 1 
BUFFER_SIZE = int(os.getenv ('MIC_BRIDGE_BUFFER', '256'))
VERBOSE = os.getenv('MIC_BRIDGE_VERBOSE' , '1') == '1'
last_print_time = time.time()

print("Danh sách thiết bị âm thanh :\n")
for i , d in enumerate(sd.query_devices()):
    marker = "(VB-CABLE)" if 'cable' in d['name']. lower() else ""
    print(f"[{i}] {d['name']}{marker}")
    print (f" Input: {d['max_input_channels']} | output: {d['max_output_channerls']}\n")


# Queue để lưu audio
audio_queue = queue.Queue(maxsize=BUFFER_SIZE)
is_running = True

# ========================
# 🎯 TÌM VÀ CHỌN THIẾT BỊ TỰ ĐỘNG
# ========================
def find_audio_device():
    """Tìm thiết bị audio phù hợp tự động"""
    print("=" * 70)
    print("🎧 ĐANG QUÉT THIẾT BỊ ÂM THANH...")
    print("=" * 70)
    
    try:
        devices = sd.query_devices()
        
        # Ưu tiên tìm VB-CABLE trước
        cable_devices = []
        other_output_devices = []
        
        for i, device in enumerate(devices):
            name_lower = device['name'].lower()
            
            # Kiểm tra device có output channels và hoạt động
            if device['max_output_channels'] > 0:
                if 'cable' in name_lower:
                    cable_devices.append((i, device))
                else:
                    other_output_devices.append((i, device))
        
        # Ưu tiên VB-CABLE
        if cable_devices:
            print("✅ TÌM THẤY THIẾT BỊ CABLE:")
            for i, device in cable_devices:
                print(f"   [{i}] {device['name']} (Output: {device['max_output_channels']} channels)")
            return cable_devices[0][0]
        
        # Fallback: thiết bị output khác
        if other_output_devices:
            print("⚠️ KHÔNG TÌM THẤY CABLE, SỬ DỤNG THIẾT BỊ KHÁC:")
            for i, device in other_output_devices[:3]:  # Hiển thị 3 thiết bị đầu
                print(f"   [{i}] {device['name']} (Output: {device['max_output_channels']} channels)")
            return other_output_devices[0][0]
            
        print("❌ KHÔNG TÌM THẤY THIẾT BỊ OUTPUT NÀO!")
        return None
        
    except Exception as e:
        print(f"❌ LỖI KHI QUÉT THIẾT BỊ: {e}")
        return None

# Tự động chọn device
DEVICE_ID = find_audio_device()

if DEVICE_ID is None:
    print("❌ KHÔNG THỂ TÌM THẤY THIẾT BỊ AUDIO PHÙ HỢP!")
    sys.exit(1)

# Xác nhận device đã chọn
devices = sd.query_devices()
selected_device = devices[DEVICE_ID]
print(f"\n🎯 ĐÃ CHỌN THIẾT BỊ: [{DEVICE_ID}] {selected_device['name']}")
print(f"   📊 Output Channels: {selected_device['max_output_channels']}")
print(f"   🎚️ Sample Rate: {SAMPLE_RATE} Hz")
print(f"   🔢 Chunk Size: {CHUNK_SIZE} samples")

# ========================
# 📊 THEO DÕI HOẠT ĐỘNG
# ========================
class AudioStats:
    def __init__(self):
        self.total_received = 0
        self.queue_drops = 0
        self.silent_frames = 0
        self.active_frames = 0
        self.last_print = time.time()
        self.audio_levels = []
        
    def update_audio_level(self, audio_data):
        """Cập nhật mức độ âm thanh"""
        if len(audio_data) > 0:
            rms = np.sqrt(np.mean(audio_data**2))
            self.audio_levels.append(rms)
            # Giữ lại 50 giá trị gần nhất
            if len(self.audio_levels) > 50:
                self.audio_levels.pop(0)
            
            if rms > 0.01:
                self.active_frames += 1
                self.silent_frames = 0
                return "🔊"
            else:
                self.silent_frames += 1
                return "🔇"
        return "❓"
    
    def get_avg_audio_level(self):
        """Lấy mức âm thanh trung bình"""
        if self.audio_levels:
            return np.mean(self.audio_levels)
        return 0

audio_stats = AudioStats()
active_clients = set()

# ========================
# 🎵 XỬ LÝ AUDIO NÂNG CAO
# ========================
def enhance_audio_for_speech(audio_data):
    """Xử lý audio tối ưu cho speech recognition"""
    if len(audio_data) == 0:
        return audio_data
    
    # 1. Remove DC offset
    audio_data = audio_data - np.mean(audio_data)
    
    # 2. Tính RMS và điều chỉnh gain
    rms = np.sqrt(np.mean(audio_data**2))
    
    if rms > 0:
        # 3. Dynamic gain adjustment
        target_rms = 0.3  # Mức target cho speech
        current_gain = target_rms / rms
        
        # Giới hạn gain để tránh distortion
        gain = np.clip(current_gain, 0.5, 3.0)
        audio_data = audio_data * gain
        
        # 4. Soft clipping để tránh peak distortion
        clip_threshold = 0.9
        audio_data = np.tanh(audio_data * 1.2) * 0.8  # Soft clip
        
        # 5. High-pass filter đơn giản (giảm low-frequency noise)
        if len(audio_data) > 1:
            alpha = 0.95
            audio_data[1:] = audio_data[1:] - alpha * audio_data[:-1]
    
    return audio_data

def resample_audio(audio_data, original_rate=48000, target_rate=16000):
    """Resample audio chất lượng cao"""
    if len(audio_data) == 0:
        return audio_data
        
    if original_rate == target_rate:
        return audio_data
        
    # Tỷ lệ resample
    ratio = original_rate / target_rate
    
    if ratio.is_integer():
        # Resample đơn giản cho tỷ lệ nguyên
        return audio_data[::int(ratio)]
    else:
        # Linear interpolation cho tỷ lệ không nguyên
        original_indices = np.arange(len(audio_data))
        target_length = int(len(audio_data) / ratio)
        target_indices = np.linspace(0, len(audio_data)-1, target_length)
        return np.interp(target_indices, original_indices, audio_data)

# ========================
# 🔊 AUDIO CALLBACK HOÀN CHỈNH
# ========================
def audio_callback(outdata, frames, time_info, status):
    """Callback phát audio - TỐI ƯU CHO SPEECH"""
    global audio_stats
    
    if status:
        print(f"⚠️ Audio Status: {status}")
    
    try:
        # Lấy audio từ queue
        data = audio_queue.get_nowait()
        audio_stats.total_received += len(data)
        
        # Xử lý kích thước khung
        if len(data) != frames:
            if len(data) < frames:
                # Thêm silence nếu thiếu
                pad_length = frames - len(data)
                silence = np.zeros(pad_length, dtype=np.float32)
                data = np.concatenate([data, silence])
            else:
                data = data[:frames]
        
        # Ghi vào output
        outdata[:, 0] = data
        
    except queue.Empty:
        # Không có data - output silence
        outdata.fill(0)
        
        # Log nếu silence kéo dài nhưng có client kết nối
        current_time = time.time()
        if (len(active_clients) > 0 and 
            current_time - audio_stats.last_print > 3.0 and
            audio_stats.silent_frames > 10):
            print("🔇 ĐANG CHỜ ÂM THANH TỪ ĐIỆN THOẠI...")
            audio_stats.last_print = current_time

# ========================
# 🎙️ AUDIO STREAM MANAGER
# ========================
def audio_playback_thread():
    """Quản lý audio stream - PHIÊN BẢN ỔN ĐỊNH"""
    global is_running
    
    print("\n🔊 ĐANG KHỞI ĐỘNG AUDIO STREAM...")
    
    max_retries = 3
    retry_count = 0
    
    while is_running and retry_count < max_retries:
        try:
            with sd.OutputStream(
                samplerate=SAMPLE_RATE,
                channels=CHANNELS,
                dtype=np.float32,
                device=DEVICE_ID,
                blocksize=CHUNK_SIZE,
                latency='low',
                callback=audio_callback
            ):
                print("✅ AUDIO STREAM ĐÃ SẴN SÀNG!")
                print("🎯 ĐANG CHỜ ÂM THANH TỪ ĐIỆN THOẠI...")
                
                # Reset retry count khi kết nối thành công
                retry_count = 0
                
                # Vòng lặp chính
                while is_running:
                    time.sleep(0.05)  # Giảm sleep để responsive hơn
                    
                    # Log stats định kỳ
                    current_time = time.time()
                    if current_time - audio_stats.last_print > 8.0:
                        qsize = audio_queue.qsize()
                        clients_count = len(active_clients)
                        avg_level = audio_stats.get_avg_audio_level()
                        
                        status_icon = "✅" if clients_count > 0 else "⏸️"
                        audio_icon = "🔊" if avg_level > 0.02 else "🔇"
                        
                        print(
                            f"📊 {status_icon} Clients: {clients_count} | "
                            f"{audio_icon} Queue: {qsize}/{BUFFER_SIZE} | "
                            f"Drops: {audio_stats.queue_drops} | "
                            f"Level: {avg_level:.3f}"
                        )
                        audio_stats.last_print = current_time
                    
        except sd.PortAudioError as e:
            retry_count += 1
            print(f"❌ Lỗi Audio Stream (lần {retry_count}/{max_retries}): {e}")
            
            if retry_count < max_retries:
                print(f"🔄 Thử lại sau 3 giây...")
                time.sleep(3)
            else:
                print("❌ Đã thử lại quá số lần cho phép!")
                break
                
        except Exception as e:
            print(f"❌ Lỗi không xác định: {e}")
            break
    
    is_running = False
    print("🔴 AUDIO STREAM ĐÃ DỪNG!")

# ========================
# 🌐 WEBSOCKET HANDLER HOÀN CHỈNH
# ========================
async def handle_websocket(websocket):
    """Xử lý WebSocket - TỐI ƯU CHO REAL-TIME AUDIO"""
    global active_clients, audio_stats
    
    client_id = f"{websocket.remote_address[0]}:{websocket.remote_address[1]}"
    print(f"✅ CLIENT KẾT NỐI: {client_id}")
    active_clients.add(client_id)
    
    # Biến cục bộ cho client
    client_stats = {
        'frames_received': 0,
        'last_audio_time': time.time(),
        'audio_quality': 'unknown'
    }
    
    try:
        async for message in websocket:
            try:
                # Nhận audio data
                audio_data = np.frombuffer(message, dtype=np.float32)
                client_stats['frames_received'] += 1
                client_stats['last_audio_time'] = time.time()
                
                # Kiểm tra chất lượng audio cơ bản
                if len(audio_data) > 0:
                    # Cập nhật audio level
                    level_icon = audio_stats.update_audio_level(audio_data)
                    
                    # Xử lý audio
                    processed_audio = enhance_audio_for_speech(audio_data)
                    
                    # Resample nếu cần (giả sử input là 48kHz)
                    if len(processed_audio) > 1000:  # Chỉ resample nếu audio đủ dài
                        processed_audio = resample_audio(processed_audio, 48000, SAMPLE_RATE)
                    
                    # Đưa vào queue
                    if not audio_queue.full():
                        audio_queue.put(processed_audio)
                    else:
                        # Queue đầy - xóa frame cũ nhất
                        try:
                            audio_queue.get_nowait()
                            audio_queue.put(processed_audio)
                            audio_stats.queue_drops += 1
                        except:
                            pass
                    
                    # Log chi tiết (giới hạn tần suất)
                    if client_stats['frames_received'] % 100 == 0:
                        rms = np.sqrt(np.mean(processed_audio**2))
                        print(f"📥 {level_icon} {client_id}: {len(processed_audio)} samples | RMS: {rms:.4f}")
                        
            except Exception as e:
                print(f"⚠️ Lỗi xử lý audio từ {client_id}: {e}")
                
    except websockets.exceptions.ConnectionClosed:
        print(f"🔌 CLIENT NGẮT KẾT NỐI: {client_id}")
    except Exception as e:
        print(f"❌ Lỗi WebSocket với {client_id}: {e}")
    finally:
        active_clients.discard(client_id)
        duration = time.time() - client_stats['last_audio_time']
        print(f"📊 {client_id} disconnected | Frames: {client_stats['frames_received']} | Duration: {duration:.1f}s")

# ========================
# 🛠️ TIỆN ÍCH VÀ HƯỚNG DẪN
# ========================
def print_system_checklist():
    """Checklist cấu hình hệ thống"""
    print("\n" + "=" * 80)
    print("🎯 CHECKLIST CẤU HÌNH HỆ THỐNG")
    print("=" * 80)
    print("1. 🎙️ CÀI ĐẶT WINDOWS SOUND:")
    print("   • Mở Settings > System > Sound")
    print("   • Input: Chọn 'CABLE Output'")
    print("   • Click 'Set as Default' ✅")
    print("   • Click 'Set as Default Communications Device' ✅")
    print()
    print("2. 🌐 CẤU HÌNH CHROME:")
    print("   • Mở: chrome://settings/content/microphone")
    print("   • Cho phép website sử dụng microphone")
    print("   • Chọn 'CABLE Output' làm microphone")
    print()
    print("3. 🧪 TEST HỆ THỐNG:")
    print("   • Mở: https://www.onlinemictest.com/")
    print("   • Cho phép sử dụng microphone")
    print("   • Nói thử → phải thấy thanh âm lượng nhảy")
    print()
    print("4. 🔍 TEST GOOGLE VOICE SEARCH:")
    print("   • Mở Google.com → click biểu tượng microphone")
    print("   • Nói thử → phải thấy sóng âm thanh và nhận diện")
    print("=" * 80)

def check_system_requirements():
    """Kiểm tra yêu cầu hệ thống"""
    print("\n🔍 KIỂM TRA HỆ THỐNG...")
    
    # Kiểm tra Python version
    python_version = sys.version_info
    if python_version.major < 3 or (python_version.major == 3 and python_version.minor < 7):
        print("❌ Python 3.7+ là bắt buộc!")
        return False
    
    # Kiểm tra port
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        result = sock.connect_ex(('localhost', 8765))
        if result == 0:
            print("❌ Port 8765 đang được sử dụng!")
            sock.close()
            return False
        sock.close()
    except:
        pass
    
    print("✅ HỆ THỐNG ĐẠT YÊU CẦU!")
    return True

# ========================
# 🚀 MAIN APPLICATION
# ========================
async def main():
    global is_running
    
    # Hiển thị thông tin ứng dụng
    print("\n" + "=" * 60)
    print("🎙️  WEBSOCKET TO VB-CABLE BRIDGE SERVER")
    print("=" * 60)
    print(f"📡 WebSocket: ws://0.0.0.0:8765")
    print(f"🔊 Output: [{DEVICE_ID}] {devices[DEVICE_ID]['name']}")
    print(f"🎚️ Sample Rate: {SAMPLE_RATE} Hz")
    print(f"🔢 Channels: {CHANNELS}")
    print("=" * 60)
    
    # Hiển thị checklist
    print_system_checklist()
    
    # Kiểm tra hệ thống
    if not check_system_requirements():
        print("❌ KHÔNG THỂ KHỞI ĐỘNG SERVER!")
        return
    
    # Khởi động audio thread
    print("\n🚀 ĐANG KHỞI ĐỘNG HỆ THỐNG...")
    audio_thread = threading.Thread(target=audio_playback_thread, daemon=True)
    audio_thread.start()
    
    # Chờ audio stream khởi động
    time.sleep(2)
    
    print("\n📱 HƯỚNG DẪN SỬ DỤNG:")
    print("1. Mở file microphone.php trên trình duyệt PC")
    print("2. Dùng điện thoại quét QR code để kết nối")
    print("3. Nói vào điện thoại → âm thanh vào CABLE Output")
    print("4. Mở website cần speech recognition để test")
    
    print("\n⚡ TRẠNG THÁI:")
    print("   • Audio Stream: ✅ Đã khởi động")
    print("   • WebSocket Server: ✅ Đang chờ kết nối...")
    print("   • VB-Cable Bridge: ✅ Sẵn sàng")
    
    print("\n⏹️ Nhấn Ctrl+C để dừng server\n")
    
    try:
        # Khởi động WebSocket server
        server = await websockets.serve(
            handle_websocket, 
            "0.0.0.0", 
            8765, 
            ping_interval=20,
            ping_timeout=30,
            close_timeout=10
        )
        
        print("🎯 SERVER ĐÃ SẴN SÀNG!")
        print("🔄 Đang chờ kết nối từ điện thoại...")
        
        # Giữ server chạy
        await asyncio.Future()
            
    except KeyboardInterrupt:
        print("\n🛑 NHẬN TÍN HIỆU DỪNG...")
    except Exception as e:
        print(f"❌ LỖI SERVER: {e}")
        import traceback
        traceback.print_exc()
    finally:
        is_running = False
        print("👋 SERVER ĐÃ DỪNG HOÀN TOÀN")

if __name__ == "__main__":
    # Parse arguments
    parser = argparse.ArgumentParser(description='WebSocket to VB-CABLE Audio Bridge')
    parser.add_argument('--port', type=int, default=8765, help='WebSocket port (default: 8765)')
    parser.add_argument('--sample-rate', type=int, default=16000, help='Sample rate (default: 16000)')
    parser.add_argument('--chunk-size', type=int, default=512, help='Chunk size (default: 512)')

    args = parser.parse_args()
    
    # Cập nhật cấu hình từ arguments
    SAMPLE_RATE = args.sample_rate
    CHUNK_SIZE = args.chunk_size
    
    try:
        # Import socket cho port check
        import socket
        
        # Chạy ứng dụng
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n🎯 SERVER ĐÃ DỪNG THEO YÊU CẦU")
    except Exception as e:
        print(f"❌ LỖI KHỞI ĐỘNG: {e}")
        import traceback
        traceback.print_exc()