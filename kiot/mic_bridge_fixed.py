#!/usr/bin/env python3
# mic_bridge_fixed.py
# Cải tiến cho mic_bridge.py:
# - Dùng queue (deque) thay vì ghi đè current_audio_data
# - Tăng BUFFER mặc định
# - Chuyển và chuẩn hoá Float32 -> int16 trước khi phát để Chrome/VB-Cable nhận tốt
# - Thêm tùy chọn lưu incoming_debug.wav (MIC_BRIDGE_SAVE=1)
# - In log rõ ràng hơn
import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import wave
from collections import deque

# ========================
#  CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
BUFFER_SIZE = int(os.getenv('MIC_BRIDGE_BUFFER', '1024'))  # tăng buffer
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
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        name = d['name']
        if 'cable input' in name.lower() and d['max_output_channels'] > 0:
            return i
    return None

DEVICE_ID = find_vb_cable()
if DEVICE_ID is None:
    # Fallback: try find any device with 'cable' in name
    for i, d in enumerate(sd.query_devices()):
        if 'cable' in d['name'].lower():
            DEVICE_ID = i
            break

if DEVICE_ID is None:
    print("⚠️ Không tìm thấy VB-Cable. Vui lòng cài đặt VB-Audio Virtual Cable và thử lại.")
    # nhưng không exit, để user có thể chọn thủ công
    DEVICE_ID = int(os.getenv('MIC_BRIDGE_DEVICE', '4'))

print(f"\n💡 HƯỚNG DẪN:")
print(f"1. Script này sẽ phát âm thanh vào device id = {DEVICE_ID}")
try:
    print(f"   Thiết bị được chọn: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")
except Exception:
    print("   Không thể lấy tên device - kiểm tra DEVICE_ID bằng tay nếu cần.")

# Kiểm tra thiết bị
try:
    test_data = np.zeros(512, dtype=np.float32)
    sd.play(test_data, samplerate=SAMPLE_RATE, device=DEVICE_ID, blocking=False)
    sd.stop()
    print("✅ Thiết bị âm thanh hoạt động tốt (test play)")
except Exception as e:
    print(f"❌ Lỗi thiết bị âm thanh: {e}")
    # không exit, vẫn cho phép debug

# ========================
#  BIẾN TOÀN CỤC (QUEUE)
# ========================
audio_queue = deque(maxlen=400)  # queue chứa các chunk float32
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
    audio_data = audio_data.astype(np.float32)
    max_val = np.max(np.abs(audio_data)) if audio_data.size>0 else 0.0
    if max_val < 0.1:
        audio_data = np.clip(audio_data * 1.5, -1.0, 1.0)
    return audio_data

# ========================
#  VÒNG LẶP PHÁT ÂM THANH (đọc từ queue)
# ========================
def audio_playback_loop():
    global is_playing, last_print_time, _saved_incoming
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
                to_play = None
                with audio_lock:
                    if len(audio_queue) > 0:
                        to_play = audio_queue.popleft()

                if to_play is not None and to_play.size > 0:
                    try:
                        optimized = optimize_audio_quality(to_play)
                        # đảm bảo mono column vector
                        if optimized.ndim == 1:
                            audio_arr = optimized.reshape(-1, 1)
                        else:
                            audio_arr = optimized[:, 0].reshape(-1, 1)

                        # Convert float32 (-1..1) -> int16 -> back to float32 normalized
                        clipped = np.clip(audio_arr, -1.0, 1.0)
                        int16 = (clipped * 32767).astype('<i2')   # little-endian int16
                        audio_to_play = (int16.astype(np.float32) / 32767.0).astype(np.float32)
                        # ensure shape (N,1)
                        if audio_to_play.ndim == 1:
                            audio_to_play = audio_to_play.reshape(-1, 1)

                        stream.write(audio_to_play)
                        is_playing = True
                        if VERBOSE and time.time() - last_print_time >= 2.0:
                            print(f"📤 Phát audio: {audio_to_play.shape[0]} samples, max: {np.max(np.abs(audio_to_play)):.4f}")
                            last_print_time = time.time()
                    except Exception as e:
                        print(f"⚠️ Lỗi khi phát audio: {e}")
                        is_playing = False
                else:
                    # phát silence nếu queue rỗng
                    silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                    try:
                        stream.write(silence)
                    except Exception:
                        pass
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
    global _incoming_chunks, _saved_incoming
    client_addr = websocket.remote_address
    print(f"✅ Client đã kết nối từ {client_addr}")

    try:
        async for message in websocket:
            try:
                # message là binary Float32Array (little-endian)
                audio_data = np.frombuffer(message, dtype=np.float32)
                if audio_data.size == 0:
                    continue

                # debug print
                if VERBOSE and time.time() - last_print_time >= 2.0:
                    print(f"📥 Nhận audio: {audio_data.shape[0]} samples, max: {np.max(np.abs(audio_data)):.4f}")

                # đảm bảo mono 1d
                if audio_data.ndim > 1:
                    audio_data = audio_data[:, 0]

                # push vào queue
                with audio_lock:
                    audio_queue.append(audio_data.copy())

                # Lưu incoming để debug nếu bật
                if SAVE_INCOMING and not _saved_incoming:
                    with _save_lock:
                        _incoming_chunks.append(audio_data.copy())
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
                            print(f"✅ Đã lưu incoming audio: incoming_debug.wav ({total} samples)")
                            _saved_incoming = True
            except Exception as e:
                print(f"⚠️ Lỗi xử lý message: {e}")
    except websockets.exceptions.ConnectionClosed:
        print(f"❌ Client {client_addr} đã ngắt kết nối")
    except Exception as e:
        print(f"❌ Lỗi WebSocket: {e}")
    finally:
        print(f"🧹 Dọn dẹp kết nối từ {client_addr}")

# ========================
#  MAIN SERVER
# ========================
async def main():
    # Bắt đầu thread phát audio
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()

    print(f"\n🎙️ WebSocket Server đang chạy tại ws://0.0.0.0:8765")
    try:
        print(f"🔊 Phát audio vào device: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")
    except Exception:
        print("🔊 Không xác định tên device")

    print("📱 Hãy mở trình duyệt và kết nối...")
    print("⏹️ Nhấn Ctrl+C để dừng server\n")

    # Khởi động WebSocket server
    async with websockets.serve(
        handle_audio,
        "0.0.0.0",
        8765,
        ping_interval=20,
        ping_timeout=10
    ):
        await asyncio.Future()

    print("đang dừng server")
if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n🛑 Đang dừng server...")
    except Exception as e:
        print(f"❌ Lỗi không mong muốn: {e}")
    