import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import wave
from scipy import signal

# ========================
#  CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
BUFFER_SIZE = 2048  # ✅ Tăng buffer size
VERBOSE = os.getenv('MIC_BRIDGE_VERBOSE', '1') == '1'
last_print_time = time.time()

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
    """Tìm VB-Cable Input (ID=4) - thiết bị output để phát âm thanh vào"""
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        name = d['name']
        if 'cable input' in name.lower() and d['max_output_channels'] > 0:
            return i
    return 4

DEVICE_ID = find_vb_cable()
print(f"\n💡 HƯỚNG DẪN:")
print(f"1. Script này sẽ phát âm thanh vào: CABLE Input (device {DEVICE_ID})")
print(f"2. Trong Chrome/Windows: chọn 'CABLE Output' làm microphone")
print(f"✅ Tự động chọn device [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")

# Kiểm tra thiết bị
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
SAVE_INCOMING = os.getenv('MIC_BRIDGE_SAVE', '0') == '1'
SAVE_SECONDS = int(os.getenv('MIC_BRIDGE_SAVE_SECONDS', '4'))
_save_samples_threshold = SAVE_SECONDS * SAMPLE_RATE
_incoming_chunks = []
_save_lock = threading.Lock()
_saved_incoming = False

# ========================
#  TỐI ƯU HÓA ÂM THANH
# ========================
def optimize_audio_quality(audio_data):
    """Tối ưu hóa chất lượng âm thanh cho speech recognition"""
    audio_data = audio_data.astype(np.float32)
    
    # ✅ Normalize âm lượng
    max_val = np.max(np.abs(audio_data)) if audio_data.size > 0 else 0.0
    if max_val > 0.01:  # Tránh chia cho 0
        audio_data = audio_data / max_val * 0.8  # Normalize về 80%
    
    # ✅ Boost nếu quá nhỏ
    if max_val < 0.1:
        audio_data = np.clip(audio_data * 2.0, -1.0, 1.0)
    
    return audio_data

# ========================
#  XỬ LÝ AUDIO LIÊN TỤC
# ========================
def audio_playback_loop():
    """Vòng lặp phát âm thanh liên tục"""
    global current_audio_data, is_playing, last_print_time
    
    print("📊 Bắt đầu vòng lặp phát âm thanh...")
    
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
                            # Tối ưu hóa chất lượng âm thanh
                            optimized_audio = optimize_audio_quality(current_audio_data)
                            
                            # Đảm bảo dữ liệu là mono
                            if optimized_audio.ndim == 1:
                                audio_to_play = optimized_audio.reshape(-1, 1)
                            else:
                                audio_to_play = optimized_audio[:, 0].reshape(-1, 1)
                            
                            stream.write(audio_to_play.astype(np.float32))
                            is_playing = True
                            
                            if VERBOSE and time.time() - last_print_time >= 2.0:
                                print(f"📤 Phát audio: {len(audio_to_play)} samples, max: {np.max(np.abs(audio_to_play)):.4f}")
                                last_print_time = time.time()
                        except Exception as e:
                            print(f"⚠️ Lỗi phát audio: {e}")
                            is_playing = False
                    else:
                        silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                        stream.write(silence)
                        is_playing = False
                
                time.sleep(0.001)
                
    except Exception as e:
        print(f"❌ Lỗi audio stream: {e}")
        import traceback
        traceback.print_exc()

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    """Xử lý kết nối WebSocket và nhận audio data"""
    global current_audio_data, _incoming_chunks, _saved_incoming, last_print_time
    
    client_addr = websocket.remote_address
    print(f"✅ Client đã kết nối từ {client_addr}")
    
    try:
        async for message in websocket:
            try:
                # Chuyển đổi binary message thành numpy array
                raw_data = np.frombuffer(message, dtype=np.float32)
                
                # ✅ XỬ LÝ STEREO -> MONO
                if len(raw_data) % 2 == 0:  # Stereo format: LRLRLR...
                    # Tách left (phone) và right (PC mic)
                    left_channel = raw_data[0::2]   # Remote/phone audio
                    right_channel = raw_data[1::2]  # Local PC mic (không dùng)
                    
                    audio_data = left_channel  # ✅ Chỉ lấy phone audio
                    
                    if VERBOSE and time.time() - last_print_time >= 2.0:
                        print(f"📊 Stereo->Mono: Left={len(left_channel)}, Right={len(right_channel)}")
                else:
                    audio_data = raw_data
                
                # Debug info
                max_amplitude = np.max(np.abs(audio_data))
                print(f"📥 Nhận audio: {len(audio_data)} samples, âm lượng: {max_amplitude:.4f}")
                
                # Cập nhật audio data để phát
                with audio_lock:
                    current_audio_data = audio_data

                # Lưu audio để debug (nếu cần)
                if SAVE_INCOMING and not _saved_incoming:
                    try:
                        with _save_lock:
                            chunk = audio_data if audio_data.ndim == 1 else audio_data[:, 0]
                            _incoming_chunks.append(chunk.copy())
                            total = sum(c.shape[0] for c in _incoming_chunks)
                            
                            if total >= _save_samples_threshold:
                                combined = np.concatenate(_incoming_chunks)
                                combined = np.clip(combined, -1.0, 1.0)
                                int_data = (combined * 32767).astype('<i2')
                                
                                with wave.open('incoming_debug.wav', 'wb') as wf:
                                    wf.setnchannels(1)
                                    wf.setsampwidth(2)
                                    wf.setframerate(SAMPLE_RATE)
                                    wf.writeframes(int_data.tobytes())
                                
                                print(f"✅ Đã lưu incoming audio: incoming_debug.wav")
                                _saved_incoming = True
                    except Exception as e:
                        print(f"⚠️ Lỗi khi lưu audio: {e}")
                        
            except Exception as e:
                print(f"⚠️ Lỗi xử lý message: {e}")
                
    except websockets.exceptions.ConnectionClosed:
        print(f"❌ Client {client_addr} đã ngắt kết nối")
    except Exception as e:
        print(f"❌ Lỗi WebSocket: {e}")
    finally:
        # Reset audio khi client disconnect
        with audio_lock:
            current_audio_data = None
        print(f"🧹 Đã dọn dẹp kết nối từ {client_addr}")

# ========================
#  MAIN SERVER
# ========================
async def main():
    """Khởi động server WebSocket và audio playback"""
    # Bắt đầu thread phát audio
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()
    
    print(f"\n🎙️ WebSocket Server đang chạy tại ws://0.0.0.0:8765")
    print(f"📊 Phát audio vào device: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")
    print("📱 Hãy mở trình duyệt và kết nối...")
    print("ℹ️ Nhấn Ctrl+C để dừng server\n")
    
    # Khởi động WebSocket server
    async with websockets.serve(
        handle_audio, 
        "0.0.0.0", 
        8765,
        ping_interval=20,
        ping_timeout=10
    ):
        await asyncio.Future()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n👋 Đã dừng server")
    except Exception as e:
        print(f"\n❌ Lỗi không mong muốn: {e}")
        import traceback
        traceback.print_exc()