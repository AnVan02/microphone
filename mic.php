<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng (WebRTC + WebSocket)</title>
    <!-- Thư viện cần thiết -->
    <script src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h1 {
            margin-bottom: 1.5rem;
            color: #1a73e8;
        }

        .status {
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .status.wait {
            background: #fff3cd;
            color: #856404;
        }

        .status.ok {
            background: #d4edda;
            color: #155724;
        }

        .status.err {
            background: #f8d7da;
            color: #721c24;
        }

        button {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background: #1557b0;
        }

        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        #qrcode {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        video {
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            border: 2px solid #ddd;
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1>🎙️ Mic Bridge System</h1>

        <!-- GIAO DIỆN PC (RECEIVER) -->
        <div id="pc-view" class="hidden">
            <h3>🖥️ Chế độ Máy Tính (Receiver)</h3>
            <p>Quét mã bên dưới bằng điện thoại:</p>
            <div id="qrcode"></div>
            <div id="pc-status" class="status wait">Đang khởi tạo ID...</div>
            <p style="font-size: 0.8em; color: gray">Trạng thái WebSocket: <span id="ws-status">Chưa kết nối</span></p>
            <button id="activeAudioBtn" class="hidden">🔊 Kích hoạt Âm thanh</button>
        </div>

        <!-- GIAO DIỆN ĐIỆN THOẠI (SENDER) -->
        <div id="mobile-view" class="hidden">
            <h3>📱 Chế độ Điện Thoại (Mic)</h3>
            <div id="mobile-status" class="status wait">Chọn chế độ kết nối...</div>

            <!-- Scanner -->
            <div id="scanner-wrapper">
                <button id="btnScan" onclick="startScan()">📷 Quét mã QR</button>
                <div id="video-container" class="hidden">
                    <video id="qr-video" playsinline></video>
                    <button onclick="stopScan()" style="background:#dc3545; margin-top:5px">Hủy quét</button>
                </div>
            </div>
            <!-- Calling UI -->
            <div id="call-ui" class="hidden">
                <div class="status ok">Đang truyền âm thanh...</div>
                <canvas id="visualizer" style="width:100%; height:50px; background:#eee; border-radius:4px;"></canvas>
                <button onclick="window.location.reload()" style="background:#dc3545">🔴 Ngắt kết nối</button>
            </div>
        </div>
    </div>

    <script>
        // --- CẤU HÌNH ---
        const isMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);
        let peer = null;
        let ws = null;
        let audioContext = null;

        // --- KHỞI TẠO GIAO DIỆN ---
        if (isMobile) {
            document.getElementById('mobile-view').classList.remove('hidden');
            // Kiểm tra xem có ID trong URL không (kết nối tự động sau khi quét)
            const params = new URLSearchParams(window.location.search);
            if (params.has('id')) {
                connectToPC(params.get('id'));
            }
        } else {
            document.getElementById('pc-view').classList.remove('hidden');
            initPC();
        }

        // ==========================================
        // LOGIC CHO MÁY TÍNH (RECEIVER)
        // ==========================================
        function initPC() {
            // Tạo Peer
            peer = new Peer();

            peer.on('open', (id) => {
                const statusEl = document.getElementById('pc-status');
                statusEl.innerText = "Sẵn sàng kết nối!";
                statusEl.classList.replace('wait', 'ok');

                // Tạo QR Code chứa URL kèm ID
                const url = `${window.location.origin}${window.location.pathname}?id=${id}`;
                new QRCode(document.getElementById("qrcode"), {
                    text: url,
                    width: 200,
                    height: 200
                });
            });


            peer.on('call', (call) => {
                document.getElementById('pc-status').innerText = "📲 Điện thoại đang kết nối...";

                // Trả lời cuộc gọi (nhận stream)
                call.answer();

                call.on('stream', (remoteStream) => {
                    document.getElementById('pc-status').innerText = "✅ Đã nhận tín hiệu Mic!";

                    // Hiển thị nút kích hoạt Audio Context (bắt buộc trên trình duyệt)
                    const btn = document.getElementById('activeAudioBtn');
                    btn.classList.remove('hidden');
                    btn.onclick = () => {
                        setupAudioBridge(remoteStream);
                        btn.classList.add('hidden');
                    };
                });
            });
        }

        function setupAudioBridge(stream) {
            // 1. Kết nối WebSocket tới Python (Localhost vì chạy trên PC)
            // Lưu ý: Python chạy cổng 8765
            ws = new WebSocket("ws://127.0.0.1:8765");
            ws.binaryType = 'arraybuffer';

            const wsStatus = document.getElementById('ws-status');

            ws.onopen = () => {
                wsStatus.innerText = "🟢 Đã nối tới Python Bridge";
                wsStatus.style.color = "green";
                processAudio(stream);
            };

            ws.onerror = () => {
                wsStatus.innerText = "🔴 Lỗi nối Python (Chưa chạy mic_bridge.py?)";
                wsStatus.style.color = "red";
            };
        }

        function processAudio(stream) {
            // 2. Xử lý âm thanh Web Audio API
            audioContext = new(window.AudioContext || window.webkitAudioContext)({
                sampleRate: 48000
            });
            const source = audioContext.createMediaStreamSource(stream);

            // ScriptProcessorNode để lấy raw data (bufferSize 512)
            const processor = audioContext.createScriptProcessor(512, 1, 1);

            source.connect(processor);
            processor.connect(audioContext.destination); // Cần connect destination để timer chạy, dù volume = 0

            // Mute output trên trình duyệt (để tránh nghe lại tiếng mình, chỉ gửi xuống VB-Cable)
            // const gainNode = audioContext.createGain();
            // gainNode.gain.value = 0; 
            // processor.connect(gainNode);
            // gainNode.connect(audioContext.destination);

            processor.onaudioprocess = (e) => {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    const inputData = e.inputBuffer.getChannelData(0);
                    // Gửi trực tiếp Float32Array sang Python
                    ws.send(inputData.buffer);
                }
            };
        }

        // ==========================================
        // LOGIC CHO ĐIỆN THOẠI (SENDER)
        // ==========================================

        // --- Quét QR ---
        let videoStream;

        function startScan() {
            document.getElementById('btnScan').classList.add('hidden');
            const video = document.getElementById('qr-video');
            document.getElementById('video-container').classList.remove('hidden');

            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment"
                    }
                })
                .then(stream => {
                    videoStream = stream;
                    video.srcObject = stream;
                    video.play();
                    requestAnimationFrame(tick);
                });
        }

        function stopScan() {
            if (videoStream) videoStream.getTracks().forEach(track => track.stop());
            document.getElementById('video-container').classList.add('hidden');
            document.getElementById('btnScan').classList.remove('hidden');
        }

        function tick() {
            const video = document.getElementById('qr-video');
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                const code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code) {
                    stopScan();
                    // Redirect tới URL đã quét (kèm ID)
                    window.location.href = code.data;
                }
            }
            if (!document.getElementById('video-container').classList.contains('hidden')) {
                requestAnimationFrame(tick);
            }
        }

        // --- Kết nối WebRTC ---
        function connectToPC(peerId) {
            document.getElementById('scanner-wrapper').classList.add('hidden');
            document.getElementById('mobile-status').innerText = "Đang kết nối tới PC...";

            peer = new Peer();
            peer.on('open', (id) => {
                navigator.mediaDevices.getUserMedia({
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        autoGainControl: true
                    },
                    video: false
                }).then((stream) => {
                    const call = peer.call(peerId, stream);

                    call.on('open', () => {
                        document.getElementById('mobile-status').classList.add('hidden');
                        document.getElementById('call-ui').classList.remove('hidden');
                        drawVisualizer(stream);
                    });

                    call.on('close', () => alert("Kết nối bị ngắt"));
                }).catch(err => {
                    alert("Lỗi truy cập Mic: " + err.message);
                });
            });
        }

        function drawVisualizer(stream) {
            const ctx = new(window.AudioContext || window.webkitAudioContext)();
            const src = ctx.createMediaStreamSource(stream);
            const analyser = ctx.createAnalyser();
            src.connect(analyser);
            const data = new Uint8Array(analyser.frequencyBinCount);
            const canvas = document.getElementById('visualizer');
            const cCtx = canvas.getContext('2d');

            function loop() {
                analyser.getByteFrequencyData(data);
                cCtx.fillStyle = 'white';
                cCtx.fillRect(0, 0, canvas.width, canvas.height);
                cCtx.fillStyle = '#1a73e8';
                let w = canvas.width / data.length * 2.5;
                for (let i = 0; i < data.length; i++) {
                    cCtx.fillRect(i * w, canvas.height - data[i] / 5, w - 1, data[i] / 5);
                }
                requestAnimationFrame(loop);
            }
            loop();
        }
    </script>
</body>

</html>