# mic_bridge.py - Micro Bridge Server với Speech-to-Text
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
from dataclasses import dataclass
from typing import Optional
from datetime import datetime, timedelta

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

# Timeout settings
CONNECTION_TIMEOUT = 300  # 5 phút
INACTIVITY_TIMEOUT = 180  # 3 phút không có audio

# ========================
#  DATA CLASSES
# ========================
@dataclass
class Connection:
    websocket: websockets.WebSocketServerProtocol
    connected_at: datetime
    last_activity: datetime
    client_ip: str
    is_active: bool = True

# ========================
#  BIẾN TOÀN CỤC
# ========================
current_audio_data = None
is_playing = False
audio_lock = threading.Lock()

# Quản lý kết nối
current_connection: Optional[Connection] = None
connection_lock = threading.Lock()

# Timeout tracking
inactivity_timer = None
connection_timer = None

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
print("=" * 60)
print("📊 DANH SÁCH THIẾT BỊ ÂM THANH")
print("=" * 60)

for i, d in enumerate(sd.query_devices()):
    marker = " (VB-CABLE)" if 'cable' in d['name'].lower() else ""
    print(f"[{i}] {d['name']}{marker}")
    print(f"    Input: {d['max_input_channels']} | Output: {d['max_output_channels']}")

# ========================
#  CHỌN VB-CABLE TỰ ĐỘNG
# ========================
def find_vb_cable():
    devices = sd.query_devices()
    
    # Ưu tiên tìm VB-CABLE
    for i, d in enumerate(devices):
        name = d['name'].lower()
        if ('cable' in name or 'virtual' in name) and d['max_output_channels'] > 0:
            return i
    
    # Nếu không tìm thấy, tìm thiết bị output đầu tiên
    for i, d in enumerate(devices):
        if d['max_output_channels'] > 0:
            return i
    
    return 0

DEVICE_ID = find_vb_cable()
device_name = sd.query_devices(DEVICE_ID)['name']

print("\n" + "=" * 60)
print("🎯 THIẾT BỊ ĐƯỢC CHỌN")
print("=" * 60)
print(f"Device ID: {DEVICE_ID}")
print(f"Device: {device_name}")
print("\n📋 HƯỚNG DẪN:")
print(f"1. Phát âm thanh vào: {device_name}")
print(f"2. Trong Chrome/Windows: chọn '{device_name}' làm microphone")
print("=" * 60 + "\n")

# Kiểm tra thiết bị phát
try:
    test_data = np.zeros(512, dtype=np.float32)
    sd.play(test_data, samplerate=SAMPLE_RATE, device=DEVICE_ID, blocking=False)
    sd.stop()
    print("✅ Thiết bị âm thanh hoạt động tốt\n")
except Exception as e:
    print(f"❌ Lỗi thiết bị: {e}")
    exit(1)

# ========================
#  TỐI ƯU ÂM THANH
# ========================
def optimize_audio_quality(audio_data):
    audio_data = audio_data.astype(np.float32)
    max_val = np.max(np.abs(audio_data)) if len(audio_data) > 0 else 0.0
    
    # Tăng gain nếu âm lượng quá nhỏ
    if 0.001 < max_val < 0.1:
        gain = 1.0 / max_val
        audio_data = np.clip(audio_data * gain, -1.0, 1.0)
    
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
            
            # Gửi kết quả STT qua WebSocket
            if current_connection and current_connection.is_active:
                try:
                    stt_result = {
                        'type': 'STT_RESULT',
                        'text': text,
                        'timestamp': datetime.now().isoformat()
                    }
                    asyncio.create_task(
                        current_connection.websocket.send(json.dumps(stt_result))
                    )
                except:
                    pass
            
            print(f"🗣️ [STT] {text}")
            
    except sr.UnknownValueError:
        if VERBOSE:
            print("❓ [STT] Không nhận diện được giọng nói")
    except sr.RequestError as e:
        print(f"❌ [STT] Lỗi dịch vụ: {e}")
    except Exception as e:
        print(f"❌ [STT] Lỗi xử lý: {e}")

# ========================
#  TIMEOUT MANAGEMENT
# ========================
def reset_inactivity_timer():
    global inactivity_timer
    if inactivity_timer:
        inactivity_timer.cancel()
    
    inactivity_timer = threading.Timer(INACTIVITY_TIMEOUT, check_inactivity)
    inactivity_timer.daemon = True
    inactivity_timer.start()

