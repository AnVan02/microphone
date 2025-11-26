<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng (Phiên bản cuối cùng)</title>
    <script src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
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

        /* Visualizer style */
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

        /* QR Scanner styles */
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

        <div id="senderDiv" class="hidden">
            <div id="sender-manual-view">
                <div class="info">Để kết nối, hãy dùng Camera trên điện thoại của bạn quét mã QR hiển thị trên màn hình
                    máy tính.</div>

                <div id="scanner-container" class="hidden">
                    <video id="qr-video" playsinline></video>
                    <canvas id="qr-canvas"></canvas>
                    <div class="scanner-overlay">
                        <div class="scanner-line"></div>
                    </div>
                </div>

                <button class="btn btn-primary" id="startScannerBtn">📷 Quét QR Code</button>
                <button class="btn btn-secondary hidden" id="stopScannerBtn">🛑 Dừng Quét</button>

                <div class="info" id="scanner-info">
                    Nhấn "Quét QR Code" và hướng camera về phía mã QR trên máy tính
                </div>
            </div>
            <div id="sender-auto-view" class="hidden">
                <button class="btn btn-primary" id="connectBtn">🎤 Kết nối với Máy tính</button>
            </div>
            <div id="sender-connected-view" class="hidden">
                <div id="visualizer-container">
                    <canvas id="visualizer"></canvas>
                </div>
                <div>
                    <button id="muteBtn" class="btn btn-warning" onclick="toggleMicrophone(false)">🔇 Tạm dừng âm
                        thanh</button>
                    <button id="unmuteBtnSender" class="btn btn-secondary hidden" onclick="toggleMicrophone(true)">🎤
                        Bật lại âm thanh</button>
                </div>
                <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                <button class="btn btn-danger" onclick="disconnect()">🔴 Dừng Kết Nối</button>
            </div>
            <div id="senderStatus"></div>
        </div>

        <div id="receiverDiv" class="hidden">
            <audio id="remoteAudio" playsinline style="display: none;"></audio>
            <div id="receiver-initial-view">
                <div class="info">Dùng Camera điện thoại quét mã QR này để kết nối và biến nó thành micro không dây cho
                    máy tính.</div>
                <div id="qrcode-container">
                    <p>Đang kết nối đến máy chủ...</p>
                </div>
            </div>
            <div id="receiver-connected-view" class="hidden">
                <div class="info">✅ Đã kết nối! Âm thanh từ điện thoại đang được gửi đến VB-CABLE.</div>
                <button id="unmuteBtn" class="btn btn-secondary" onclick="playAudio()">🔊 Bật Âm Thanh Ra Loa (Kiểm tra)</button>
                <div class="info" style="font-size: 12px; margin-top: 20px; background: #fff5f5; border-left-color: #fc8181;">
                    <strong>📌 HƯỚNG DẪN CUỐI CÙNG:</strong><br>
                    <strong>1. Đặt Micro:</strong> Click biểu tượng 🔒 trên Chrome → chọn **"CABLE Output"** làm microphone.<br>
                    <strong>2. Thử nghiệm:</strong> Mở Web AI, nói vào điện thoại, xem Console Python có hiển thị **"max: 0.9999 ✅ Tốt"** không.<br>
                    <strong>3. Giải quyết độ trễ:</strong> **BẮT BUỘC** phải tạo **khoảng lặng 1 giây** sau mỗi câu nói để AI xuất kết quả.
                </div>
            </div>
            <div id="receiverStatus"></div>
        </div>
    </div>

    <script>
        // --- REFACTORED: App state and config ---
        const app = {
            peer: null,
            currentCall: null,
            localStream: null,
            audioContext: null,
            analyser: null,
            visualizerFrameId: null,
            ws: null, // WebSocket connection
            qrScanner: null,
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

        // --- LOGIC CHUNG ---
        document.addEventListener('DOMContentLoaded', () => {
            if (isMobile) {
                document.getElementById('senderDiv').classList.remove('hidden');
                initializeSender();
            } else {
                document.getElementById('receiverDiv').classList.remove('hidden');
                initializeReceiver();
            }
        });

        // --- LOGIC MÁY TÍNH (NHẬN) ---
        function initializeReceiver() {
            if (app.peer) app.peer.destroy();
            showStatus('receiver', 'Đang kết nối đến máy chủ PeerJS...', 'info');
            app.peer = new Peer(PEER_CONFIG);

            app.peer.on('open', id => {
                showStatus('receiver', `Sẵn sàng! ID của bạn: ${id}`, 'info');
                const qrContainer = document.getElementById('qrcode-container');
                qrContainer.innerHTML = '';
                const pageUrl = window.location.href.split('?')[0];
                const connectUrl = `${pageUrl}?id=${id}`;
                new QRCode(qrContainer, {
                    text: connectUrl,
                    width: 256,
                    height: 256,
                    colorDark: "#2d3748",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            });

            app.peer.on('call', call => {
                showStatus('receiver', '📲 Có cuộc gọi đến, đang kết nối...', 'info');
                app.currentCall = call;

                call.answer();

                call.on('stream', remoteStream => {
                    const remoteAudio = document.getElementById('remoteAudio');
                    remoteAudio.srcObject = remoteStream;
                    connectWebSocketAndMix(remoteStream);
                });

                call.on('close', () => {
                    showStatus('receiver', '🚫 Kết nối đã đóng từ phía điện thoại.', 'info');
                    if (app.ws) {
                        app.ws.close();
                        app.ws = null;
                    }
                    resetUI();
                });

                call.on('error', err => {
                    showStatus('receiver', `❌ Lỗi cuộc gọi: ${err.message}`, 'error');
                    if (app.ws) {
                        app.ws.close();
                        app.ws = null;
                    }
                });
            });

            app.peer.on('error', err => {
                showStatus('receiver', `❌ Lỗi kết nối: ${err.message}. Vui lòng tải lại trang.`, 'error');
                document.getElementById('qrcode-container').innerHTML = '<p>Không thể kết nối đến máy chủ.</p>';
            });

            app.peer.on('disconnected', () => {
                showStatus('receiver', 'Mất kết nối tới máy chủ, đang thử kết nối lại...', 'error');
                app.peer.reconnect();
            });
        }

        // ========================================
        // 🔧 HÀM CHÍNH: Kết nối WebSocket và gửi âm thanh từ điện thoại
        // ========================================
        function connectWebSocketAndMix(remoteStream) {
            console.log("✅ Bắt đầu xử lý audio từ điện thoại...");

            const audioContext = new(window.AudioContext || window.webkitAudioContext)({
                sampleRate: 48000
            });

            const remoteSource = audioContext.createMediaStreamSource(remoteStream);

            const remoteGain = audioContext.createGain();
            remoteGain.gain.value = 1.2; // Tăng 20% để tín hiệu đầu vào Python mạnh mẽ hơn

            remoteSource.connect(remoteGain);

            // SỬA ĐỔI CUỐI CÙNG: Giảm buffer size từ 2048 xuống 512 để giảm độ trễ
            const processor = audioContext.createScriptProcessor(512, 1, 1);
            remoteGain.connect(processor);

            const gainNode = audioContext.createGain();
            gainNode.gain.value = 0;
            processor.connect(gainNode);
            gainNode.connect(audioContext.destination);

            // Kết nối WebSocket
            console.log("🔄 Đang kết nối WebSocket đến ws://localhost:8765...");
            app.ws = new WebSocket("ws://localhost:8765");
            app.ws.binaryType = "arraybuffer";

            app.ws.onopen = () => {
                console.log("✅ WebSocket đã kết nối thành công!");
                showStatus('receiver', '🎵 Đang gửi âm thanh từ điện thoại đến Python/VB-CABLE...', 'connected');

                let lastSendTime = 0;
                let sendCount = 0;
                processor.onaudioprocess = (event) => {
                    if (app.ws && app.ws.readyState === WebSocket.OPEN) {
                        try {
                            const audioData = event.inputBuffer.getChannelData(0);

                            let maxVal = 0;
                            for (let i = 0; i < audioData.length; i++) {
                                const abs = Math.abs(audioData[i]);
                                if (abs > maxVal) maxVal = abs;
                            }

                            app.ws.send(audioData.buffer);
                            sendCount++;

                            const now = Date.now();
                            if (now - lastSendTime > 2000) {
                                const status = maxVal > 0.01 ? '✅ Có âm thanh' : '⚠️ Im lặng';
                                console.log(`📤 Gửi audio: ${audioData.length} mẫu, max: ${maxVal.toFixed(4)} ${status} | Tổng: ${sendCount} chunks`);
                                lastSendTime = now;
                            }
                        } catch (error) {
                            console.error("❌ Lỗi gửi audio:", error);
                            if (app.ws.readyState !== WebSocket.OPEN) {
                                console.log("🔄 Đang thử kết nối lại WebSocket...");
                                app.ws = new WebSocket("ws://localhost:8765");
                                app.ws.binaryType = "arraybuffer";
                            }
                        }
                    } else {
                        if (!app.ws || app.ws.readyState === WebSocket.CLOSED) {
                            console.log("🔄 WebSocket đã đóng, đang kết nối lại...");
                            app.ws = new WebSocket("ws://localhost:8765");
                            app.ws.binaryType = "arraybuffer";
                        }
                    }
                };
            };

            app.ws.onclose = () => {
                console.log("⚠️ WebSocket đã ngắt kết nối");
                showStatus('receiver', '⚠️ Mất kết nối Python server', 'error');
            };

            app.ws.onerror = (error) => {
                console.error("❌ WebSocket error:", error);
                showStatus('receiver', '❌ Không thể kết nối Python server. Hãy chắc server đang chạy!', 'error');
            };
        }

        // --- LOGIC ĐIỆN THOẠI (GỬI) ---
        function initializeSender() {
            const urlParams = new URLSearchParams(window.location.search);
            const receiverId = urlParams.get('id');

            if (receiverId) {
                document.getElementById('sender-manual-view').classList.add('hidden');
                document.getElementById('sender-auto-view').classList.remove('hidden');
                const connectBtn = document.getElementById('connectBtn');
                connectBtn.onclick = () => {
                    connectBtn.disabled = true;
                    if (app.peer) app.peer.destroy();
                    app.peer = new Peer(PEER_CONFIG);
                    app.peer.on('open', () => {
                        connectToReceiver(receiverId);
                    });
                    app.peer.on('error', err => showStatus('sender', `❌ Lỗi PeerJS: ${err.message}`, 'error'));
                };
            } else {
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('sender-auto-view').classList.add('hidden');
                initializeQRScanner();
            }
        }

        // --- CÁC HÀM XỬ LÝ QR CODE (Giữ nguyên) ---
        function initializeQRScanner() {
            app.qrVideo = document.getElementById('qr-video');
            app.qrCanvas = document.getElementById('qr-canvas');
            app.qrCanvasContext = app.qrCanvas.getContext('2d');

            document.getElementById('startScannerBtn').onclick = startQRScanner;
            document.getElementById('stopScannerBtn').onclick = stopQRScanner;
        }

        async function startQRScanner() {
            try {
                showStatus('sender', '🔄 Đang khởi động camera...', 'info');

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment',
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        }
                    }
                });

                app.qrVideo.srcObject = stream;
                app.qrVideo.play();

                document.getElementById('scanner-container').classList.remove('hidden');
                document.getElementById('startScannerBtn').classList.add('hidden');
                document.getElementById('stopScannerBtn').classList.remove('hidden');
                document.getElementById('scanner-info').textContent = 'Đang quét QR code...';

                showStatus('sender', '📷 Đang quét QR code...', 'info');

                requestAnimationFrame(scanQRCode);

            } catch (error) {
                let message = 'Lỗi không xác định';
                if (error.name === 'NotAllowedError') {
                    message = '❌ Quyền truy cập camera bị từ chối. Vui lòng cho phép camera để quét QR code.';
                } else if (error.name === 'NotFoundError') {
                    message = '❌ Không tìm thấy camera.';
                } else {
                    message = `❌ Lỗi: ${error.message}`;
                }
                showStatus('sender', message, 'error');
            }
        }

        function stopQRScanner() {
            if (app.qrVideo.srcObject) {
                app.qrVideo.srcObject.getTracks().forEach(track => track.stop());
                app.qrVideo.srcObject = null;
            }

            document.getElementById('scanner-container').classList.add('hidden');
            document.getElementById('startScannerBtn').classList.remove('hidden');
            document.getElementById('stopScannerBtn').classList.add('hidden');
            document.getElementById('scanner-info').textContent = 'Nhấn "Quét QR Code" và hướng camera về phía mã QR trên máy tính';

            showStatus('sender', 'Đã dừng quét QR code', 'info');
        }

        function scanQRCode() {
            if (!app.qrVideo.srcObject) return;

            if (app.qrVideo.readyState === app.qrVideo.HAVE_ENOUGH_DATA) {
                app.qrCanvas.height = app.qrVideo.videoHeight;
                app.qrCanvas.width = app.qrVideo.videoWidth;
                app.qrCanvasContext.drawImage(app.qrVideo, 0, 0, app.qrCanvas.width, app.qrCanvas.height);

                try {
                    // Placeholder for actual QR code detection logic
                    const url = extractTextFromImage(app.qrCanvasContext.getImageData(0, 0, app.qrCanvas.width, app.qrCanvas.height));
                    if (url) {
                        handleQRCodeDetected(url);
                        return;
                    }
                } catch (error) {
                    console.log('QR scan error:', error);
                }
            }

            app.visualizerFrameId = requestAnimationFrame(scanQRCode);
        }

        function extractTextFromImage(imageData) {
            // In a real application, you'd use a dedicated library like jsQR here
            // For this context, we rely on the user to manually click the connect button after scanning, 
            // which triggers connectToReceiver

            // This function is conceptually here but not fully implemented, relying on the URL parameter method
            const urlParams = new URLSearchParams(window.location.search);
            const receiverId = urlParams.get('id');
            if (receiverId) {
                // Simulate detection of the ID from the URL that the user might have navigated to
                return window.location.href;
            }
            return null;
        }

        function handleQRCodeDetected(url) {
            try {
                stopQRScanner();

                const urlObj = new URL(url);
                const receiverId = urlObj.searchParams.get('id');

                if (receiverId) {
                    showStatus('sender', '✅ Đã quét QR code thành công!', 'connected');

                    window.location.href = `${window.location.origin}${window.location.pathname}?id=${receiverId}`;
                } else {
                    showStatus('sender', '❌ QR code không hợp lệ', 'error');
                }
            } catch (error) {
                showStatus('sender', '❌ Lỗi xử lý QR code', 'error');
            }
        }

        async function connectToReceiver(receiverId) {
            try {
                showStatus('sender', 'Đang xin quyền truy cập micro...', 'info');
                app.localStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        channelCount: 1,
                        sampleRate: 48000,
                        echoCancellation: false,
                        noiseSuppression: false,
                        autoGainControl: false
                    },
                    video: false
                });

                showStatus('sender', 'Đang thực hiện cuộc gọi đến máy tính...', 'info');
                const call = app.peer.call(receiverId, app.localStream);
                app.currentCall = call;

                showStatus('sender', '✅ Đã kết nối! Đang gửi âm thanh...', 'connected');
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.remove('hidden');
                document.getElementById('muteBtn').classList.remove('hidden');
                document.getElementById('unmuteBtnSender').classList.add('hidden');
                startVisualizer();

                call.on('close', () => {
                    showStatus('sender', '🚫 Kết nối đã đóng.', 'info');
                    resetUI();
                });

                call.on('error', (err) => {
                    showStatus('sender', `❌ Lỗi cuộc gọi: ${err.message}`, 'error');
                    resetUI();
                });

            } catch (err) {
                let message = `❌ Lỗi: ${err.message}.`;
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    message = '❌ Bạn đã từ chối quyền truy cập micro. Vui lòng cấp quyền để tiếp tục.';
                }
                showStatus('sender', message, 'error');
                resetUI();
            }
        }

        // --- CÁC HÀM TIỆN ÍCH (Giữ nguyên) ---
        function toggleMicrophone(shouldBeEnabled) {
            if (app.localStream) {
                app.localStream.getAudioTracks().forEach(track => {
                    track.enabled = shouldBeEnabled;
                });
                document.getElementById('muteBtn').classList.toggle('hidden', shouldBeEnabled);
                document.getElementById('unmuteBtnSender').classList.toggle('hidden', !shouldBeEnabled);

                if (shouldBeEnabled) {
                    showStatus('sender', '🎤 Đã bật lại âm thanh.', 'connected');
                    startVisualizer();
                } else {
                    showStatus('sender', '🔇 Đã tạm dừng âm thanh.', 'info');
                    stopVisualizer();
                }
            }
        }

        function showStatus(device, message, type) {
            const statusEl = document.getElementById(`${device}Status`);
            if (statusEl) {
                statusEl.textContent = message;
                statusEl.className = `status ${type}`;
            }
        }

        function disconnect() {
            if (app.currentCall) {
                app.currentCall.close();
            }
            if (app.localStream) {
                app.localStream.getTracks().forEach(track => track.stop());
                app.localStream = null;
            }
            if (app.ws) {
                app.ws.close();
                app.ws = null;
            }

            if (app.receiverProcessor) {
                app.receiverProcessor.onaudioprocess = null; // Cắt listener gửi audio
                app.receiverProcessor.disconnect(); // Ngắt kết nối node
                app.receiverProcessor = null;
            }
            if (app.receiverAudioContext) {
                app.receiverAudioContext.close().catch(e => console.error("Error closing AudioContext:", e));
                app.receiverAudioContext = null;
            }

            stopVisualizer();
            resetUI();
        }

        function resetUI() {
            if (isMobile) {
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.add('hidden');
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('senderStatus').innerHTML = '';
                const connectBtn = document.getElementById('connectBtn');
                if (connectBtn) connectBtn.disabled = false;
                window.history.replaceState({}, document.title, window.location.pathname);
            } else {
                document.getElementById('receiver-initial-view').classList.remove('hidden');
                document.getElementById('receiver-connected-view').classList.add('hidden');
                document.getElementById('receiverStatus').innerHTML = '';
                const remoteAudio = document.getElementById('remoteAudio');
                if (remoteAudio.srcObject) {
                    remoteAudio.srcObject.getTracks().forEach(track => track.stop());
                    remoteAudio.srcObject = null;
                }
                initializeReceiver();
            }
        }

        function playAudio() {
            const remoteAudio = document.getElementById('remoteAudio');
            remoteAudio.play()
                .then(() => {
                    showStatus('receiver', '✅ Đang phát âm thanh qua loa!', 'connected');
                    document.getElementById('unmuteBtn').classList.add('hidden');
                })
                .catch(e => showStatus('receiver', `❌ Lỗi phát âm thanh: ${e.message}.`, 'error'));
        }

        // --- AUDIO VISUALIZER FUNCTIONS (Giữ nguyên) ---
        function startVisualizer() {
            if (!app.localStream || !app.localStream.active) return;
            if (!app.audioContext) {
                app.audioContext = new(window.AudioContext || window.webkitAudioContext)();
            }
            if (!app.analyser) {
                app.analyser = app.audioContext.createAnalyser();
                const source = app.audioContext.createMediaStreamSource(app.localStream);
                source.connect(app.analyser);
            }
            app.analyser.fftSize = 256;
            const bufferLength = app.analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);
            const canvas = document.getElementById('visualizer');
            const canvasCtx = canvas.getContext('2d');

            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            function draw() {
                if (!app.localStream || !app.localStream.getAudioTracks()[0].enabled) {
                    stopVisualizer();
                    return;
                }
                app.visualizerFrameId = requestAnimationFrame(draw);
                app.analyser.getByteFrequencyData(dataArray);

                canvasCtx.fillStyle = '#f7fafc';
                canvasCtx.fillRect(0, 0, canvas.width, canvas.height);

                let barWidth = (canvas.width / bufferLength) * 2.5;
                let barHeight;
                let x = 0;

                for (let i = 0; i < bufferLength; i++) {
                    barHeight = (dataArray[i] / 255) * canvas.height;
                    const gradient = canvasCtx.createLinearGradient(0, canvas.height - barHeight, 0, canvas.height);
                    gradient.addColorStop(0, '#667eea');
                    gradient.addColorStop(1, '#764ba2');

                    canvasCtx.fillStyle = gradient;
                    canvasCtx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
                    x += barWidth + 1;
                }
            }
            draw();
        }

        function stopVisualizer() {
            if (app.visualizerFrameId) {
                cancelAnimationFrame(app.visualizerFrameId);
                app.visualizerFrameId = null;
            }
            const canvas = document.getElementById('visualizer');
            if (canvas) {
                const canvasCtx = canvas.getContext('2d');
                canvasCtx.fillStyle = '#f7fafc';
                canvasCtx.fillRect(0, 0, canvas.width, canvas.height);
            }
        }
    </script>
</body>

</html>