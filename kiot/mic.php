
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mic Điện Thoại → PC (QR)</title>
  <style>
    body { font-family: sans-serif; padding: 20px; background: #f4f6f9; text-align: center; }
    .box { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 340px; margin: auto; }
    h2 { color: #2c3e50; margin-bottom: 15px; }
    button { padding: 14px; margin: 10px 0; font-size: 16px; border: none; border-radius: 8px; cursor: pointer; width: 100%; }
    #start { background: #3498db; color: white; }
    #stop { background: #e74c3c; color: white; }
    button:disabled { background: #95a5a6; }
    #status { padding: 12px; border-radius: 8px; margin: 15px 0; font-weight: bold; }
    .on { background: #d5efdb; color: #27ae60; }
    .off { background: #fadbd8; color: #c0392b; }
    canvas { width: 100%; height: 60px; background: #ecf0f1; border-radius: 8px; margin: 10px 0; }
  </style>
</head>
<body>
  <div class="box">
    <h2>Mic Điện Thoại → PC</h2>
    <button id="start">Bắt đầu gửi mic</button>
    <button id="stop" disabled>Dừng</button>
    <div id="status" class="off">Đang lấy IP từ QR...</div>
    <canvas id="wave"></canvas>
  </div>

  <script>
    window.onbeforeunload = function () {
      if (app.peer) {
        app.peer.destroy();
      }
    };

    let ws = null, stream = null, audioCtx = null, processor = null;
    const status = (msg, ok) => {
      const el = document.getElementById('status');
      el.textContent = msg;
      el.className = ok ? 'on' : 'off';
    };

    // LẤY IP TỪ URL (do QR tạo)
    const params = new URLSearchParams(location.search);
    const wsIP = params.get('ws');
    if (!wsIP) {
      status('Không có IP! Quét QR từ PC!', false);
    } else {
      status(`Sẵn sàng kết nối đến: ${wsIP}`, false);
      document.getElementById('start').onclick = start;
    }

    async function start() {
      ws = new WebSocket(`ws://${wsIP}:8765`);
      ws.binaryType = 'arraybuffer';

      ws.onopen = () => {
        status('Đã kết nối! Bật mic...', true);
        enableMic();
      };
      ws.onclose = () => status('Mất kết nối', false);
      ws.onerror = () => status('Lỗi kết nối', false);
    }


    async function enableMic() {
      try {
        stream = await navigator.mediaDevices.getUserMedia({
          audio: { sampleRate: 48000, channelCount: 1 }
        });
        audioCtx = new AudioContext({ sampleRate: 48000 });
        const source = audioCtx.createMediaStreamSource(stream);
        processor = audioCtx.createScriptProcessor(1024, 1, 1);
        source.connect(processor);
        processor.connect(audioCtx.destination);

        processor.onaudioprocess = e => {
          if (ws?.readyState === WebSocket.OPEN) {
            ws.send(e.inputBuffer.getChannelData(0).buffer);
          }
        };

        document.getElementById('start').disabled = true;
        document.getElementById('stop').disabled = false;
        drawWave();
      } catch (e) {
        status('Lỗi mic: ' + e.message, false);
      }
    }

    document.getElementById('stop').onclick = () => {
      if (processor) processor.disconnect();
      if (stream) stream.getTracks().forEach(t => t.stop());
      if (ws) ws.close();
      document.getElementById('start').disabled = false;
      document.getElementById('stop').disabled = true;
      status('Đã dừng', false);
    };

    function drawWave() {
      const canvas = document.getElementById('wave');
      const ctx = canvas.getContext('2d');
      const analyser = audioCtx.createAnalyser();
      audioCtx.createMediaStreamSource(stream).connect(analyser);
      analyser.fftSize = 256;
      const data = new Uint8Array(analyser.frequencyBinCount);

      const draw = () => {
        analyser.getByteFrequencyData(data);
        ctx.fillStyle = '#ecf0f1';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        const w = canvas.width / data.length * 2.5;
        let x = 0;
        for (let i = 0; i < data.length; i++) {
          const h = data[i] / 255 * canvas.height;
          ctx.fillStyle = '#3498db';
          ctx.fillRect(x, canvas.height - h, w, h);
          x += w + 1;
        }
        if (stream?.active) requestAnimationFrame(draw);
      };
      draw();
    }
  </script>
</body>
</html>