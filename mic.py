import numpy as np
import soundcard as sc
import soundfile as sf
import sounddevice as sd
import pyaudio

# Initialize PyAudio
p = pyaudio.PyAudio()

buffer_size = 512

# set devices to in- and output from
mic_input_index = 1
input_sample_rate = int(sd.query_devices(mic_input_index)['default_samplerate'])
v_cable_output_index = 14        

# Open an audio stream for input
input_stream = p.open(format=pyaudio.paInt16,
                    channels=2,
                    rate=input_sample_rate,
                    input=True,
                    frames_per_buffer=buffer_size,
                    input_device_index=mic_input_index)

# Open an audio stream for output (virtual audio device)
output_stream = p.open(format=pyaudio.paInt16,
                    channels=8,
                    rate=input_sample_rate,
                    output=True,
                    output_device_index=v_cable_output_index)

while True:
    try:
        # Read audio data
        data = input_stream.read(buffer_size)
        audio_array = np.frombuffer(data, dtype=np.int16)
        processed_audio = audio_array

        # Play the processed audio to the virtual audio device
        output_stream.write(processed_audio.tobytes())

    except KeyboardInterrupt:
        break