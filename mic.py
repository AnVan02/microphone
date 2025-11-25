# mic_bridge_with_stt.py
import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import wave
import queue
import speech_recognition as sr
from io import BytesIO

# ========================
#  CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
BUFFER_SIZE = int(os.getenv('MIC_BRIDGE_BUFFER', '512'))
VERBOSE = os.getenv('MIC_BRIDGE_VERBOSE', '1') == '1'
ENABLE_STT = True  # Bật Speech-to-Text
STT_CHUNK_SECONDS = 3  # Nhận dạng mỗi 3 giây

# Queue cho audio playback và STT
audio_queue = queue.Queue(maxsize=10)
stt_queue = queue.Queue(maxsize=5)

# Biến toàn cục
last_print_time = time.time()
is_playing = False
recognizer = sr.Recognizer()

# ========================
#  TÌM VB-CABLE
# ========================
def find_vb_cable():
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        name = d['name'].lower()
        if 'cable input' in name and d['max_output_channels'] > 0:
            return i
    return None

DEVICE_ID = find_vb_cable()
if DEVICE_ID is None:
    print("⚠️ Không tìm thấy VB-CABLE, sử dụng thiết bị mặc định")
    DEVICE_ID = sd.default.device[1]

device_name = sd.query_devices(DEVICE_ID)['name']
print(f"✅ Đã chọn: [{DEVICE_ID}] {device_name}\n")

# ========================
#  TỐI ƯU ÂM THANH
# ========================
def optimize_audio_quality(audio_data):
    audio_data = audio_data.astype(np.float32)
    max_val = np.max(np.abs(audio_data))
    
    if max_val < 0.05:
        audio_data = np.clip(audio_data * 4.0, -1.0, 1.0)
    elif max_val < 0.15:
        audio_data = np.clip(audio_data * 2.5, -1.0, 1.0)
    elif max_val < 0.4:
        audio_data = np.clip(audio_data * 1.3, -1.0, 1.0)
    
    new_max = np.max(np.abs(audio_data))
    if new_max > 0.01 and new_max < 0.7:
        target_max = 0.75
        audio_data = np.clip(audio_data * (target_max / new_max), -1.0, 1.0)
    
    return audio_data

# ========================
#  SPEECH-TO-TEXT WORKER
# ========================
def stt_worker():
    """Thread xử lý Speech Recognition"""
    print("🎯 Speech-to-Text worker started...")
    accumulated_audio = []
    accumulated_samples = 0
    target_samples = SAMPLE_RATE * STT_CHUNK_SECONDS
    
    while True:
        try:
            # Lấy audio từ STT queue
            audio_chunk = stt_queue.get()
            if audio_chunk is None:
                break
                
            accumulated_audio.append(audio_chunk)
            accumulated_samples += len(audio_chunk)
            
            # Khi đủ audio, thực hiện nhận dạng
            if accumulated_samples >= target_samples:
                combined = np.concatenate(accumulated_audio)
                
                # Chuyển sang int16 để speech_recognition xử lý
                audio_int16 = (combined * 32767).astype(np.int16)
                
                # Tạo AudioData object
                audio_data = sr.AudioData(
                    audio_int16.tobytes(), 
                    SAMPLE_RATE, 
                    2  # 16-bit
                )
                
                try:
                    # Nhận dạng giọng nói (dùng Google Speech Recognition - miễn phí)
                    print("🎤 Đang nhận dạng giọng nói...")
                    text = recognizer.recognize_google(audio_data, language='vi-VN')
                    
                    # In kết quả với màu xanh lá
                    print(f"\n{'='*60}")
                    print(f"📝 NHẬN DẠNG: {text}")
                    print(f"{'='*60}\n")
                    
                except sr.UnknownValueError:
                    print("⚠️ Không nghe rõ, thử nói to hơn")
                except sr.RequestError as e:
                    print(f"❌ Lỗi API: {e}")
                except Exception as e:
                    print(f"❌ Lỗi STT: {e}")
                
                # Reset buffer
                accumulated_audio = []
                accumulated_samples = 0
                
        except Exception as e:
            print(f"❌ Lỗi STT worker: {e}")
            time.sleep(0.1)

