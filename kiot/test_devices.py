import sounddevice as sd
import numpy as np
import wave

# Cấu hình: đổi DEVICE_ID thành ID của CABLE Input bạn muốn thử (từ output của test_devices)
DEVICE_ID = 10   # <-- đổi thành ID CABLE Input bạn muốn thử
FS = 48000
DURATION = 4.0

print("Bắt đầu ghi... device=", DEVICE_ID)
rec = sd.rec(int(FS * DURATION), samplerate=FS, channels=1, dtype='float32', device=DEVICE_ID)
sd.wait()
print("Ghi xong, lưu file test_record.wav")
data = (rec * 32767).astype('<i2')  # int16 little-endian
with wave.open('test_record.wav','wb') as wf:
    wf.setnchannels(1)
    wf.setsampwidth(2)
    wf.setframerate(FS)
    wf.writeframes(data.tobytes())