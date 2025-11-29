<?php
// microphone_fixed.php
// Mã HTML+JS để chạy trên điện thoại / trình duyệt: lấy micro và gửi audio (Float32 @48kHz mono) qua WebSocket
// Lưu ý: để đơn giản, trang này trả về HTML. Bạn có thể đặt file .php hoặc .html trên server.

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Micro → WebSocket</title>
    <style>
        body {
            font-family: system-ui, Segoe UI, Roboto, Segoe, Arial;
            padding: 16px;
            max-width: 700px;
            margin: auto;
        }

        button {
            padding: 10px 14px;
            margin: 6px;
        }

        #log {
            white-space: pre-wrap;
            background: #f7f7f7;
            padding: 10px;
            border-radius: 6px;
            height: 220px;
            overflow: auto;
        }
    </style>
</head>

<body>
    <h2>Gửi micro qua WebSocket (48kHz Float32)</h2>
    <p>Kết nối tới server WebSocket: <code>ws://[IP-PC]:8765</code></p>
    <input id="wsUrl" value="ws://192.168.1.100:8765" style="width:100%;" />
    <div style="margin-top:8px;">
        <button id="btnStart">Bắt đầu</button>
        <button id="btnStop" disabled>Stop</button>
        <button id="btnTest">Gửi test tone 1s</button>
    </div>
    <div id="log"></div>

    <script>
        const logEl = document.getElementById('log');

        function log(...args) {
            logEl.textContent += args.join(' ') + '\\n';
            logEl.scrollTop = logEl.scrollHeight;
        }

        let ws = null;
        let audioCtx = null;
        let processor = null;
        let streamNode = null;
        let micStream = null;
        let running = false;

        function floatTo32Buffer(float32Array) {
            // Return ArrayBuffer of float32 little-endian
            return float32Array.buffer;
        }

        async function start() {
            const url = document.getElementById('wsUrl').value.trim();
            if (!url) {
                alert('Nhập URL WebSocket');
                return;
            }

            try {
                ws = new WebSocket(url);
                ws.binaryType = 'arraybuffer';
                ws.onopen = () => log('WebSocket opened to', url);
                ws.onclose = () => log('WebSocket closed');
                ws.onerror = (e) => log('WebSocket error', e);
            } catch (e) {
                log('WS error', e);
                return;
            }

            // tạo audio context với sampleRate 48000 nếu trình duyệt cho phép
            audioCtx = new(window.AudioContext || window.webkitAudioContext)({
                sampleRate: 48000
            });

            try {
                micStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        channelCount: 1,
                        sampleRate: 48000
                    }
                });
            } catch (e) {
                log('Lỗi getUserMedia:', e);
                return;
            }

            // sử dụng ScriptProcessorNode để đơn giản (buffer size 1024)
            const bufferSize = 1024;
            processor = audioCtx.createScriptProcessor(bufferSize, 1, 1);

            const source = audioCtx.createMediaStreamSource(micStream);
            source.connect(processor);
            processor.connect(audioCtx.destination); // optional so node runs on some browsers

            processor.onaudioprocess = (ev) => {
                try {
                    const input = ev.inputBuffer.getChannelData(0); // Float32Array
                    // copy vì buffer có thể bị reuse
                    const copy = new Float32Array(input.length);
                    copy.set(input);
                    if (ws && ws.readyState === WebSocket.OPEN) {
                        ws.send(floatTo32Buffer(copy));
                    }
                } catch (err) {
                    console.error('onaudioprocess error', err);
                }
            };

            running = true;
            document.getElementById('btnStart').disabled = true;
            document.getElementById('btnStop').disabled = false;
            log('Bắt đầu gửi micro (48kHz Float32)');
        }

        function stop() {
            if (processor) {
                processor.disconnect();
                processor.onaudioprocess = null;
                processor = null;
            }
            if (micStream) {
                const tracks = micStream.getTracks();
                tracks.forEach(t => t.stop());
                micStream = null;
            }
            if (ws) {
                try {
                    ws.close();
                } catch (e) {}
                ws = null;
            }
            if (audioCtx) {
                try {
                    audioCtx.close();
                } catch (e) {}
                audioCtx = null;
            }
            running = false;
            document.getElementById('btnStart').disabled = false;
            document.getElementById('btnStop').disabled = true;
            log('Stopped');
        }

        function sendTestTone() {
            if (!ws || ws.readyState !== WebSocket.OPEN) {
                log('WS chưa mở');
                return;
            }
            // tạo 1s tone 440Hz @48kHz
            const sr = 48000;
            const len = sr;
            const arr = new Float32Array(len);
            for (let i = 0; i < len; i++) {
                arr[i] = Math.sin(2 * Math.PI * 440 * (i / sr)) * 0.6;
            }
            ws.send(arr.buffer);
            log('Sent test tone 1s');
        }

        document.getElementById('btnStart').addEventListener('click', start);
        document.getElementById('btnStop').addEventListener('click', stop);
        document.getElementById('btnTest').addEventListener('click', sendTestTone);
    </script>
</body>

</html>