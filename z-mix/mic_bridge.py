import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time

# ========================
#  CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1  # Chuyển sang mono để tương thích với YouTube
BUFFER_SIZE = 512  # Giảm buffer size để giảm độ trễ

# ========================
#  IN DANH SÁCH THIẾT BỊ
# ========================
print("🎧 Danh sách thiết bị âm thanh:\n")
for i, d in enumerate(sd.query_devices()):
    marker = ""
    if 'cable' in d['name'].lower():
        marker = " ⭐ VB-CABLE"
    print(f"[{i}] {d['name']}")
    print(f"    📥 Input: {d['max_input_channels']} | 📤 Output: {d['max_output_channels']}{marker}\n")

# ========================
#  CHỌN DEVICE TỰ ĐỘNG
# ========================
def find_vb_cable():
    """Tìm VB-Cable Input (thiết bị output để phát âm thanh vào)"""
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        name = d['name'].lower()
        if any(cable_name in name for cable_name in ['cable input', 'vb-cable', 'virtual cable']):
            if d['max_output_channels'] > 0:
                return i
    return None

vb_device = find_vb_cable()

if vb_device is None:
    print("❌ Không tìm thấy VB-Cable. Vui lòng nhập ID thiết bị thủ công:")
    DEVICE_ID = int(input("Nhập device ID: "))
else:
    DEVICE_ID = vb_device
    print(f"✅ Tự động chọn device [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")

# Kiểm tra thiết bị có hoạt động không
try:
    test_data = np.zeros(512, dtype=np.float32)
    sd.play(test_data, samplerate=SAMPLE_RATE, device=DEVICE_ID, blocking=False)
    sd.stop()
    print("✅ Thiết bị âm thanh hoạt động tốt")
except Exception as e:
    print(f"❌ Lỗi thiết bị âm thanh: {e}")
    exit(1)

# ========================
#  BIẾN TOÀN CỤC
# ========================
current_audio_data = None
is_playing = False
audio_lock = threading.Lock()

# ========================
#  XỬ LÝ AUDIO LIÊN TỤC
# ========================

def audio_playback_loop():
    """Vòng lặp phát âm thanh liên tục"""
    global current_audio_data, is_playing
    
    print("🔊 Bắt đầu vòng lặp phát âm thanh...")
    
    try:
        with sd.OutputStream(
            samplerate=SAMPLE_RATE,
            channels=CHANNELS,
            dtype=np.float32,
            device=DEVICE_ID,
            blocksize=BUFFER_SIZE,
            latency='low'
        ) as stream:
            
            print("✅ Audio stream đã sẵn sàng")
            
            while True:
                with audio_lock:
                    if current_audio_data is not None and len(current_audio_data) > 0:
                        try:
                            # Đảm bảo dữ liệu là mono
                            if current_audio_data.ndim == 1:
                                audio_to_play = current_audio_data.reshape(-1, 1)
                            else:
                                audio_to_play = current_audio_data[:, 0].reshape(-1, 1)  # Lấy kênh trái
                            stream.write(audio_to_play.astype(np.float32))
                            is_playing = True
                            print(f"📤 Phát audio: {len(audio_to_play)} samples, max: {np.max(audio_to_play):.4f}, min: {np.min(audio_to_play):.4f}")
                        except Exception as e:
                            print(f"⚠️ Lỗi phát audio: {e}")
                            is_playing = False
                    else:
                        silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                        stream.write(silence)
                        is_playing = False
                
                time.sleep(0.001)  # Giảm CPU usage
                
    except Exception as e:
        print(f"❌ Lỗi audio stream: {e}")
        import traceback
        traceback.print_exc()

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    global current_audio_data
    
    print(f"✅ Client đã kết nối từ {websocket.remote_address}")
    
    try:
        async for message in websocket:
            try:
                audio_data = np.frombuffer(message, dtype=np.float32)
                print(f"📥 Nhận audio: {len(audio_data)} samples, shape: {audio_data.shape}, max: {np.max(audio_data):.4f}, min: {np.min(audio_data):.4f}")
                
                with audio_lock:
                    current_audio_data = audio_data
                        
            except Exception as e:
                print(f"⚠️ Lỗi xử lý audio: {e}")
                
    except websockets.exceptions.ConnectionClosed:
        print(f"❌ Client {websocket.remote_address} đã ngắt kết nối")
        with audio_lock:
            current_audio_data = None

async def main():
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()
    
    print(f"\n🎙️ WebSocket Server đang chạy tại ws://0.0.0.0:8765")
    print(f"🔊 Phát audio vào device: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")
    print("📱 Hãy mở trình duyệt và kết nối...")
    print("⏹️ Nhấn Ctrl+C để dừng server\n")
    
    async with websockets.serve(handle_audio, "0.0.0.0", 8765, ping_interval=None):
        await asyncio.Future()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n👋 Đã dừng server")
    except Exception as e:
        print(f"\n❌ Lỗi: {e}")
        import traceback
        traceback.print_exc()
        