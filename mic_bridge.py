import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
from collections import deque

# ========================
# ⚙️ CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
BUFFER_SIZE = 512
MAX_QUEUE = 50

# ========================
# 🎯 CHỌN DEVICE VB-CABLE
# ========================
def find_vb_cable():
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        name = d['name'].lower()
        if any(cable_name in name for cable_name in ['cable input', 'vb-cable', 'virtual cable']):
            if d['max_output_channels'] > 0:
                return i
    return None

vb_device = find_vb_cable()
if vb_device is None:
    print("❌ Không tìm thấy VB-Cable. Nhập ID thủ công:")
    DEVICE_ID = int(input("Nhập device ID: "))
else:
    DEVICE_ID = vb_device
    print(f"✅ Chọn device [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")

# ========================
# 📡 BIẾN TOÀN CỤC
# ========================
audio_queue = deque()
audio_lock = threading.Lock()

# ========================
# 🎵 VÒNG LẶP PHÁT AUDIO
# ========================
def audio_playback_loop():
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
                    if audio_queue:
                        audio_data = audio_queue.popleft()
                        # CHỈNH SỬA QUAN TRỌNG: Chỉ lấy kênh trái (điện thoại)
                        if audio_data.ndim == 1:
                            # Nếu là mono, sử dụng trực tiếp
                            audio_to_play = audio_data.reshape(-1, 1)
                        else:
                            # Nếu là stereo, chỉ lấy kênh trái (điện thoại)
                            audio_to_play = audio_data[:, 0].reshape(-1, 1)
                        
                        stream.write(audio_to_play.astype(np.float32))
                        print(f"📤 Phát {len(audio_to_play)} samples từ điện thoại")
                    else:
                        # Phát silence khi không có dữ liệu
                        silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                        stream.write(silence)
    except Exception as e:
        print(f"❌ Lỗi audio stream: {e}")
        import traceback
        traceback.print_exc()

# ========================
# 🌐 WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    print(f"✅ Client kết nối từ {websocket.remote_address}")
    try:
        async for message in websocket:
            try:
                audio_data = np.frombuffer(message, dtype=np.float32)
                print(f"📥 Nhận {len(audio_data)} samples từ WebSocket")
                
                # CHỈNH SỬA: Xử lý dữ liệu stereo đúng cách
                if len(audio_data) % 2 == 0:
                    # Đây là dữ liệu stereo interleaved (L-R-L-R...)
                    stereo_frames = len(audio_data) // 2
                    audio_data = audio_data.reshape(stereo_frames, 2)
                    
                    # CHỈ lấy kênh trái (điện thoại)
                    phone_audio = audio_data[:, 0]
                    
                    with audio_lock:
                        if len(audio_queue) < MAX_QUEUE:
                            audio_queue.append(phone_audio)
                        else:
                            print("⚠️ Queue đầy, bỏ block")
                else:
                    print("⚠️ Dữ liệu audio không hợp lệ")
                    
            except Exception as e:
                print(f"⚠️ Lỗi xử lý audio: {e}")
    except websockets.exceptions.ConnectionClosed:
        print(f"❌ Client {websocket.remote_address} ngắt kết nối")

async def main():
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()
    print(f"\n🎙️ WebSocket Server chạy tại ws://0.0.0.0:8888")
    async with websockets.serve(handle_audio, "0.0.0.0", 8888, ping_interval=None):
        await asyncio.Future()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n👋 Dừng server")