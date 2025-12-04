# mic_bridge_stt.py
import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import wave
import io

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
BUFFER_SIZE = int(os.getenv('MIC_BRIDGE_BUFFER', '256'))
VERBOSE = os.getenv('MIC_BRIDGE_VERBOSE', '1') == '1'
SAVE_INCOMING = os.getenv('MIC_BRIDGE_SAVE', '0') == '1'
SAVE_SECONDS = 4

# ========================
#  BIẾN TOÀN CỤC
# ========================
current_audio_data = None
is_playing = False
audio_lock = threading.Lock()

# Quản lý kết nối
current_connection = None
connection_lock = threading.Lock()

# Lưu incoming để debug
_save_samples_threshold = SAVE_SECONDS * SAMPLE_RATE
_incoming_chunks = []
_save_lock = threading.Lock()
_saved_incoming = False

# Bộ đệm và VAD cho STT
stt_lock = threading.Lock()
stt_buffer = np.array([], dtype=np.float32)
last_voice_time = time.time()
VAD_THRESHOLD = 0.02       # ngưỡng biên độ coi là nói
VAD_MIN_SPEECH_MS = 300    # tối thiểu 0.3s để coi là câu nói
VAD_SILENCE_MS = 800       # khoảng lặng 0.8s để cắt câu
MAX_STT_BUFFER_SECONDS = 15  # tránh buffer quá dài

recognizer = sr.Recognizer() if SPEECH_RECOGNITION_AVAILABLE else None

# ========================
#  IN DANH SÁCH THIẾT BỊ
# ========================
print("📊 Danh sách thiết bị âm thanh:\n")
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
    for i, d in enumerate(devices):
        if d['max_output_channels'] > 0:
            return i
    return 0

DEVICE_ID = find_vb_cable()
print(f"\n📋 HƯỚNG DẪN:")
print(f"1. Phát âm thanh vào: CABLE Input (device {DEVICE_ID})")
print(f"2. Trong Chrome/Windows: chọn 'CABLE Output' làm microphone")
print(f"🎯 Đã chọn: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")

# Kiểm tra thiết bị phát
try:
    test_data = np.zeros(512, dtype=np.float32)
    sd.play(test_data, samplerate=SAMPLE_RATE, device=DEVICE_ID, blocking=False)
    sd.stop()
    print("✅ Thiết bị âm thanh hoạt động tốt")
except Exception as e:
    print(f"❌ Lỗi thiết bị: {e}")
    exit(1)

# ========================
#  TỐI ƯU ÂM THANH
# ========================
def optimize_audio_quality(audio_data):
    audio_data = audio_data.astype(np.float32)
    max_val = np.max(np.abs(audio_data)) if len(audio_data) > 0 else 0.0
    if max_val < 0.1 and max_val > 0:
        audio_data = np.clip(audio_data * 2.0, -1.0, 1.0)
    return audio_data

# ========================
#  CHUYỂN BUFFER → TEXT
# ========================
def buffer_to_text_and_print(buf: np.ndarray):
    if not SPEECH_RECOGNITION_AVAILABLE or buf.size == 0:
        return
    try:
        max_samples = int(MAX_STT_BUFFER_SECONDS * SAMPLE_RATE)
        if buf.size > max_samples:
            buf = buf[-max_samples:]

        int_data = np.clip(buf, -1.0, 1.0)
        int_data = (int_data * 32767.0).astype('<i2')
        wav_bytes = io.BytesIO()
        with wave.open(wav_bytes, 'wb') as wf:
            wf.setnchannels(1)
            wf.setsampwidth(2)
            wf.setframerate(SAMPLE_RATE)
            wf.writeframes(int_data.tobytes())
        wav_bytes.seek(0)

        with sr.AudioFile(wav_bytes) as source:
            audio = recognizer.record(source)
            text = recognizer.recognize_google(audio, language="vi-VN")
            print(f"🗣️ [STT] {text}")
    except sr.UnknownValueError:
        if VERBOSE:
            print("❓ [STT] Không nhận diện được")
    except sr.RequestError as e:
        print(f"❌ [STT] Lỗi dịch vụ: {e}")
    except Exception as e:
        print(f"❌ [STT] Lỗi xử lý: {e}")

