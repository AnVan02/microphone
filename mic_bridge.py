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
BUFFER_SIZE = 512
VERBOSE = True
last_print_time = time.time()

# ========================
#  IN DANH SÁCH THIẾT BỊ
# ========================
print("🎧 Danh sách thiết bị âm thanh:\n")
for i, d in enumerate(sd.query_devices()):
    marker = " (VB-CABLE)" if 'cable' in d['name'].lower() else ""
    print(f"[{i}] {d['name']}{marker}")
    print(f"    📥 Input: {d['max_input_channels']} | 📤 Output: {d['max_output_channels']}\n")

def find_vb_cable():
    """Tìm VB-Cable Input để phát âm thanh vào"""
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        name = d['name'].lower()
        # Tìm "CABLE Input" - đây là nơi Python sẽ PHÁT audio vào
        if 'cable input' in name and d['max_output_channels'] > 0:
            return i
    # Nếu không tìm thấy, thử tìm các tên khác
    for i, d in enumerate(devices):
        name = d['name'].lower()
        if ('cable' in name or 'virtual' in name) and d['max_output_channels'] > 0:
            return i
    return None

DEVICE_ID = find_vb_cable()

if DEVICE_ID is None:
    print("❌ Không tìm thấy VB-Cable. Thiết bị có sẵn:")
    for i, d in enumerate(sd.query_devices()):
        if d['max_output_channels'] > 0:
            print(f"[{i}] {d['name']} (Output: {d['max_output_channels']})")
    print("⚠️  Chọn device mặc định")
    DEVICE_ID = sd.default.device[1] if sd.default.device[1] is not None else 1
else:
    device_name = sd.query_devices(DEVICE_ID)['name']
    print(f"✅ Đã chọn: [{DEVICE_ID}] {device_name}")

# Kiểm tra thiết bị
try:
    print("🔊 Kiểm tra thiết bị...")
    test_data = np.zeros(512, dtype=np.float32)
    sd.play(test_data, samplerate=SAMPLE_RATE, device=DEVICE_ID, blocking=False)
    sd.stop()
    print("✅ Thiết bị âm thanh hoạt động tốt")
except Exception as e:
    print(f"❌ Lỗi thiết bị: {e}")
    exit(1)

# ========================
#  BIẾN TOÀN CỤC
# ========================
audio_queue = queue.Queue(maxsize=20)  # Tăng queue size
is_playing = False
audio_lock = threading.Lock()
total_samples_received = 0
_last_audio_time = time.time()

# ========================
#  TỐI ƯU ÂM THANH - PHIÊN BẢN MẠNH HƠN
# ========================
def optimize_audio_quality(audio_data):
    """Tăng gain mạnh cho âm thanh từ điện thoại"""
    if len(audio_data) == 0:
        return audio_data
        
    audio_data = audio_data.astype(np.float32)
    max_val = np.max(np.abs(audio_data))
    
    # DEBUG: In thông tin âm thanh gốc
    print(f"🔊 RAW: max={max_val:.6f}, len={len(audio_data)}")
    
    # TĂNG GAIN CỰC MẠNH CHO ĐIỆN THOẠI
    if max_val < 0.0001:      # Rất rất nhỏ
        gain = 100.0
        print(f"🎚️ TĂNG GAIN X100 (rất rất nhỏ)")
    elif max_val < 0.001:     # Rất nhỏ
        gain = 50.0
        print(f"🎚️ TĂNG GAIN X50 (rất nhỏ)")
    elif max_val < 0.01:      # Nhỏ
        gain = 25.0
        print(f"🎚️ TĂNG GAIN X25 (nhỏ)")
    elif max_val < 0.05:      # Trung bình
        gain = 15.0
        print(f"🎚️ TĂNG GAIN X15 (trung bình)")
    elif max_val < 0.1:       # Khá
        gain = 8.0
        print(f"🎚️ TĂNG GAIN X8 (khá)")
    else:                     # Tốt
        gain = 3.0
        print(f"🎚️ TĂNG GAIN X3 (tốt)")
    
    # Áp dụng gain
    audio_data = audio_data * gain
    
    # Compressor để tránh distortion
    threshold = 0.9
    new_max = np.max(np.abs(audio_data))
    if new_max > threshold:
        compression_ratio = threshold / new_max
        audio_data = audio_data * compression_ratio
        print(f"🔧 NÉN ÂM: {compression_ratio:.3f}")
    
    # Final check và clip
    audio_data = np.clip(audio_data, -1.0, 1.0)
    final_max = np.max(np.abs(audio_data))
    print(f"🎵 SAU XỬ LÝ: max={final_max:.4f}")
    
    return audio_data

