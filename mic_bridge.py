# websocket_audio_server.py (Mô hình tham khảo từ các dự án mã nguồn mở)
import asyncio
import websockets
import numpy as np
import sounddevice as sd
import threading
import time
import os
import queue
import socket 

#=============

# ========================
#  CẤU HÌNH CỐT LÕI (LOW LATENCY)
# ========================
SAMPLE_RATE = 48000
CHANNELS = 1
# Buffer Size: Giá trị tối ưu cho độ trễ thấp (thường là 256, 512, 1024)
# Giảm buffer size giúp giảm độ trễ, nhưng tăng nguy cơ lỗi âm thanh
BUFFER_SIZE = int(os.getenv('MIC_BRIDGE_BUFFER', '256')) 

# Queue: Bộ đệm giữa luồng nhận (WebSocket) và luồng phát (sounddevice)
# Giúp hệ thống ổn định khi có dao động về tốc độ mạng
audio_queue = queue.Queue(maxsize=10) 

# ========================
#  KHUẾCH ĐẠI TÍN HIỆU (Tham khảo)
# ========================
def optimize_audio_quality(audio_data):
    """
    Hàm chuẩn hóa và khuếch đại tín hiệu audio (Dynamic Gain).
    Quan trọng để AI nhận được giọng nói rõ ràng, bất kể âm lượng đầu vào.
    """
    audio_data = audio_data.astype(np.float32)
    new_max = np.max(np.abs(audio_data))
    
    # Mục tiêu tối đa an toàn (gần 1.0)
    target_max = 0.9999 

    # Nếu âm thanh quá nhỏ, tăng cường khuếch đại
    if new_max > 0.01 and new_max < target_max:
        # Áp dụng khuếch đại và đảm bảo không bị méo tiếng (Clipping)
        audio_data = np.clip(audio_data * (target_max / new_max), -1.0, 1.0)
    
    return audio_data

# ========================
#  1. LUỒNG PHÁT LẠI (Playback Thread)
# ========================

def audio_playback_loop(device_id):
    """
    Chạy trong một luồng riêng biệt để liên tục lấy dữ liệu từ queue và phát ra thiết bị.
    Sử dụng stream mode 'low latency' và blocksize nhỏ.
    """
    try:
        with sd.OutputStream(
            samplerate=SAMPLE_RATE,
            channels=CHANNELS,
            dtype=np.float32,
            device=device_id,
            blocksize=BUFFER_SIZE, # Sử dụng BUFFER_SIZE đã định nghĩa
            latency='low'          # Đảm bảo độ trễ thấp nhất
        ) as stream:
            print(f"✅ Luồng phát audio đã sẵn sàng (Device: {sd.query_devices(device_id)['name']})")
            while True:
                try:
                    # Lấy dữ liệu từ queue (timeout ngắn để stream không bị chặn lâu)
                    audio_data = audio_queue.get(timeout=0.1)
                    
                    if audio_data is not None and len(audio_data) > 0:
                        optimized = optimize_audio_quality(audio_data)
                        
                        # Chia và phát từng phần (chunk) để đảm bảo độ trễ thấp
                        chunk_size = BUFFER_SIZE
                        for i in range(0, len(optimized), chunk_size):
                            chunk = optimized[i:i+chunk_size]
                            stream.write(chunk.reshape(-1, 1).astype(np.float32))
                            
                    # Tối ưu hóa: Nếu queue rỗng, phát âm thanh im lặng (zero padding)
                    else:
                        silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                        stream.write(silence)
                        
                except queue.Empty:
                    # Nếu queue rỗng, phát âm thanh im lặng để giữ stream hoạt động
                    silence = np.zeros((BUFFER_SIZE, CHANNELS), dtype=np.float32)
                    stream.write(silence)
                except Exception as e:
                    # Lỗi trong quá trình phát
                    print(f"❌ Lỗi phát audio: {e}")
                    time.sleep(0.01)
    except Exception as e:
        print(f"❌ Lỗi khởi tạo stream: {e}")

# ========================
#  2. WEBSOCKET HANDLER (Nhận dữ liệu)
# ========================
async def handle_audio(websocket):
    """
    Hàm xử lý kết nối WebSocket, nhận dữ liệu audio và đưa vào queue.
    """
    print(f"✅ Client kết nối: {websocket.remote_address}")
    try:
        async for message in websocket:
            # Chuyển đổi dữ liệu binary nhận được thành numpy array (float32)
            audio_data = np.frombuffer(message, dtype=np.float32)
            
            # Xử lý: Lấy kênh mono (cần thiết nếu đầu vào là stereo)
            if len(audio_data) > 0 and len(audio_data) % 2 == 0:
                audio_data = audio_data[::2] 
            # Đưa dữ liệu vào queue để luồng phát xử lý
            try:
                audio_queue.put_nowait(audio_data)
            except queue.Full:
                # Nếu queue đầy (xảy ra khi luồng nhận nhanh hơn luồng phát), 
                # bỏ qua hoặc xóa phần tử cũ nhất (bỏ qua là giải pháp đơn giản hơn)
                pass 
                
    except websockets.exceptions.ConnectionClosed:
        # ⚠️ Client ngắt kết nối
        print(f"⚠️ Client ngắt kết nối: {websocket.remote_address}")
    except Exception as e:
        print(f"❌ Lỗi WebSocket: {e}")
    finally:
        # Dọn dẹp queue khi kết nối đóng
        while not audio_queue.empty():
            try:
                audio_queue.get_nowait()
            except:
                break
        
        # THÊM DÒNG NÀY ĐỂ XÁC NHẬN PHIÊN ĐÃ KẾT THÚC
        print(f"🧹 Đã dọn dẹp phiên kết nối từ {websocket.remote_address}. Server vẫn đang lắng nghe...") 
        # --------------------------------------------------------------------------------------------

# ========================
#  3. MAIN SERVER VÀ KHỞI TẠO
# ========================

def find_vb_cable():
    """Tìm ID của CABLE Input"""
    devices = sd.query_devices()
    for i, d in enumerate(devices):
        if 'cable input' in d['name'].lower() and d['max_output_channels'] > 0:
            return i
    return None

async def main():
    device_id = find_vb_cable()
    if device_id is None:
        print("❌ Lỗi: Không tìm thấy VB-CABLE. Vui lòng cài đặt VB-CABLE.")
        return

    # Khởi tạo luồng phát audio riêng biệt
    audio_thread = threading.Thread(target=audio_playback_loop, args=(device_id,), daemon=True)
    audio_thread.start()

    print(f"\nWebSocket Server đang chạy tại: ws://0.0.0.0:8765")
    async with websockets.serve(handle_audio, "0.0.0.0", 9001):
        await asyncio.Future() # Giữ server chạy vô thời hạn

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\nĐã dừng server")
    
    except Exception as e:
        print(f"Lỗi: {e}")

    
    