# ========================
#  VÒNG LẶP PHÁT ÂM THANH
# ========================
def audio_playback_loop():
    global current_audio_data, is_playing
    print("▶️ Bắt đầu phát âm thanh vào VB-CABLE...")
    try:
        with sd.OutputStream(
            samplerate=SAMPLE_RATE,
            channels=CHANNELS,
            dtype=np.float32,
            device=DEVICE_ID,
            blocksize=BUFFER_SIZE,
            latency='low'
        ) as stream:
            print("🔊 Stream sẵn sàng")
            while True:
                with audio_lock:
                    data = current_audio_data
                    current_audio_data = None
                if data is not None and len(data) > 0:
                    try:
                        optimized = optimize_audio_quality(data)
                        audio_to_play = optimized.reshape(-1, 1)
                        stream.write(audio_to_play.astype(np.float32))
                        is_playing = True
                        if VERBOSE:
                            print(f"📤 Phát: {len(audio_to_play)} mẫu, max: {np.max(np.abs(audio_to_play)):.4f}")
                    except Exception as e:
                        print(f"❌ Lỗi phát: {e}")
                        is_playing = False
                else:
                    silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                    stream.write(silence)
                    is_playing = False
                time.sleep(0.001)
    except Exception as e:
        print(f"❌ Lỗi stream: {e}")

# ========================
#  XỬ LÝ STT THEO VAD
# ========================
def stt_ingest_and_maybe_decode(audio_chunk: np.ndarray):
    global stt_buffer, last_voice_time
    now = time.time()
    
    with stt_lock:
        if audio_chunk.size > 0:
            stt_buffer = np.concatenate([stt_buffer, audio_chunk])
    amp = np.max(np.abs(audio_chunk)) if audio_chunk.size > 0 else 0.0
    if amp >= VAD_THRESHOLD:
        last_voice_time = now
    silence_ms = (now - last_voice_time) * 1000.0
    buf_len_ms = (stt_buffer.size / SAMPLE_RATE) * 1000.0

    if silence_ms >= VAD_SILENCE_MS and buf_len_ms >= VAD_MIN_SPEECH_MS:
        with stt_lock:
            buf = stt_buffer.copy()
            stt_buffer = np.array([], dtype=np.float32)
        buffer_to_text_and_print(buf)

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    global current_audio_data, _incoming_chunks, _saved_incoming, current_connection
    
    client_addr = websocket.remote_address
    print(f"🔗 Client thử kết nối: {client_addr}")
    
    # Kiểm tra nếu đã có kết nối
    with connection_lock:
        if current_connection is not None and current_connection.open:
            print(f"⛔ TỪ CHỐI: Đã có kết nối từ {current_connection.remote_address}")
            print(f"   Client {client_addr} bị từ chối (chỉ cho phép 1 kết nối)")
            await websocket.close(1008, "Chỉ cho phép một kết nối. Đã có người dùng khác.")
            return
        
        current_connection = websocket
        print(f"✅ CHẤP NHẬN: Kết nối từ {client_addr}")
        print(f"   Đang chờ nhận âm thanh...")


    try:
        await websocket.send("CONNECTED")
        
        async for message in websocket:
            try:
                audio_data = np.frombuffer(message, dtype=np.float32)

                if VERBOSE and audio_data.size > 0:
                    print(f"📡 Nhận từ {client_addr}: {len(audio_data)} mẫu, âm lượng: {np.max(np.abs(audio_data)):.4f}")

                with audio_lock:
                    current_audio_data = audio_data

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
                            print("💾 Đã lưu: incoming_debug.wav")
                            _saved_incoming = True

                stt_ingest_and_maybe_decode(audio_data)

            except Exception as e:
                print(f"❌ Lỗi xử lý từ {client_addr}: {e}")
    except websockets.exceptions.ConnectionClosed as e:
        print(f"🔌 Client ngắt kết nối: {client_addr} (code: {e.code})")
    except Exception as e:
        print(f"❌ Lỗi WebSocket từ {client_addr}: {e}")
    finally:
        with connection_lock:
            if current_connection == websocket:
                current_connection = None
                print(f"🔄 Đã giải phóng kết nối từ {client_addr}, sẵn sàng cho kết nối mới")
        
        with audio_lock:
            current_audio_data = None
        
        with stt_lock:
            buf = stt_buffer.copy()
            stt_buffer = np.array([], dtype=np.float32)
        
        if buf.size > 0:
            buffer_to_text_and_print(buf)
        
        print(f"✅ Đã dọn dẹp kết nối từ {client_addr}")

# ========================
#  MAIN SERVER
# ========================
async def main():
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()

    print(f"\n🌐 WebSocket Server: ws://0.0.0.0:8765")
    print(f"🔊 Phát vào: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")
    print("📱 Mở trình duyệt → kết nối từ điện thoại")
    print("🛑 Ctrl+C để dừng\n")

    async with websockets.serve(handle_audio, "0.0.0.0", 8765, ping_interval=20, ping_timeout=10):
        await asyncio.Future()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n🛑 Đã dừng server")
    except Exception as e:
        print(f"❌ Lỗi: {e}")
        import traceback
        traceback.print_exc()
    
