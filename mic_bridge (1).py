import asyncio
import websockets
import sounddevice as sd
import numpy as np

# ======== CẤU HÌNH ========
# Tên thiết bị output mong muốn (bạn có thể đổi nếu muốn)
PREFERRED_OUTPUTS = [
    "CABLE Input",      # VB-Audio Virtual Cable
    "Speakers",         # Loa thật
    "Headphones"
]
SAMPLE_RATE = 48000
CHANNELS = 1

# ======== CHỌN THIẾT BỊ ========
def get_output_device():
    devices = sd.query_devices()
    print("\n🎧 Thiết bị audio có sẵn:\n")
    for i, d in enumerate(devices):
        print(f"[{i}] {d['name']} — Input: {d['max_input_channels']} | Output: {d['max_output_channels']}")

    # Chọn VB-Cable hoặc Loa
    for name in PREFERRED_OUTPUTS:
        for i, d in enumerate(devices):
            if name.lower() in d['name'].lower() and d['max_output_channels'] > 0:
                print(f"\n✅ Đã chọn thiết bị output: [{i}] {d['name']}\n")
                return i

    # Nếu không tìm thấy thì hỏi người dùng chọn
    print("⚠️ Không tìm thấy thiết bị phù hợp, vui lòng chọn thủ công:")
    while True:
        idx = int(input("Nhập số thiết bị output: "))
        if 0 <= idx < len(devices):
            return idx

# ======== PHÁT ÂM THANH ========
async def handle_audio(websocket):
    print("✅ Đã kết nối với trình duyệt!\n")
    try:
        async for message in websocket:
            audio_data = np.frombuffer(message, dtype=np.float32)
            if len(audio_data) > 0:
                sd.play(audio_data, samplerate=SAMPLE_RATE, blocking=False)
    except websockets.ConnectionClosed:
        print("❌ Kết nối WebSocket đã đóng.")
    except Exception as e:
        print("⚠️ Lỗi khi xử lý âm thanh:", e)

# ======== CHẠY SERVER ========
async def main():
    OUTPUT_DEVICE = get_output_device()
    sd.default.device = (None, OUTPUT_DEVICE)

    import socket
    ip = socket.gethostbyname(socket.gethostname())
    print(f"🎙️ WebSocket server đang chạy tại ws://{ip}:8765")
    print("⏳ Đang chờ kết nối từ điện thoại...")

    async with websockets.serve(handle_audio, ip, 8765, max_size=None, ping_timeout=None):
        await asyncio.Future()  # giữ server chạy

if __name__ == "__main__":
    asyncio.run(main())
