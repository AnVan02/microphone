# mic_bridge.py
import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import wave
import queue

# ========================
#  CẤU HÌNH
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
BUFFER_SIZE = int(os.getenv('MIC_BRIDGE_BUFFER', '512'))
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
# GIẢI THÍCH CÁCH VB-CABLE HOẠT ĐỘNG:
# - VB-CABLE là một "cáp ảo" (virtual cable) hoạt động như: Input → Output
# - Code Python PHÁT audio vào "CABLE Input" (đầu vào của cáp ảo)
# - Chrome/Windows CHỌN "CABLE Output" (đầu ra của cáp ảo) làm microphone
# - Luồng: Python → CABLE Input → CABLE Output → Chrome/trình duyệt 
# - Đây là cách VB-CABLE được thiết kế: Input nhận audio, Output phát ra audio đó
def find_vb_cable():
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        name = d['name'].lower()
        # Tìm "CABLE Input" vì đây là nơi Python sẽ PHÁT audio vào
        if 'cable input' in name and d['max_output_channels'] > 0:
            return i
    return 4  # Default nếu không tìm thấy

DEVICE_ID = find_vb_cable()
device_name = sd.query_devices(DEVICE_ID)['name']
print(f"\n{'='*60}")
print(f"HƯỚNG DẪN SỬ DỤNG:")
print(f"{'='*60}")
print(f"📤 Python phát audio vào: CABLE Input (device {DEVICE_ID})")
print(f"📥 Chrome chọn làm mic: CABLE Output")
print(f"\nCách hoạt động:")
print(f"  Python → CABLE Input → CABLE Output → Chrome/trình duyệt ")
print(f"\nCác bước:")
print(f"1. Code này phát audio vào: {device_name}")
print(f"2. Trong Chrome: Click 🔒 → chọn 'CABLE Output' làm microphone")
print(f"3. Mở trình duyệt  → cho phép microphone → chọn 'CABLE Output'")
print(f"4. Nói trên điện thoại → trình duyệt  sẽ nhận được!")
print(f"{'='*60}")
print(f"✅ Đã chọn: [{DEVICE_ID}] {device_name}")

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
audio_queue = queue.Queue(maxsize=10)  # Queue để lưu audio chunks
is_playing = False
audio_lock = threading.Lock()
SAVE_INCOMING = os.getenv('MIC_BRIDGE_SAVE', '0') == '1'
SAVE_SECONDS = 4
_save_samples_threshold = SAVE_SECONDS * SAMPLE_RATE
_incoming_chunks = []
_save_lock = threading.Lock()
_saved_incoming = False
_last_audio_time = time.time()

# ========================
#  TỐI ƯU ÂM THANH
# ========================
def optimize_audio_quality(audio_data):
    audio_data = audio_data.astype(np.float32)
    max_val = np.max(np.abs(audio_data))
    
    # Tăng âm lượng nếu quá nhỏ (quan trọng cho speech recognition)
    if max_val < 0.05:
        # Âm thanh rất nhỏ, tăng mạnh
        audio_data = np.clip(audio_data * 4.0, -1.0, 1.0)
    elif max_val < 0.15:
        # Âm thanh nhỏ, tăng vừa
        audio_data = np.clip(audio_data * 2.5, -1.0, 1.0)
    elif max_val < 0.4:
        # Âm thanh trung bình, tăng nhẹ
        audio_data = np.clip(audio_data * 1.3, -1.0, 1.0)
    
    # Normalize để đảm bảo chất lượng tốt cho speech recognition
    new_max = np.max(np.abs(audio_data))
    if new_max > 0.01 and new_max < 0.7:
        # Tăng đến mức tối ưu cho speech (không quá lớn để tránh distortion)
        target_max = 0.75
        audio_data = np.clip(audio_data * (target_max / new_max), -1.0, 1.0)
    
    return audio_data

