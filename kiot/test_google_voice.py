# Tạo file test_audio.py để kiểm tra nhanh
import sounddevice as sd
import numpy as np

def test_audio_devices():
    print("🎧 Danh sách thiết bị:")
    devices = sd.query_devices()
    for i, dev in enumerate(devices):
        print(f"[{i}] {dev['name']} - Input: {dev['max_input_channels']} - Output: {dev['max_output_channels']}")
    
    # Tìm VB-Cable
    cable_input = None
    cable_output = None
    
    for i, dev in enumerate(devices):
        if 'cable input' in dev['name'].lower():
            cable_input = i
        if 'cable output' in dev['name'].lower():
            cable_output = i
    
    print(f"\n🔍 VB-Cable Input: {cable_input}")
    print(f"🔍 VB-Cable Output: {cable_output}")
    
    return cable_input, cable_output

test_audio_devices()