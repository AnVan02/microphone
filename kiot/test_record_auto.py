import sounddevice as sd
import numpy as np
import wave
import sys

# Tự động tìm thiết bị VB-Cable có khả năng input (max_input_channels > 0)
def find_input_vb_cable():
    for i, d in enumerate(sd.query_devices()):
        name = d['name'].lower()
        if 'cable' in name and d['max_input_channels'] > 0:
            return i
    return None

dev = find_input_vb_cable()
if dev is None:
    print("Không tìm thấy thiết bị VB-Cable có input (>0). Hãy chạy test_devices.py và chọn ID thủ công.")
    sys.exit(1)

info = sd.query_devices(dev)
print(f"Sử dụng device {dev}: {info['name']} (Inputs: {info['max_input_channels']}, Outputs: {info['max_output_channels']})")

FS = 48000
import os
try:
    # CLI arg has priority
    if len(sys.argv) > 1:
        DURATION = float(sys.argv[1])
    else:
        DURATION = float(os.getenv('RECORD_SECONDS', '4.0'))
except Exception:
    DURATION = 4.0

print(f"Bắt đầu ghi {DURATION}s...\n")
rec = sd.rec(int(FS * DURATION), samplerate=FS, channels=1, dtype='float32', device=dev)
sd.wait()
print("Ghi xong, lưu file test_record_auto.wav")

# Chuyển về int16 mono
data = rec
if data.ndim > 1:
    data = data[:, 0]
int_data = (data * 32767).astype('<i2')
with wave.open('test_record_auto.wav', 'wb') as wf:
    wf.setnchannels(1)
    wf.setsampwidth(2)
    wf.setframerate(FS)
    wf.writeframes(int_data.tobytes())

print("Saved: test_record_auto.wav")