# ========================
#  VÒNG LẶP PHÁT ÂM THANH
# ========================
def audio_playback_loop():
    global is_playing, last_print_time
    print("Bắt đầu phát âm thanh vào VB-CABLE Input...")
    print("(Audio sẽ chảy từ CABLE Input → CABLE Output → Chrome)")
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
            print("Đang chờ audio từ WebSocket...")
            while True:
                try:
                    # Lấy audio từ queue (blocking với timeout ngắn)
                    try:
                        audio_data = audio_queue.get(timeout=0.1)
                        if audio_data is not None and len(audio_data) > 0:
                            try:
                                optimized = optimize_audio_quality(audio_data)
                                audio_to_play = optimized.reshape(-1, 1)
                                
                                # Chia nhỏ thành các chunk để phát liên tục
                                chunk_size = BUFFER_SIZE
                                for i in range(0, len(audio_to_play), chunk_size):
                                    chunk = audio_to_play[i:i+chunk_size]
                                    
                                    # Pad nếu thiếu
                                    if len(chunk) < chunk_size:
                                        padding = np.zeros((chunk_size - len(chunk), 1), dtype=np.float32)
                                        chunk = np.vstack([chunk, padding])
                                    
                                    stream.write(chunk.astype(np.float32))
                                
                                is_playing = True
                                if VERBOSE and time.time() - last_print_time >= 2.0:
                                    max_amp = np.max(np.abs(audio_to_play))
                                    status = "✅ Tốt" if max_amp > 0.1 else "⚠️ Nhỏ" if max_amp > 0.01 else "❌ Im lặng"
                                    queue_size = audio_queue.qsize()
                                    print(f"Phát: {len(audio_to_play)} mẫu, max: {max_amp:.4f} {status} | Queue: {queue_size}")
                                    last_print_time = time.time()
                            except Exception as e:
                                print(f"Lỗi phát: {e}")
                                is_playing = False
                    except queue.Empty:
                        # Không có audio mới, phát silence ngắn để giữ stream
                        silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                        stream.write(silence)
                        is_playing = False
                        
                except Exception as e:
                    print(f"Lỗi trong playback loop: {e}")
                    time.sleep(0.01)
    except Exception as e:
        print(f"Lỗi stream: {e}")

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    global _incoming_chunks, _saved_incoming, last_print_time, _last_audio_time
    client_addr = websocket.remote_address
    print(f"✅ Client kết nối: {client_addr}")
    print("Đang chờ audio từ client...")

    try:
        async for message in websocket:
            try:
                audio_data = np.frombuffer(message, dtype=np.float32)
                
                # Xử lý cả mono và stereo (nếu có)
                if len(audio_data) > 0:
                    # Nếu số lượng mẫu chẵn, có thể là stereo interleaved
                    if len(audio_data) % 2 == 0:
                        left_channel = audio_data[::2]
                        right_channel = audio_data[1::2]
                        # Nếu kênh phải toàn là 0 hoặc giống hệt kênh trái, thì là mono
                        if np.max(np.abs(right_channel)) < 0.001 or np.allclose(left_channel, right_channel, atol=0.01):
                            audio_data = left_channel  # Lấy mono
                        else:
                            # Là stereo thật, chỉ lấy kênh trái (điện thoại)
                            audio_data = left_channel
                
                # Đảm bảo audio_data là 1D array
                if len(audio_data.shape) > 1:
                    audio_data = audio_data.flatten()
                
                # Log thông tin audio
                max_amplitude = np.max(np.abs(audio_data))
                _last_audio_time = time.time()
                
                if VERBOSE and time.time() - last_print_time >= 2.0:
                    status = "✅ Có âm thanh" if max_amplitude > 0.01 else "⚠️ Im lặng"
                    queue_size = audio_queue.qsize()
                    print(f"📥 Nhận: {len(audio_data)} mẫu, âm lượng: {max_amplitude:.4f} {status} | Queue: {queue_size}")
                    last_print_time = time.time()

                # Thêm vào queue (bỏ qua nếu queue đầy để tránh delay)
                try:
                    audio_queue.put_nowait(audio_data)
                except queue.Full:
                    # Queue đầy, bỏ qua chunk cũ và thêm mới
                    try:
                        audio_queue.get_nowait()  # Bỏ chunk cũ
                        audio_queue.put_nowait(audio_data)  # Thêm chunk mới
                    except:
                        pass

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
                            print("💾 Đã lưu: incoming_debug.wav")
                            _saved_incoming = True
            except Exception as e:
                print(f"❌ Lỗi xử lý audio: {e}")
                import traceback
                traceback.print_exc()
    except websockets.exceptions.ConnectionClosed:
        print(f"⚠️ Client ngắt kết nối: {client_addr}")
    except Exception as e:
        print(f"❌ Lỗi WebSocket: {e}")
    finally:
        # Xóa queue khi ngắt kết nối
        while not audio_queue.empty():
            try:
                audio_queue.get_nowait()
            except:
                break
        print("🧹 Đã dọn dẹp kết nối")

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