# ========================
#  VÒNG LẶP PHÁT ÂM THANH
# ========================
def audio_playback_loop():
    global is_playing, last_print_time, total_samples_received
    
    print("🔊 Bắt đầu phát âm thanh vào VB-CABLE...")
    print("📍 Audio flow: Điện thoại → Trình duyệt → Python → VB-Cable Input → VB-Cable Output → Ứng dụng")
    
    silent_chunks = 0
    audio_chunks = 0
    
    try:
        with sd.OutputStream(
            samplerate=SAMPLE_RATE,
            channels=CHANNELS,
            dtype=np.float32,
            device=DEVICE_ID,
            blocksize=BUFFER_SIZE,
            latency='low'
        ) as stream:
            
            print("✅ Stream output sẵn sàng")
            print("⏳ Đang chờ audio từ WebSocket...")
            
            while True:
                try:
                    # Lấy audio từ queue
                    audio_data = audio_queue.get(timeout=0.1)
                    
                    if audio_data is not None and len(audio_data) > 0:
                        try:
                            # Tối ưu âm thanh
                            optimized_audio = optimize_audio_quality(audio_data)
                            audio_to_play = optimized_audio.reshape(-1, 1)
                            
                            # Phát âm thanh
                            stream.write(audio_to_play.astype(np.float32))
                            
                            # Thống kê
                            max_amp = np.max(np.abs(audio_to_play))
                            if max_amp > 0.01:
                                audio_chunks += 1
                            else:
                                silent_chunks += 1
                            
                            is_playing = True
                            
                            # Log định kỳ
                            if time.time() - last_print_time >= 3.0:
                                queue_size = audio_queue.qsize()
                                status = "✅ Có âm thanh" if max_amp > 0.1 else "⚠️ Nhỏ" if max_amp > 0.01 else "🔇 Im lặng"
                                print(f"📤 Phát: {len(audio_to_play)} samples, max: {max_amp:.4f} {status} | Queue: {queue_size}")
                                print(f"📊 Thống kê: Audio chunks: {audio_chunks}, Silent: {silent_chunks}")
                                last_print_time = time.time()
                                
                        except Exception as e:
                            print(f"❌ Lỗi phát audio: {e}")
                            is_playing = False
                            
                    else:
                        # Không có dữ liệu, phát silence
                        silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                        stream.write(silence)
                        is_playing = False
                        
                except queue.Empty:
                    # Queue rỗng, phát silence
                    silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                    stream.write(silence)
                    is_playing = False
                    
                except Exception as e:
                    print(f"❌ Lỗi trong playback loop: {e}")
                    time.sleep(0.01)

    except Exception as e:
        print(f"❌ Lỗi stream: {e}")
        import traceback
        traceback.print_exc()