# ========================
#  VÒNG LẶP PHÁT ÂM THANH
# ========================
def audio_playback_loop():
    global is_playing, last_print_time
    print("🔊 Bắt đầu phát âm thanh...")
    
    try:
        with sd.OutputStream(
            samplerate=SAMPLE_RATE,
            channels=CHANNELS,
            dtype=np.float32,
            device=DEVICE_ID,
            blocksize=BUFFER_SIZE,
            latency='low'
        ) as stream:
            while True:
                try:
                    audio_data = audio_queue.get(timeout=0.1)
                    if audio_data is not None and len(audio_data) > 0:
                        optimized = optimize_audio_quality(audio_data)
                        audio_to_play = optimized.reshape(-1, 1)
                        
                        # Chia nhỏ thành chunks
                        chunk_size = BUFFER_SIZE
                        for i in range(0, len(audio_to_play), chunk_size):
                            chunk = audio_to_play[i:i+chunk_size]
                            if len(chunk) < chunk_size:
                                padding = np.zeros((chunk_size - len(chunk), 1), dtype=np.float32)
                                chunk = np.vstack([chunk, padding])
                            stream.write(chunk.astype(np.float32))
                        
                        is_playing = True
                        
                        # Gửi audio sang STT queue
                        if ENABLE_STT:
                            try:
                                stt_queue.put_nowait(optimized.copy())
                            except queue.Full:
                                pass  # Bỏ qua nếu queue đầy
                        
                except queue.Empty:
                    silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                    stream.write(silence)
                    is_playing = False
                    
                except Exception as e:
                    print(f"❌ Lỗi playback: {e}")
                    
    except Exception as e:
        print(f"❌ Lỗi stream: {e}")

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    global last_print_time
    client_addr = websocket.remote_address
    print(f"✅ Client kết nối: {client_addr}")

    try:
        async for message in websocket:
            try:
                audio_data = np.frombuffer(message, dtype=np.float32)
                
                # Xử lý stereo/mono
                if len(audio_data) > 0:
                    if len(audio_data) % 2 == 0:
                        left_channel = audio_data[::2]
                        right_channel = audio_data[1::2]
                        if np.max(np.abs(right_channel)) < 0.001:
                            audio_data = left_channel
                        else:
                            audio_data = left_channel
                
                if len(audio_data.shape) > 1:
                    audio_data = audio_data.flatten()
                
                # Log
                max_amplitude = np.max(np.abs(audio_data))
                if VERBOSE and time.time() - last_print_time >= 2.0:
                    status = "✅ Có âm thanh" if max_amplitude > 0.01 else "⚠️ Im lặng"
                    print(f"📥 Nhận: {len(audio_data)} mẫu, âm lượng: {max_amplitude:.4f} {status}")
                    last_print_time = time.time()

                # Thêm vào queue
                try:
                    audio_queue.put_nowait(audio_data)
                except queue.Full:
                    try:
                        audio_queue.get_nowait()
                        audio_queue.put_nowait(audio_data)
                    except:
                        pass
                        
            except Exception as e:
                print(f"❌ Lỗi xử lý audio: {e}")
                
    except websockets.exceptions.ConnectionClosed:
        print(f"⚠️ Client ngắt kết nối: {client_addr}")
    except Exception as e:
        print(f"❌ Lỗi WebSocket: {e}")
    finally:
        while not audio_queue.empty():
            try:
                audio_queue.get_nowait()
            except:
                break

# ========================
#  MAIN SERVER
# ========================
async def main():
    # Khởi động threads
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()
    
    if ENABLE_STT:
        stt_thread = threading.Thread(target=stt_worker, daemon=True)
        stt_thread.start()
        print("🎯 Speech-to-Text: ĐÃ BẬT (tiếng Việt)\n")
    
    print(f"WebSocket Server: ws://0.0.0.0:8765")
    print(f"Phát vào: [{DEVICE_ID}] {device_name}")
    print("Mở trình duyệt → kết nối từ điện thoại")
    print("Ctrl+C để dừng\n")

    async with websockets.serve(handle_audio, "0.0.0.0", 8765, ping_interval=20, ping_timeout=10):
        await asyncio.Future()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n⛔ Đã dừng server")
        stt_queue.put(None)  # Stop STT worker
    except Exception as e:
        print(f"❌ Lỗi: {e}")
        import traceback
        traceback.print_exc()