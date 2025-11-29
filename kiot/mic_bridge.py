# mic_bridge.py
import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import wave

# speech recogition config

try :
    import speech_recognition as sr 
    SPEECH_RECOGNITION_AVAILABLE = True
    print ("speechrecogntion đã sẵn sàng")
except ImportError :
    SPEECH_RECOGNITION_AVAILABLE = False 
    print ("speech không khả dụng tắt tính năng speech-to-text")
    print ("chạy: speechrecognition pyaudio")

# ========================
#  CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
BUFFER_SIZE = int(os.getenv('MIC_BRIDGE_BUFFER', '256'))
VERBOSE = os.getenv('MIC_BRIDGE_VERBOSE', '1') == '1'
last_print_time = time.time()

# ========================
#  IN DANH SÁCH THIẾT BỊ
# ========================
print("Danh sách thiết bị âm thanh:\n")
for i, d in enumerate(sd.query_devices()):
    marker = " (VB-CABLE)" if 'cable' in d['name'].lower() else ""
    print(f"[{i}] {d['name']}{marker}")
    print(f"    Input: {d['max_input_channels']} | Output: {d['max_output_channels']}\n")

# ========================
#  CHỌN VB-CABLE TỰ ĐỘNG
# ========================
def find_vb_cable():
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        name = d['name'].lower()
        if 'cable input' in name and d['max_output_channels'] > 0:
            return i
    return 4  # Default nếu không tìm thấy

DEVICE_ID = find_vb_cable()
print(f"\nHƯỚNG DẪN:")
print(f"1. Phát âm thanh vào: CABLE Input (device {DEVICE_ID})")
print(f"2. Trong Chrome/Windows: chọn 'CABLE Output' làm microphone")
print(f"Đã chọn: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")

# Kiểm tra thiết bị
try:
    test_data = np.zeros(512, dtype=np.float32)
    sd.play(test_data, samplerate=SAMPLE_RATE, device=DEVICE_ID, blocking=False)
    sd.stop()
    print("Thiết bị âm thanh hoạt động tốt")
except Exception as e:
    print(f"Lỗi thiết bị: {e}")
    exit(1)

# ========================
#  BIẾN TOÀN CỤC
# ========================
current_audio_data = None
is_playing = False
audio_lock = threading.Lock()
SAVE_INCOMING = os.getenv('MIC_BRIDGE_SAVE', '0') == '1'
SAVE_SECONDS = 4
_save_samples_threshold = SAVE_SECONDS * SAMPLE_RATE
_incoming_chunks = []
_save_lock = threading.Lock()
_saved_incoming = False

# ========================
#  TỐI ƯU ÂM THANH
# ========================
def optimize_audio_quality(audio_data):
    audio_data = audio_data.astype(np.float32)
    max_val = np.max(np.abs(audio_data))
    if max_val < 0.1:
        audio_data = np.clip(audio_data * 2.0, -1.0, 1.0)
    return audio_data

# ========================
#  VÒNG LẶP PHÁT ÂM THANH
# ========================

def audio_playback_loop():
    global current_audio_data, is_playing, last_print_time
    print("Bắt đầu phát âm thanh vào VB-CABLE...")
    try:
        with sd.OutputStream(
            samplerate=SAMPLE_RATE,
            channels=CHANNELS,
            dtype=np.float32,
            device=DEVICE_ID,
            blocksize=BUFFER_SIZE,
            latency='low'
        ) as stream:
            print("Stream sẵn sàng")
            while True:
                with audio_lock:
                    if current_audio_data is not None and len(current_audio_data) > 0:
                        try:
                            optimized = optimize_audio_quality(current_audio_data)
                            audio_to_play = optimized.reshape(-1, 1)
                            stream.write(audio_to_play.astype(np.float32))
                            is_playing = True
                            if VERBOSE and time.time() - last_print_time >= 2.0:
                                print(f"Phát: {len(audio_to_play)} mẫu, max: {np.max(np.abs(audio_to_play)):.4f}")
                                last_print_time = time.time()
                        except Exception as e:
                            print(f"Lỗi phát: {e}")
                            is_playing = False
                    else:
                        silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                        stream.write(silence)
                        is_playing = False
                time.sleep(0.001)
    except Exception as e:
        print(f"Lỗi stream: {e}")

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    global current_audio_data, _incoming_chunks, _saved_incoming, last_print_time
    client_addr = websocket.remote_address
    print(f"Client kết nối: {client_addr}")
    try:
        async for message in websocket:
            try:
                audio_data = np.frombuffer(message, dtype=np.float32)
                print(f"Nhận: {len(audio_data)} mẫu, âm lượng: {np.max(np.abs(audio_data)):.4f}")

                with audio_lock:
                    current_audio_data = audio_data

                # Lưu để debug
                if SAVE_INCOMING and not _saved_incoming:
                    with _save_lock:
                        chunk = audio_data.copy()
                        _incoming_chunks.append(chunk)
                        total = sum(len(c) for c in _incoming_chunks)
                        if total >= _save_samples_threshold:
                            combined = np.concatenate(_incoming_chunks)
                            combined = np.clip(combined, -1.0, 1.0)
                            int_data = (combined * 32767).astype('<i2')
                            with wave.open('incoming_debug.wav', 'wb') as wf:
                                wf.setnchannels(1)
                                wf.setsampwidth(2)
                                wf.setframerate(SAMPLE_RATE)
                                wf.writeframes(int_data.tobytes())
                            print("Đã lưu: incoming_debug.wav")
                            _saved_incoming = True
            except Exception as e:
                print(f"Lỗi xử lý: {e}")
    except websockets.exceptions.ConnectionClosed:
        print(f"Client ngắt: {client_addr}")
    except Exception as e:
        print(f"Lỗi WebSocket: {e}")
    finally:
        with audio_lock:
            current_audio_data = None
        print("Dọn dẹp kết nối")

# ========================
#  MAIN SERVER
# ========================
async def main():
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()

    print(f"\nWebSocket Server: ws://0.0.0.0:8765")
    print(f"Phát vào: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")
    print("Mở trình duyệt → kết nối từ điện thoại")
    print("Ctrl+C để dừng\n")

    async with websockets.serve(handle_audio, "0.0.0.0", 8765, ping_interval=20, ping_timeout=10):
        await asyncio.Future()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\nĐã dừng server")
    except Exception as e:
        print(f"Lỗi: {e}")
        import traceback
        traceback.print_exc()