def reset_connection_timer():
    global connection_timer
    if connection_timer:
        connection_timer.cancel()
    
    connection_timer = threading.Timer(CONNECTION_TIMEOUT, check_connection_timeout)
    connection_timer.daemon = True
    connection_timer.start()

def check_inactivity():
    global current_connection
    
    with connection_lock:
        if current_connection and current_connection.is_active:
            time_since_activity = (datetime.now() - current_connection.last_activity).total_seconds()
            
            if time_since_activity > INACTIVITY_TIMEOUT:
                print(f"⏰ Timeout không hoạt động sau {INACTIVITY_TIMEOUT} giây")
                
                try:
                    asyncio.create_task(current_connection.websocket.close(1000, "Timeout không hoạt động"))
                except:
                    pass
                
                current_connection = None

def check_connection_timeout():
    global current_connection
    
    with connection_lock:
        if current_connection and current_connection.is_active:
            connection_duration = (datetime.now() - current_connection.connected_at).total_seconds()
            
            if connection_duration > CONNECTION_TIMEOUT:
                print(f"⏰ Timeout kết nối sau {CONNECTION_TIMEOUT} giây")
                
                try:
                    asyncio.create_task(current_connection.websocket.close(1000, "Timeout kết nối"))
                except:
                    pass
                
                current_connection = None

# ========================
#  VÒNG LẶP PHÁT ÂM THANH
# ========================
def audio_playback_loop():
    global current_audio_data, is_playing
    print("▶️ Bắt đầu phát âm thanh...")
    
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
                        optimized = optimize_audio_quality(data)
                        audio_to_play = optimized.reshape(-1, 1)
                        stream.write(audio_to_play.astype(np.float32))
                        is_playing = True
                        
                        if VERBOSE:
                            max_amp = np.max(np.abs(audio_to_play))
                            print(f"📤 Phát: {len(audio_to_play)} mẫu, âm lượng: {max_amp:.4f}")
                    
                    except Exception as e:
                        print(f"❌ Lỗi phát audio: {e}")
                        is_playing = False
                else:
                    # Phát silence
                    silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                    stream.write(silence)
                    is_playing = False
                
                time.sleep(0.001)
    
    except Exception as e:
        print(f"❌ Lỗi stream audio: {e}")

