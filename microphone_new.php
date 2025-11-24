<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng (Chỉ Điện Thoại)</title>
    <script src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script> <!-- THÊM JSQR -->
    <!-- CSS giữ nguyên -->
    <style>
    /* ... (giữ nguyên CSS bạn đã có) ... */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: system-ui;
    }

    body {
        background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
        min-height: 100vh;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4a5568;
    }

    .container {
        max-width: 500px;
        width: 100%;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        padding: 25px;
        text-align: center;
    }

    h1 {
        color: #2d3748;
        margin-bottom: 20px;
        font-size: 24px;
    }

    .btn {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin: 10px 0;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-secondary {
        background: #38a169;
        color: white;
    }

    .btn-warning {
        background: #d69e2e;
        color: white;
    }

    .btn-danger {
        background: #e53e3e;
        color: white;
    }

    .btn:disabled {
        background: #cbd5e0;
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn:hover:not(:disabled) {
        opacity: 0.9;
    }

    .hidden {
        display: none;
    }

    .info {
        background: #ebf8ff;
        color: #2a4365;
        padding: 12px;
        border-radius: 8px;
        margin: 15px 0;
        font-size: 14px;
        line-height: 1.6;
        border-left: 4px solid #4299e1;
        text-align: left;
    }

    .status {
        padding: 15px;
        border-radius: 10px;
        margin: 15px 0;
        font-weight: 500;
        border: 2px solid transparent;
        word-wrap: break-word;
    }

    .status.info {
        background: #fffbeb;
        color: #92400e;
        border-color: #fbbF24;
    }

    .status.connected {
        background: #c6f6d5;
        color: #22543d;
        border-color: #48bb78;
    }

    .status.error {
        background: #fed7d7;
        color: #742a2a;
        border-color: #f56565;
    }

    #qrcode-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
        margin-top: 20px;
        min-height: 290px;
    }

    #visualizer-container {
        margin-top: 15px;
        padding: 10px;
        background-color: #f7fafc;
        border-radius: 8px;
    }

    #visualizer {
        width: 100%;
        height: 50px;
        border-radius: 5px;
    }

    #scanner-container {
        margin: 20px 0;
        position: relative;
    }

    #qr-video {
        width: 100%;
        max-width: 300px;
        border-radius: 10px;
        border: 3px solid #667eea;
    }

    #qr-canvas {
        display: none;
    }

    .scanner-overlay {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 300px;
        height: 100%;
        border: 2px solid #38a169;
        border-radius: 10px;
        pointer-events: none;
    }

    .scanner-line {
        position: absolute;
        width: 100%;
        height: 2px;
        background: #38a169;
        animation: scan 2s infinite linear;
    }

    @keyframes scan {
        0% {
            top: 0;
        }

        50% {
            top: 100%;
        }

        100% {
            top: 0;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>🎙️ Mic Qua Mạng</h1>

        <!-- GỬI (ĐIỆN THOẠI) -->
        <div id="senderDiv" class="hidden">
            <div id="sender-manual-view">
                <div class="info">Quét mã QR trên máy tính bằng camera điện thoại.</div>
                <div id="scanner-container" class="hidden">
                    <video id="qr-video" playsinline></video>
                    <canvas id="qr-canvas"></canvas>
                    <div class="scanner-overlay">
                        <div class="scanner-line"></div>
                    </div>
                </div>
                <button class="btn btn-primary" id="startScannerBtn">Quét QR Code</button>
                <button class="btn btn-secondary hidden" id="stopScannerBtn">Dừng Quét</button>
                <div class="info" id="scanner-info">Nhấn nút và hướng camera vào mã QR</div>
            </div>
            <div id="sender-auto-view" class="hidden">
                <button class="btn btn-primary" id="connectBtn">Kết nối Mic</button>
            </div>
            <div id="sender-connected-view" class="hidden">
                <div id="visualizer-container"><canvas id="visualizer"></canvas></div>
                <button id="muteBtn" class="btn btn-warning" onclick="toggleMicrophone(false)">Tạm dừng</button>
                <button id="unmuteBtnSender" class="btn btn-secondary hidden" onclick="toggleMicrophone(true)">Bật
                    lại</button>
                <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                <button class="btn btn-danger" onclick="disconnect()">Dừng Kết Nối</button>
            </div>
            <div id="senderStatus"></div>
        </div>

        <!-- NHẬN (MÁY TÍNH) -->
        <div id="receiverDiv" class="hidden">
            <audio id="remoteAudio" playsinline style="display: none;"></audio>
            <div id="receiver-initial-view">
                <div class="info">Quét mã QR này bằng điện thoại để biến nó thành micro.</div>
                <div id="qrcode-container">
                    <p>Đang kết nối...</p>
                </div>
            </div>
            <div id="receiver-connected-view" class="hidden">
                <div class="info">Đã kết nối! Google sẽ nghe thấy âm thanh từ điện thoại.</div>
            </div>
            <div id="receiverStatus"></div>
        </div>
    </div>

    <script>
    const app = {
        peer: null,
        currentCall: null,
        localStream: null,
        audioContext: null,
        analyser: null,
        visualizerFrameId: null,
        ws: null,
        qrVideo: null,
        qrCanvas: null,
        qrCanvasContext: null
    };
    const PEER_CONFIG = {
        host: '0.peerjs.com',
        port: 443,
        secure: true,
        path: '/'
    };
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

    document.addEventListener('DOMContentLoaded', () => {
        if (isMobile) {
            document.getElementById('senderDiv').classList.remove('hidden');
            initializeSender();
        } else {
            document.getElementById('receiverDiv').classList.remove('hidden');
            initializeReceiver();
        }
    });

    // === NHẬN (PC) ===
    function initializeReceiver() {
        if (app.peer) app.peer.destroy();
        showStatus('receiver', 'Kết nối PeerJS...', 'info');
        app.peer = new Peer(PEER_CONFIG);

        app.peer.on('open', id => {
            showStatus('receiver', `Sẵn sàng! ID: ${id}`, 'info');
            const qrContainer = document.getElementById('qrcode-container');
            qrContainer.innerHTML = '';
            const connectUrl = `${window.location.href.split('?')[0]}?id=${id}`;
            new QRCode(qrContainer, {
                text: connectUrl,
                width: 256,
                height: 256,
                colorDark: "#2d3748",
                colorLight: "#ffffff"
            });
        });

        app.peer.on('call', call => {
            showStatus('receiver', 'Có kết nối...', 'info');
            app.currentCall = call;
            call.answer();
            call.on('stream', remoteStream => {
                connectWebSocketAndSend(remoteStream); // CHỈ GỬI ÂM TỪ ĐIỆN THOẠI
            });
            call.on('close', resetUI);
        });

        app.peer.on('error', err => showStatus('receiver', `Lỗi: ${err.message}`, 'error'));
    }

    // CHỈ GỬI ÂM TỪ ĐIỆN THOẠI
    function connectWebSocketAndSend(remoteStream) {
        const audioContext = new(window.AudioContext || window.webkitAudioContext)({
            sampleRate: 48000
        });
        const source = audioContext.createMediaStreamSource(remoteStream);
        const gainNode = audioContext.createGain();
        gainNode.gain.value = 3.0; // Tăng âm lượng
        source.connect(gainNode);

        const processor = audioContext.createScriptProcessor(4096, 1, 1);
        gainNode.connect(processor);
        const mute = audioContext.createGain();
        mute.gain.value = 0;
        processor.connect(mute);
        mute.connect(audioContext.destination);

        app.ws = new WebSocket("ws://localhost:8765");
        app.ws.binaryType = "arraybuffer";
        app.ws.onopen = () => {
            showStatus('receiver', 'Đang gửi âm thanh từ điện thoại...', 'connected');
            processor.onaudioprocess = e => {
                if (app.ws.readyState === WebSocket.OPEN) {
                    app.ws.send(e.inputBuffer.getChannelData(0).buffer);
                }
            };
        };
        app.ws.onclose = () => showStatus('receiver', 'Mất kết nối', 'error');
    }

    // === GỬI (ĐIỆN THOẠI) ===
    function initializeSender() {
        const urlParams = new URLSearchParams(window.location.search);
        const receiverId = urlParams.get('id');
        if (receiverId) {
            document.getElementById('sender-manual-view').classList.add('hidden');
            document.getElementById('sender-auto-view').classList.remove('hidden');
            document.getElementById('connectBtn').onclick = () => connectToReceiver(receiverId);
        } else {
            document.getElementById('sender-manual-view').classList.remove('hidden');
            initializeQRScanner();
        }
    }

    // QR SCANNER VỚI JSQR
    function initializeQRScanner() {
        app.qrVideo = document.getElementById('qr-video');
        app.qrCanvas = document.getElementById('qr-canvas');
        app.qrCanvasContext = app.qrCanvas.getContext('2d');
        document.getElementById('startScannerBtn').onclick = startQRScanner;
        document.getElementById('stopScannerBtn').onclick = stopQRScanner;
    }

    async function startQRScanner() {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment'
            }
        });
        app.qrVideo.srcObject = stream;
        app.qrVideo.play();
        document.getElementById('scanner-container').classList.remove('hidden');
        document.getElementById('startScannerBtn').classList.add('hidden');
        document.getElementById('stopScannerBtn').classList.remove('hidden');
        requestAnimationFrame(scanQRCode);
    }

    function stopQRScanner() {
        if (app.qrVideo.srcObject) app.qrVideo.srcObject.getTracks().forEach(t => t.stop());
        document.getElementById('scanner-container').classList.add('hidden');
        document.getElementById('startScannerBtn').classList.remove('hidden');
        document.getElementById('stopScannerBtn').classList.add('hidden');
    }

    function scanQRCode() {
        if (app.qrVideo.readyState === app.qrVideo.HAVE_ENOUGH_DATA) {
            app.qrCanvas.height = app.qrVideo.videoHeight;
            app.qrCanvas.width = app.qrVideo.videoWidth;
            app.qrCanvasContext.drawImage(app.qrVideo, 0, 0);
            const imageData = app.qrCanvasContext.getImageData(0, 0, app.qrCanvas.width, app.qrCanvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code && code.data.includes('id=')) {
                handleQRCodeDetected(code.data);
                return;
            }
        }
        requestAnimationFrame(scanQRCode);
    }

    function handleQRCodeDetected(url) {
        stopQRScanner();
        const receiverId = new URL(url).searchParams.get('id');
        window.location.href = `${window.location.origin}${window.location.pathname}?id=${receiverId}`;
    }

    async function connectToReceiver(id) {
        app.localStream = await navigator.mediaDevices.getUserMedia({
            audio: {
                sampleRate: 48000,
                echoCancellation: false,
                noiseSuppression: false
            }
        });
        app.peer = new Peer(PEER_CONFIG);
        app.peer.on('open', () => {
            const call = app.peer.call(id, app.localStream);
            app.currentCall = call;
            showStatus('sender', 'Đã kết nối!', 'connected');
            document.getElementById('sender-auto-view').classList.add('hidden');
            document.getElementById('sender-connected-view').classList.remove('hidden');
            startVisualizer();
        });
    }

    function toggleMicrophone(enable) {
        app.localStream.getAudioTracks()[0].enabled = enable;
        document.getElementById('muteBtn').classList.toggle('hidden', enable);
        document.getElementById('unmuteBtnSender').classList.toggle('hidden', !enable);
        enable ? startVisualizer() : stopVisualizer();
    }

    function showStatus(id, msg, type) {
        const el = document.getElementById(id + 'Status');
        el.textContent = msg;
        el.className = `status ${type}`;
    }

    function disconnect() {
        if (app.currentCall) app.currentCall.close();
        if (app.localStream) app.localStream.getTracks().forEach(t => t.stop());
        if (app.ws) app.ws.close();
        stopVisualizer();
        resetUI();
    }

    function resetUI() {
        if (isMobile) {
            document.getElementById('sender-auto-view').classList.add('hidden');
            document.getElementById('sender-connected-view').classList.add('hidden');
            document.getElementById('sender-manual-view').classList.remove('hidden');
            window.history.replaceState({}, '', window.location.pathname);
        } else {
            document.getElementById('receiver-initial-view').classList.remove('hidden');
            document.getElementById('receiver-connected-view').classList.add('hidden');
            initializeReceiver();
        }
    }

    // VISUALIZER
    function startVisualizer() {
        if (!app.audioContext) app.audioContext = new AudioContext();
        if (!app.analyser) {
            app.analyser = app.audioContext.createAnalyser();
            const src = app.audioContext.createMediaStreamSource(app.localStream);
            src.connect(app.analyser);
        }
        app.analyser.fftSize = 256;
        const data = new Uint8Array(app.analyser.frequencyBinCount);
        const canvas = document.getElementById('visualizer');
        const ctx = canvas.getContext('2d');
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;

        function draw() {
            if (!app.localStream.getAudioTracks()[0].enabled) return stopVisualizer();
            app.visualizerFrameId = requestAnimationFrame(draw);
            app.analyser.getByteFrequencyData(data);
            ctx.fillStyle = '#f7fafc';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            const barWidth = (canvas.width / data.length) * 2.5;
            let x = 0;
            for (let i = 0; i < data.length; i++) {
                const h = (data[i] / 255) * canvas.height;
                const grad = ctx.createLinearGradient(0, canvas.height - h, 0, canvas.height);
                grad.addColorStop(0, '#667eea');
                grad.addColorStop(1, '#764ba2');
                ctx.fillStyle = grad;
                ctx.fillRect(x, canvas.height - h, barWidth, h);
                x += barWidth + 1;
            }
        }
        draw();
    }

    function stopVisualizer() {
        if (app.visualizerFrameId) cancelAnimationFrame(app.visualizerFrameId);
        const canvas = document.getElementById('visualizer');
        if (canvas) canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    }
    </script>
</body>

</html>