# ========================
#  WEBSOCKET HANDLER
# ========================
async def handle_audio(websocket):
    global total_samples_received, _last_audio_time
    
    client_addr = websocket.remote_address
    print(f"✅ Client kết nối: {client_addr}")
    
    # Biến thống kê
    chunks_received = 0
    silent_chunks = 0
    audio_chunks = 0
    start_time = time.time()
    
    try:
        async for message in websocket:
            try:
                # Nhận audio data
                audio_data = np.frombuffer(message, dtype=np.float32)
                chunks_received += 1
                total_samples_received += len(audio_data)
                _last_audio_time = time.time()
                
                if len(audio_data) == 0:
                    continue
                
                # Xử lý stereo/mono
                if len(audio_data) % 2 == 0:
                    # Có thể là stereo, chuyển thành mono
                    left = audio_data[::2]
                    right = audio_data[1::2]
                    # Chọn kênh có âm lượng lớn hơn
                    left_max = np.max(np.abs(left))
                    right_max = np.max(np.abs(right))
                    audio_data = left if left_max >= right_max else right
                
                # Kiểm tra âm lượng
                max_amplitude = np.max(np.abs(audio_data))
                rms = np.sqrt(np.mean(audio_data**2))
                
                # Phân loại
                if max_amplitude < 0.0001:
                    status = "🔇 RẤT NHỎ"
                    silent_chunks += 1
                elif max_amplitude < 0.001:
                    status = "🔈 NHỎ" 
                    silent_chunks += 1
                elif max_amplitude < 0.01:
                    status = "🔉 TRUNG BÌNH"
                    audio_chunks += 1
                else:
                    status = "🔊 LỚN"
                    audio_chunks += 1
                
                # Log chi tiết cho 10 chunk đầu
                if chunks_received <= 10:
                    print(f"📥 Chunk {chunks_received}: {len(audio_data)} samples, max={max_amplitude:.6f}, RMS={rms:.6f} - {status}")
                
                # Log định kỳ
                if chunks_received % 20 == 0:
                    elapsed = time.time() - start_time
                    print(f"📈 [{chunks_received}] Total: {chunks_received}, Audio: {audio_chunks}, Silent: {silent_chunks}")
                    print(f"⏱️  Tốc độ: {chunks_received/elapsed:.1f} chunks/giây, {total_samples_received/elapsed:.0f} samples/giây")
                
                # Thêm vào queue
                try:
                    audio_queue.put_nowait(audio_data)
                except queue.Full:
                    # Queue đầy, bỏ chunk cũ nhất
                    try:
                        audio_queue.get_nowait()  # Bỏ chunk cũ
                        audio_queue.put_nowait(audio_data)  # Thêm chunk mới
                        print("⚠️ Queue full, bỏ chunk cũ")
                    except:
                        pass
                        
            except Exception as e:
                print(f"❌ Lỗi xử lý audio: {e}")
                import traceback
                traceback.print_exc()
                
    except websockets.exceptions.ConnectionClosed:
        elapsed = time.time() - start_time
        print(f"⚠️ Client ngắt kết nối sau {elapsed:.1f}s")
        print(f"📊 Tổng kết: {chunks_received} chunks, {audio_chunks} có audio, {silent_chunks} im lặng")
        
    except Exception as e:
        print(f"❌ Lỗi WebSocket: {e}")
        
    finally:
        # Dọn dẹp
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
    # Khởi động audio thread
    audio_thread = threading.Thread(target=audio_playback_loop, daemon=True)
    audio_thread.start()
    
    print(f"\n{'='*60}")
    print(f"🎉 WEB SOCKET SERVER ĐÃ SẴN SÀNG!")
    print(f"📍 Địa chỉ: ws://0.0.0.0:8765")
    print(f"🔊 Output: [{DEVICE_ID}] {sd.query_devices(DEVICE_ID)['name']}")
    print(f"💡 Hướng dẫn:")
    print(f"   1. Mở file HTML trên máy tính (sẽ hiển thị QR code)")
    print(f"   2. Dùng điện thoại quét QR code để kết nối")
    print(f"   3. Nói vào điện thoại - âm thanh sẽ chuyển đến VB-Cable")
    print(f"{'='*60}\n")

    # Khởi động WebSocket server
    async with websockets.serve(handle_audio, "0.0.0.0", 8765, ping_interval=20, ping_timeout=10):
        await asyncio.Future()  # Chạy mãi mãi

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n👋 Đã dừng server")
    except Exception as e:
        print(f"\n❌ Lỗi: {e}")
        import traceback
        traceback.print_exc()