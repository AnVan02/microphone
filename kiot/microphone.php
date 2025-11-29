<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng (Phiên bản cải tiến)</title>
    <!-- THƯ VIỆN CẦN THIẾT -->
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

        /* NEW: Visualizer style */
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

        /* NEW: QR Scanner styles */
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

        <!-- === GIAO DIỆN ĐIỆN THOẠI (GỬI) === -->
        <div id="senderDiv" class="hidden">
            <div id="sender-manual-view">
                <div class="info">Để kết nối, hãy dùng Camera trên điện thoại của bạn quét mã QR hiển thị trên màn hình
                    máy tính.</div>

                <!-- NEW: QR Scanner Section -->
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
                <!-- NEW: Audio Visualizer -->
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

        <!-- === GIAO DIỆN MÁY TÍNH (NHẬN) === -->
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
                <div class="info">Đã kết nối! Âm thanh từ điện thoại đang được nhận.</div>
                <button id="unmuteBtn" class="btn btn-secondary" onclick="playAudio()">🔊 Bật Âm Thanh Ra Loa</button>
                <div class="info" style="font-size: 12px; margin-top: 20px;">Lưu ý: Nút trên chỉ phát âm thanh ra loa để
                    bạn kiểm tra. Để sử dụng làm micro hệ thống, bạn cần định tuyến âm thanh của trình duyệt này vào
                    "Virtual Audio Cable".</div>
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

                // Answer the call without providing any stream (we only want to receive)
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
        // 🔧 HÀM CHÍNH: Kết nối WebSocket và gửi âm thanh
        // ========================================
        function connectWebSocketAndMix(remoteStream) {
            navigator.mediaDevices.getUserMedia({
                audio: {
                    channelCount: 1,
                    sampleRate: 48000,
                    echoCancellation: false,
                    noiseSuppression: false,
                    autoGainControl: false
                }
            }).then(localStream => {
                console.log("✅ Đã lấy được microphone máy tính");

                const audioContext = new(window.AudioContext || window.webkitAudioContext)({
                    sampleRate: 48000
                });

                // Tạo nguồn âm thanh
                const remoteSource = audioContext.createMediaStreamSource(remoteStream);
                const localSource = audioContext.createMediaStreamSource(localStream);

                // Điều chỉnh âm lượng
                const remoteGain = audioContext.createGain();
                remoteGain.gain.value = 1.0; // Âm lượng điện thoại
                const localGain = audioContext.createGain();
                localGain.gain.value = 1.0; // Âm lượng mic máy tính

                remoteSource.connect(remoteGain);
                localSource.connect(localGain);

                // Merge thành stereo: remote (trái) + local (phải)
                const merger = audioContext.createChannelMerger(2);
                remoteGain.connect(merger, 0, 0); // remote vào kênh trái
                localGain.connect(merger, 0, 1); // local vào kênh phải

                // ScriptProcessor để xử lý audio
                const processor = audioContext.createScriptProcessor(4096, 2, 2);
                merger.connect(processor);

                // Mute loa (không phát ra ngoài)
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
                    showStatus('receiver', '🎵 Đang gửi âm thanh đến Python...', 'connected');

                    // Bắt đầu gửi audio
                    processor.onaudioprocess = (event) => {
                        if (app.ws && app.ws.readyState === WebSocket.OPEN) {
                            try {
                                const left = event.inputBuffer.getChannelData(0); // Remote (điện thoại)
                                const right = event.inputBuffer.getChannelData(1); // Local (mic máy tính)

                                // Interleave stereo: LRLRLR...
                                const interleaved = new Float32Array(left.length * 2);
                                for (let i = 0; i < left.length; i++) {
                                    interleaved[i * 2] = left[i];
                                    interleaved[i * 2 + 1] = right[i];
                                }

                                app.ws.send(interleaved.buffer);
                            } catch (error) {
                                console.error("❌ Lỗi gửi audio:", error);
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
                    showStatus('receiver', '❌ Không thể kết nối Python server. Hãy chắc server đang chạy!',
                        'error');
                };

            }).catch(err => {
                console.error("❌ Lỗi truy cập microphone:", err);
                alert("❌ Không truy cập được micro máy tính: " + err.message);
            });
        }

        // --- LOGIC ĐIỆN THOẠI (GỬI) ---
        function initializeSender() {
            const urlParams = new URLSearchParams(window.location.search);
            const receiverId = urlParams.get('id');

            if (receiverId) {
                // Đã có ID từ URL (sau khi quét QR thành công)
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
                // Chưa có ID, hiển thị QR scanner
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('sender-auto-view').classList.add('hidden');

                // Khởi tạo QR Scanner
                initializeQRScanner();
            }
        }

        // NEW: QR Scanner Functions
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

                // Yêu cầu quyền truy cập camera
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

                // Hiển thị scanner UI
                document.getElementById('scanner-container').classList.remove('hidden');
                document.getElementById('startScannerBtn').classList.add('hidden');
                document.getElementById('stopScannerBtn').classList.remove('hidden');
                document.getElementById('scanner-info').textContent = 'Đang quét QR code...';

                showStatus('sender', '📷 Đang quét QR code...', 'info');

                // Bắt đầu quét QR
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
            document.getElementById('scanner-info').textContent =
                'Nhấn "Quét QR Code" và hướng camera về phía mã QR trên máy tính';

            showStatus('sender', 'Đã dừng quét QR code', 'info');
        }

        function scanQRCode() {
            if (!app.qrVideo.srcObject) return;

            if (app.qrVideo.readyState === app.qrVideo.HAVE_ENOUGH_DATA) {
                app.qrCanvas.height = app.qrVideo.videoHeight;
                app.qrCanvas.width = app.qrVideo.videoWidth;
                app.qrCanvasContext.drawImage(app.qrVideo, 0, 0, app.qrCanvas.width, app.qrCanvas.height);

                try {
                    const imageData = app.qrCanvasContext.getImageData(0, 0, app.qrCanvas.width, app.qrCanvas.height);

                    // Simple QR code detection (you might want to use a proper QR library)
                    const url = detectQRCode(imageData);
                    if (url) {
                        handleQRCodeDetected(url);
                        return;
                    }
                } catch (error) {
                    console.log('QR scan error:', error);
                }
            }

            requestAnimationFrame(scanQRCode);
        }

        function detectQRCode(imageData) {
            // This is a simplified QR code detection
            // In a real implementation, you would use a proper QR code library like jsQR
            try {
                // Check if the URL contains the peer ID parameter
                const url = window.location.href;
                if (url.includes('id=')) {
                    return url;
                }

                // Simple pattern matching for QR code URLs
                const text = extractTextFromImage(imageData);
                if (text && text.includes('?id=')) {
                    return text;
                }
            } catch (error) {
                console.log('QR detection error:', error);
            }
            return null;
        }

        function extractTextFromImage(imageData) {
            // Simplified text extraction - in reality you'd use OCR or QR library
            // This is just a placeholder
            return null;
        }

        function handleQRCodeDetected(url) {
            try {
                stopQRScanner();

                const urlObj = new URL(url);
                const receiverId = urlObj.searchParams.get('id');

                if (receiverId) {
                    showStatus('sender', '✅ Đã quét QR code thành công!', 'connected');

                    // Chuyển hướng đến URL với ID
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

        // --- CÁC HÀM TIỆN ÍCH ---
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

        // --- AUDIO VISUALIZER FUNCTIONS ---
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

            // Set canvas size
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