# ========================
#  XỬ LÝ STT THEO VAD
# ========================
def stt_ingest_and_maybe_decode(audio_chunk: np.ndarray):
    global stt_buffer, last_voice_time
    
    now = time.time()
    
    with stt_lock:
        if audio_chunk.size > 0:
            stt_buffer = np.concatenate([stt_buffer, audio_chunk])
    
    # Kiểm tra hoạt động âm thanh
    amp = np.max(np.abs(audio_chunk)) if audio_chunk.size > 0 else 0.0
    
    if amp >= VAD_THRESHOLD:
        last_voice_time = now
        
        # Reset inactivity timer khi có audio
        reset_inactivity_timer()
    
    silence_ms = (now - last_voice_time) * 1000.0
    buf_len_ms = (stt_buffer.size / SAMPLE_RATE) * 1000.0
    
    # Kiểm tra và xử lý STT khi đủ điều kiện
    if silence_ms >= VAD_SILENCE_MS and buf_len_ms >= VAD_MIN_SPEECH_MS:
        with stt_lock:
            buf = stt_buffer.copy()
            stt_buffer = np.array([], dtype=np.float32)
        
        # Xử lý STT trong thread riêng để không block audio
        threading.Thread(target=buffer_to_text_and_print, args=(buf,), daemon=True).start()

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    global current_audio_data, _incoming_chunks, _saved_incoming, current_connection
    
    client_addr = f"{websocket.remote_address[0]}:{websocket.remote_address[1]}"
    print(f"\n🔗 Client thử kết nối: {client_addr}")
    
    # KIỂM TRA NẾU ĐÃ CÓ KẾT NỐI - TỪ CHỐI NGƯỜI THỨ 2
    with connection_lock:
        if current_connection is not None and current_connection.is_active:
            print(f"⛔ TỪ CHỐI: Đã có kết nối từ {current_connection.client_ip}")
            print(f"   Client {client_addr} bị từ chối (chỉ cho phép 1 kết nối)")
            
            try:
                await websocket.close(1008, "Chỉ cho phép một kết nối. Đã có người dùng khác.")
            except:
                pass
            
            return
        
        # CHẤP NHẬN KẾT NỐI MỚI
        current_connection = Connection(
            websocket=websocket,
            connected_at=datetime.now(),
            last_activity=datetime.now(),
            client_ip=client_addr,
            is_active=True
        )
        
        print(f"✅ CHẤP NHẬN: Kết nối từ {client_addr}")
        print(f"   Thời gian: {current_connection.connected_at.strftime('%H:%M:%S')}")
    
    # Khởi động timer
    reset_inactivity_timer()
    reset_connection_timer()
    
    try:
        # Gửi thông báo kết nối thành công
        welcome_msg = {
            'type': 'CONNECTION_ACCEPTED',
            'message': 'Kết nối thành công',
            'timestamp': datetime.now().isoformat()
        }
        await websocket.send(json.dumps(welcome_msg))
        
        print("🎤 Đang chờ nhận âm thanh...")
        
        async for message in websocket:
            try:
                # Cập nhật thời gian hoạt động
                with connection_lock:
                    if current_connection and current_connection.is_active:
                        current_connection.last_activity = datetime.now()
                
                # Xử lý audio data
                audio_data = np.frombuffer(message, dtype=np.float32)
                
                if audio_data.size > 0:
                    with audio_lock:
                        current_audio_data = audio_data
                    
                    if VERBOSE:
                        amp = np.max(np.abs(audio_data))
                        print(f"📡 Nhận từ {client_addr}: {len(audio_data)} mẫu, âm lượng: {amp:.4f}")
                    
                    # Lưu debug nếu cần
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
                    
                    # Xử lý STT
                    stt_ingest_and_maybe_decode(audio_data)
                
            except Exception as e:
                print(f"❌ Lỗi xử lý từ {client_addr}: {e}")
    
    except websockets.exceptions.ConnectionClosed as e:
        print(f"\n🔌 Client ngắt kết nối: {client_addr}")
        print(f"   Code: {e.code}, Reason: {e.reason}")
    
    except Exception as e:
        print(f"\n❌ Lỗi WebSocket từ {client_addr}: {e}")
    
    finally:
        # Dọn dẹp kết nối
        with connection_lock:
            if current_connection and current_connection.websocket == websocket:
                current_connection.is_active = False
                current_connection = None
                print(f"🔄 Đã giải phóng kết nối từ {client_addr}")
        
        # Dừng timer
        if inactivity_timer:
            inactivity_timer.cancel()
        
        if connection_timer:
            connection_timer.cancel()
        
        # Xóa audio data
        with audio_lock:
            current_audio_data = None
        
        # Xử lý STT còn lại
        with stt_lock:
            buf = stt_buffer.copy()
            stt_buffer = np.array([], dtype=np.float32)
        
        if buf.size > 0:
            buffer_to_text_and_print(buf)
        
        print(f"✅ Đã dọn dẹp hoàn toàn kết nối từ {client_addr}\n")

# ========================
#  MAIN SERVER - PORT 8766
# ========================
async def main():
    # Khởi động thread phát âm thanh
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()
    time.sleep(1)  # Đợi thread khởi động
    
    print("=" * 60)
    print("🎙️ MIC BRIDGE SERVER - PORT 8766")
    print("=" * 60)
    print(f"🌐 WebSocket Server: ws://0.0.0.0:8766")
    print(f"🔊 Output Device: [{DEVICE_ID}] {device_name}")
    print(f"⏰ Timeout: {CONNECTION_TIMEOUT}s kết nối, {INACTIVITY_TIMEOUT}s không hoạt động")
    print("=" * 60)
    print("📱 Mở trình duyệt → kết nối từ điện thoại")
    print("⚠️  Chỉ cho phép 1 kết nối tại 1 thời điểm")
    print("❌  Điện thoại thứ 2 sẽ nhận thông báo 'KẾT NỐI KHÔNG THÀNH CÔNG'")
    print("🛑 Nhấn Ctrl+C để dừng server")
    print("=" * 60 + "\n")
    
    # Khởi động WebSocket server trên PORT 8766
    async with websockets.serve(
        handle_audio, 
        "0.0.0.0", 
        8766,  # PORT 8766 (không dùng 8765 nữa)
        ping_interval=20, 
        ping_timeout=10,
        max_size=10 * 1024 * 1024
    ):
        await asyncio.Future()

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