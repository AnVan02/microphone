<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng - Kết nối điện thoại làm microphone</title>
    <!-- THƯ VIỆN -->
    <script src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2d3748;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 30px;
            text-align: center;
        }

        h1 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: 700;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            margin: 12px 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: white;
            box-shadow: 0 4px 15px rgba(237, 137, 54, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.4);
        }

        .btn:disabled {
            background: #cbd5e0;
            box-shadow: none;
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .hidden {
            display: none !important;
        }

        .info-box {
            background: #ebf8ff;
            color: #2a4365;
            padding: 16px;
            border-radius: 12px;
            margin: 20px 0;
            font-size: 15px;
            line-height: 1.6;
            border-left: 5px solid #4299e1;
            text-align: left;
        }

        .status-box {
            padding: 18px;
            border-radius: 12px;
            margin: 20px 0;
            font-weight: 600;
            border: 3px solid transparent;
            word-wrap: break-word;
            font-size: 16px;
        }

        .status-info {
            background: #fffbeb;
            color: #92400e;
            border-color: #f59e0b;
        }

        .status-connected {
            background: #f0fff4;
            color: #22543d;
            border-color: #48bb78;
        }

        .status-error {
            background: #fed7d7;
            color: #742a2a;
            border-color: #f56565;
        }

        #qrcode-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px;
            border: 3px dashed #e2e8f0;
            border-radius: 12px;
            margin: 25px 0;
            min-height: 300px;
            background: #f7fafc;
        }

        #visualizer-container {
            margin: 20px 0;
            padding: 15px;
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border-radius: 12px;
            border: 2px solid #e2e8f0;
        }

        #visualizer {
            width: 100%;
            height: 80px;
            border-radius: 8px;
            background: #2d3748;
        }

        #scanner-container {
            margin: 25px 0;
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        #qr-video {
            width: 100%;
            border-radius: 15px;
            display: block;
        }

        #qr-canvas {
            display: none;
        }

        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid #48bb78;
            border-radius: 15px;
            pointer-events: none;
            box-shadow: inset 0 0 0 2px white;
        }

        .scanner-line {
            position: absolute;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #48bb78, transparent);
            animation: scan 2s infinite linear;
            border-radius: 3px;
        }

        @keyframes scan {
            0% {
                top: 0;
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                top: 100%;
                opacity: 0;
            }
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            font-weight: bold;
        }

        .volume-indicator {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-left: 10px;
        }

        .volume-low {
            background: #f56565;
        }

        .volume-medium {
            background: #ed8936;
        }

        .volume-high {
            background: #48bb78;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🎙️ Mic Qua Mạng</h1>
        <p style="color: #718096; margin-bottom: 25px;">Biến điện thoại thành microphone không dây cho máy tính</p>

        <!-- Giao diện điện thoại (gửi âm thanh) -->
        <div id="senderDiv" class="hidden">
            <!-- View quét QR code -->
            <div id="sender-manual-view">
                <div class="info-box">
                    <div class="step-number">1</div>
                    <strong>Quét QR Code từ máy tính</strong><br>
                    Mở file này trên máy tính để hiển thị mã QR, sau đó dùng camera điện thoại quét mã đó.
                </div>

                <div id="scanner-container" class="hidden">
                    <video id="qr-video" playsinline></video>
                    <canvas id="qr-canvas"></canvas>
                    <div class="scanner-overlay">
                        <div class="scanner-line"></div>
                    </div>
                </div>

                <button class="btn btn-primary" id="startScannerBtn">
                    📷 Bật Camera Quét QR
                </button>
                <button class="btn btn-warning hidden" id="stopScannerBtn">
                    🛑 Dừng Quét
                </button>

                <div class="info-box" id="scanner-info">
                    Nhấn "Bật Camera Quét QR" và hướng camera về mã QR trên màn hình máy tính
                </div>
            </div>

            <!-- View kết nối sau khi quét QR -->
            <div id="sender-auto-view" class="hidden">
                <div class="info-box">
                    <div class="step-number">2</div>
                    <strong>Kết nối với máy tính</strong><br>
                    Nhấn nút bên dưới để bắt đầu truyền âm thanh từ điện thoại đến máy tính.
                </div>
                <button class="btn btn-primary" id="connectBtn">
                    🎤 Kết Nối Với Máy Tính
                </button>
            </div>

            <!-- View đã kết nối -->
            <div id="sender-connected-view" class="hidden">
                <div class="info-box status-connected">
                    ✅ <strong>ĐÃ KẾT NỐI THÀNH CÔNG!</strong><br>
                    Âm thanh từ điện thoại đang được truyền đến máy tính.
                </div>

                <!-- Visualizer âm thanh -->
                <div id="visualizer-container">
                    <div style="text-align: center; margin-bottom: 10px; font-weight: 600; color: #4a5568;">
                        🎵 ÂM THANH ĐANG TRUYỀN
                    </div>
                    <canvas id="visualizer"></canvas>
                </div>

                <!-- Điều khiển -->
                <div>
                    <button id="muteBtn" class="btn btn-warning" onclick="toggleMicrophone(false)">
                        🔇 Tạm Dừng Âm Thanh
                    </button>
                    <button id="unmuteBtnSender" class="btn btn-secondary hidden" onclick="toggleMicrophone(true)">
                        🎤 Bật Lại Âm Thanh
                    </button>
                </div>

                <hr style="margin: 20px 0; border: 1px solid #e2e8f0;">

                <button class="btn btn-danger" onclick="disconnect()">
                    🔴 Ngắt Kết Nối
                </button>
            </div>

            <!-- Status -->
            <div id="senderStatus"></div>
        </div>

        <!-- Giao diện máy tính (nhận âm thanh) -->
        <div id="receiverDiv" class="hidden">
            <audio id="remoteAudio" playsinline style="display: none;"></audio>

            <!-- View chờ kết nối -->
            <div id="receiver-initial-view">
                <div class="info-box">
                    <div class="step-number">1</div>
                    <strong>Chia sẻ mã kết nối</strong><br>
                    Dùng camera điện thoại quét mã QR này để kết nối điện thoại làm microphone.
                </div>

                <div id="qrcode-container">
                    <p>🔄 Đang kết nối đến máy chủ...</p>
                </div>

                <div class="info-box">
                    <div class="step-number">2</div>
                    <strong>Hướng dẫn sử dụng</strong><br>
                    • Quét QR code bằng điện thoại<br>
                    • Cho phép truy cập microphone<br>
                    • Nói vào điện thoại - âm thanh sẽ truyền đến máy tính
                </div>
            </div>

            <!-- View đã kết nối -->
            <div id="receiver-connected-view" class="hidden">
                <div class="info-box status-connected">
                    ✅ <strong>ĐÃ KẾT NỐI VỚI ĐIỆN THOẠI!</strong><br>
                    Âm thanh đang được nhận từ điện thoại và chuyển đến VB-Cable.
                </div>

                <button id="unmuteBtn" class="btn btn-secondary" onclick="playAudio()">
                    🔊 Nghe Thử Âm Thanh
                </button>

                <div class="info-box">
                    💡 <strong>Kiểm tra hoạt động:</strong><br>
                    • Mở Sound Settings trên Windows<br>
                    • Vào Recording devices<br>
                    • Tìm "CABLE Output" và xem thanh âm lượng có nhảy không<br>
                    • Nếu có, hệ thống đang hoạt động tốt!
                </div>
            </div>

            <!-- Status -->
            <div id="receiverStatus"></div>
        </div>
    </div>

    <script>
        // =============================================
        // CẤU HÌNH VÀ BIẾN TOÀN CỤC
        // =============================================
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
            qrCanvasContext: null,
            volumeLevel: 0
        };

        const PEER_CONFIG = {
            host: '0.peerjs.com',
            port: 443,
            secure: true,
            path: '/'
        };

        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

        // =============================================
        // KHỞI TẠO ỨNG DỤNG
        // =============================================
        document.addEventListener('DOMContentLoaded', () => {
            console.log('🚀 Ứng dụng Mic Qua Mạng đang khởi động...');

            if (isMobile) {
                document.getElementById('senderDiv').classList.remove('hidden');
                initializeSender();
                console.log('📱 Đang chạy trên điện thoại (Sender)');
            } else {
                document.getElementById('receiverDiv').classList.remove('hidden');
                initializeReceiver();
                console.log('💻 Đang chạy trên máy tính (Receiver)');
            }
        });

        // =============================================
        // MÁY TÍNH - NHẬN ÂM THANH
        // =============================================
        function initializeReceiver() {
            console.log('🔄 Khởi tạo receiver...');

            if (app.peer) app.peer.destroy();
            showStatus('receiver', '🔗 Đang kết nối đến máy chủ PeerJS...', 'info');

            app.peer = new Peer(PEER_CONFIG);

            app.peer.on('open', id => {
                console.log('✅ PeerJS connected với ID:', id);
                showStatus('receiver', `✅ Sẵn sàng! ID: ${id}`, 'connected');

                // Tạo QR code
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

                console.log('📱 QR Code đã được tạo');
            });

            app.peer.on('call', call => {
                console.log('📞 Nhận cuộc gọi từ điện thoại:', call.peer);
                showStatus('receiver', '📲 Điện thoại đang kết nối...', 'info');

                app.currentCall = call;
                call.answer(); // Tự động trả lời

                call.on('stream', remoteStream => {
                    console.log('🎵 Nhận audio stream từ điện thoại');
                    showStatus('receiver', '✅ Đã kết nối với điện thoại!', 'connected');

                    // Hiển thị view đã kết nối
                    document.getElementById('receiver-initial-view').classList.add('hidden');
                    document.getElementById('receiver-connected-view').classList.remove('hidden');

                    // Kết nối WebSocket và xử lý âm thanh
                    connectWebSocketAndProcessAudio(remoteStream);
                });

                call.on('close', () => {
                    console.log('🔌 Cuộc gọi đã đóng');
                    showStatus('receiver', '📱 Điện thoại đã ngắt kết nối', 'info');
                    cleanupConnection();
                });

                call.on('error', err => {
                    console.error('❌ Lỗi cuộc gọi:', err);
                    showStatus('receiver', `❌ Lỗi kết nối: ${err.message}`, 'error');
                    cleanupConnection();
                });
            });

            app.peer.on('error', err => {
                console.error('❌ Lỗi PeerJS:', err);
                showStatus('receiver', `❌ Lỗi kết nối: ${err.message}`, 'error');
                document.getElementById('qrcode-container').innerHTML = '<p>❌ Không thể kết nối đến máy chủ</p>';
            });
        }

        // Kết nối WebSocket và xử lý âm thanh
        function connectWebSocketAndProcessAudio(remoteStream) {
            console.log('🔄 Đang kết nối WebSocket đến Python server...');

            app.ws = new WebSocket("ws://localhost:8765");
            app.ws.binaryType = "arraybuffer";

            app.ws.onopen = () => {
                console.log('✅ Đã kết nối WebSocket thành công!');
                showStatus('receiver', '🎵 Đang truyền âm thanh đến Python...', 'connected');

                // Tạo AudioContext để xử lý stream
                const audioContext = new(window.AudioContext || window.webkitAudioContext)({
                    sampleRate: 48000
                });
                const source = audioContext.createMediaStreamSource(remoteStream);
                const processor = audioContext.createScriptProcessor(256, 1, 1);

                let sampleCount = 0;
                let lastLogTime = Date.now();
                let audioChunks = 0;
                let silentChunks = 0;

                processor.onaudioprocess = (event) => {
                    if (app.ws && app.ws.readyState === WebSocket.OPEN) {
                        try {
                            const inputData = event.inputBuffer.getChannelData(0);
                            const audioBuffer = new Float32Array(inputData);

                            // Kiểm tra âm lượng
                            let maxVolume = 0;
                            let sum = 0;
                            for (let i = 0; i < audioBuffer.length; i++) {
                                const absValue = Math.abs(audioBuffer[i]);
                                if (absValue > maxVolume) maxVolume = absValue;
                                sum += absValue;
                            }
                            const averageVolume = sum / audioBuffer.length;

                            // Thống kê
                            if (maxVolume > 0.01) {
                                audioChunks++;
                            } else {
                                silentChunks++;
                            }

                            // Gửi dữ liệu audio
                            app.ws.send(audioBuffer);

                            // Log định kỳ
                            sampleCount += audioBuffer.length;
                            const currentTime = Date.now();
                            if (currentTime - lastLogTime > 3000) {
                                const totalChunks = audioChunks + silentChunks;
                                console.log(`📤 Đã gửi ${sampleCount} samples, ${audioChunks}/${totalChunks} chunks có âm thanh`);

                                if (audioChunks > 0) {
                                    showStatus('receiver', `✅ Đang nhận âm thanh từ điện thoại (vol: ${(maxVolume * 100).toFixed(1)}%)`, 'connected');
                                } else {
                                    showStatus('receiver', '🔇 Chưa phát hiện âm thanh. Hãy nói vào điện thoại!', 'info');
                                }

                                sampleCount = 0;
                                lastLogTime = currentTime;
                            }

                        } catch (error) {
                            console.error('❌ Lỗi xử lý audio:', error);
                        }
                    }
                };

                source.connect(processor);
                processor.connect(audioContext.destination);

                console.log('✅ Đã bắt đầu xử lý và gửi audio stream');
            };

            app.ws.onclose = () => {
                console.log('⚠️ WebSocket đã đóng');
                showStatus('receiver', '⚠️ Mất kết nối với Python server', 'error');
            };

            app.ws.onerror = (error) => {
                console.error('❌ Lỗi WebSocket:', error);
                showStatus('receiver', '❌ Không thể kết nối đến Python server', 'error');
            };
        }

        // =============================================
        // ĐIỆN THOẠI - GỬI ÂM THANH
        // =============================================
        function initializeSender() {
            console.log('🔄 Khởi tạo sender...');

            const urlParams = new URLSearchParams(window.location.search);
            const receiverId = urlParams.get('id');

            if (receiverId) {
                // Đã có ID từ QR code, hiển thị nút kết nối
                document.getElementById('sender-manual-view').classList.add('hidden');
                document.getElementById('sender-auto-view').classList.remove('hidden');

                const connectBtn = document.getElementById('connectBtn');
                connectBtn.onclick = () => {
                    connectBtn.disabled = true;
                    connectBtn.innerHTML = '🔄 Đang kết nối...';

                    if (app.peer) app.peer.destroy();
                    app.peer = new Peer(PEER_CONFIG);

                    app.peer.on('open', () => {
                        connectToReceiver(receiverId);
                    });

                    app.peer.on('error', err => {
                        console.error('❌ Lỗi PeerJS:', err);
                        showStatus('sender', `❌ Lỗi kết nối: ${err.message}`, 'error');
                        connectBtn.disabled = false;
                        connectBtn.innerHTML = '🎤 Kết Nối Với Máy Tính';
                    });
                };

                console.log('📱 Đã nhận ID receiver từ URL:', receiverId);
            } else {
                // Chưa có ID, hiển thị scanner
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('sender-auto-view').classList.add('hidden');
                initializeQRScanner();
            }
        }

        // Quét QR code
        function initializeQRScanner() {
            console.log('📷 Khởi tạo QR scanner...');

            app.qrVideo = document.getElementById('qr-video');
            app.qrCanvas = document.getElementById('qr-canvas');
            app.qrCanvasContext = app.qrCanvas.getContext('2d');

            document.getElementById('startScannerBtn').onclick = startQRScanner;
            document.getElementById('stopScannerBtn').onclick = stopQRScanner;
        }

        async function startQRScanner() {
            try {
                showStatus('sender', '📷 Đang khởi động camera...', 'info');

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
                await app.qrVideo.play();

                // Hiển thị scanner
                document.getElementById('scanner-container').classList.remove('hidden');
                document.getElementById('startScannerBtn').classList.add('hidden');
                document.getElementById('stopScannerBtn').classList.remove('hidden');
                document.getElementById('scanner-info').textContent = 'Đang quét QR code...';

                showStatus('sender', '🔍 Đang quét QR code...', 'info');

                // Bắt đầu quét
                requestAnimationFrame(scanQRCode);

            } catch (error) {
                console.error('❌ Lỗi camera:', error);
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
            document.getElementById('scanner-info').textContent = 'Nhấn "Bật Camera Quét QR" và hướng camera về mã QR trên máy tính';

            showStatus('sender', '⏹️ Đã dừng quét QR code', 'info');
        }

        function scanQRCode() {
            if (!app.qrVideo.srcObject) return;

            if (app.qrVideo.readyState === app.qrVideo.HAVE_ENOUGH_DATA) {
                app.qrCanvas.height = app.qrVideo.videoHeight;
                app.qrCanvas.width = app.qrVideo.videoWidth;
                app.qrCanvasContext.drawImage(app.qrVideo, 0, 0, app.qrCanvas.width, app.qrCanvas.height);

                try {
                    const imageData = app.qrCanvasContext.getImageData(0, 0, app.qrCanvas.width, app.qrCanvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert",
                    });

                    if (code) {
                        console.log('✅ Đã quét được QR code:', code.data);
                        handleQRCodeDetected(code.data);
                        return;
                    }
                } catch (error) {
                    console.log('⚠️ Lỗi quét QR:', error);
                }
            }

            requestAnimationFrame(scanQRCode);
        }

        function handleQRCodeDetected(url) {
            try {
                console.log('🔗 Xử lý QR code:', url);
                stopQRScanner();

                const urlObj = new URL(url);
                const receiverId = urlObj.searchParams.get('id');

                if (receiverId) {
                    showStatus('sender', '✅ Đã quét QR code thành công!', 'connected');
                    // Chuyển hướng đến URL với ID
                    setTimeout(() => {
                        window.location.href = `${window.location.origin}${window.location.pathname}?id=${receiverId}`;
                    }, 1000);
                } else {
                    showStatus('sender', '❌ QR code không hợp lệ', 'error');
                }
            } catch (error) {
                console.error('❌ Lỗi xử lý QR code:', error);
                showStatus('sender', '❌ Lỗi xử lý QR code', 'error');
            }
        }

        // Kết nối đến receiver
        async function connectToReceiver(receiverId) {
            try {
                showStatus('sender', '🎤 Đang yêu cầu quyền truy cập micro...', 'info');

                // Lấy stream microphone với cấu hình tối ưu
                app.localStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        channelCount: 1,
                        sampleRate: 48000,
                        echoCancellation: false, // Tắt để có chất lượng gốc
                        noiseSuppression: false, // Tắt để có chất lượng gốc  
                        autoGainControl: false, // Tắt - quan trọng để không bị giảm âm lượng
                        // Constraints để tăng volume
                        volume: 1.0,
                        sampleSize: 16,
                        // Tắt các tính năng xử lý của browser
                        googEchoCancellation: false,
                        googAutoGainControl: false,
                        googNoiseSuppression: false,
                        googHighpassFilter: false
                    },
                    video: false
                });

                console.log('✅ Đã lấy được microphone stream');
                console.log('🎛️ Microphone settings:', app.localStream.getAudioTracks()[0].getSettings());

                // Thử điều chỉnh gain nếu được
                const audioTrack = app.localStream.getAudioTracks()[0];
                if (typeof audioTrack.applyConstraints === 'function') {
                    try {
                        await audioTrack.applyConstraints({
                            advanced: [{
                                volume: 1.0
                            }]
                        });
                        console.log('✅ Đã áp dụng volume constraint');
                    } catch (constraintError) {
                        console.log('⚠️ Không thể áp dụng volume constraint:', constraintError);
                    }
                }

                showStatus('sender', '📞 Đang kết nối đến máy tính...', 'info');
                const call = app.peer.call(receiverId, app.localStream);
                app.currentCall = call;

                call.on('stream', () => {
                    showStatus('sender', '✅ Đã kết nối! Đang truyền âm thanh...', 'connected');
                    document.getElementById('sender-auto-view').classList.add('hidden');
                    document.getElementById('sender-connected-view').classList.remove('hidden');
                    document.getElementById('muteBtn').classList.remove('hidden');
                    document.getElementById('unmuteBtnSender').classList.add('hidden');

                    startVisualizer();
                    testMicrophoneVolume(app.localStream);
                });

                call.on('close', () => {
                    console.log('🔌 Cuộc gọi đã đóng');
                    showStatus('sender', '🔌 Kết nối đã đóng', 'info');
                    resetUI();
                });

                call.on('error', (err) => {
                    console.error('❌ Lỗi cuộc gọi:', err);
                    showStatus('sender', `❌ Lỗi kết nối: ${err.message}`, 'error');
                    resetUI();
                });

            } catch (err) {
                console.error('❌ Lỗi kết nối:', err);
                let message = `❌ Lỗi: ${err.message}`;
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    message = '❌ Bị từ chối quyền truy cập micro. Vui lòng cấp quyền và thử lại.';
                } else if (err.name === 'NotFoundError') {
                    message = '❌ Không tìm thấy microphone.';
                } else if (err.name === 'NotReadableError') {
                    message = '❌ Không thể truy cập microphone. Có thể đang bị ứng dụng khác sử dụng.';
                }
                showStatus('sender', message, 'error');
                resetUI();
            }
        }

        // Kiểm tra âm lượng microphone
        function testMicrophoneVolume(stream) {
            const audioContext = new AudioContext();
            const source = audioContext.createMediaStreamSource(stream);
            const analyser = audioContext.createAnalyser();
            analyser.fftSize = 256;
            source.connect(analyser);

            const dataArray = new Uint8Array(analyser.frequencyBinCount);
            let maxVolume = 0;

            function checkVolume() {
                if (!stream.active) return;

                analyser.getByteFrequencyData(dataArray);

                let sum = 0;
                for (let i = 0; i < dataArray.length; i++) {
                    sum += dataArray[i];
                }
                const average = sum / dataArray.length;
                const volumePercent = (average / 256) * 100;

                // Cập nhật max volume
                if (volumePercent > maxVolume) {
                    maxVolume = volumePercent;
                }

                // Cập nhật volume cho visualizer
                app.volumeLevel = volumePercent;

                // Phân loại và hiển thị
                let status, volumeClass;
                if (volumePercent < 1) {
                    status = "🔇 RẤT NHỎ";
                    volumeClass = "volume-low";
                } else if (volumePercent < 5) {
                    status = "🔈 NHỎ";
                    volumeClass = "volume-low";
                } else if (volumePercent < 20) {
                    status = "🔉 TRUNG BÌNH";
                    volumeClass = "volume-medium";
                } else {
                    status = "🔊 TỐT";
                    volumeClass = "volume-high";
                }

                console.log(`📢 Microphone: ${volumePercent.toFixed(1)}% - ${status}`);

                // Cảnh báo nếu âm lượng quá thấp
                if (volumePercent < 2 && maxVolume < 5) {
                    showStatus('sender', `🔇 Âm lượng rất thấp: ${volumePercent.toFixed(1)}% - HÃY NÓI TO HƠN!`, 'error');
                } else if (volumePercent < 5) {
                    showStatus('sender', `🔈 Âm lượng: ${volumePercent.toFixed(1)}% - Nói to hơn để chất lượng tốt hơn`, 'info');
                } else {
                    showStatus('sender', `✅ Âm lượng tốt: ${volumePercent.toFixed(1)}%`, 'connected');
                }

                setTimeout(checkVolume, 1000);
            }

            checkVolume();
        }

        // =============================================
        // HÀM TIỆN ÍCH
        // =============================================
        function toggleMicrophone(shouldBeEnabled) {
            if (app.localStream) {
                app.localStream.getAudioTracks().forEach(track => {
                    track.enabled = shouldBeEnabled;
                });
                document.getElementById('muteBtn').classList.toggle('hidden', shouldBeEnabled);
                document.getElementById('unmuteBtnSender').classList.toggle('hidden', !shouldBeEnabled);

                if (shouldBeEnabled) {
                    showStatus('sender', '🎤 Đã bật microphone', 'connected');
                    startVisualizer();
                } else {
                    showStatus('sender', '🔇 Đã tắt microphone', 'info');
                    stopVisualizer();
                }
            }
        }

        function showStatus(device, message, type) {
            const statusEl = document.getElementById(`${device}Status`);
            if (statusEl) {
                statusEl.textContent = message;
                statusEl.className = `status-box status-${type}`;
            }
            console.log(`📢 ${device.toUpperCase()} Status: ${message}`);
        }

        function disconnect() {
            console.log('🔌 Ngắt kết nối...');

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

        function cleanupConnection() {
            if (app.ws) {
                app.ws.close();
                app.ws = null;
            }
            resetUI();
        }

        function resetUI() {
            if (isMobile) {
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.add('hidden');
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('senderStatus').innerHTML = '';

                const connectBtn = document.getElementById('connectBtn');
                if (connectBtn) {
                    connectBtn.disabled = false;
                    connectBtn.innerHTML = '🎤 Kết Nối Với Máy Tính';
                }

                // Xóa ID khỏi URL
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

                // Khởi tạo lại để có ID mới
                initializeReceiver();
            }
        }

        function playAudio() {
            const remoteAudio = document.getElementById('remoteAudio');
            remoteAudio.play()
                .then(() => {
                    showStatus('receiver', '🔊 Đang phát âm thanh qua loa', 'connected');
                    document.getElementById('unmuteBtn').classList.add('hidden');
                })
                .catch(e => {
                    console.error('❌ Lỗi phát âm thanh:', e);
                    showStatus('receiver', `❌ Không thể phát âm thanh: ${e.message}`, 'error');
                });
        }

        // =============================================
        // VISUALIZER ÂM THANH
        // =============================================
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

            // Cập nhật kích thước canvas
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            function draw() {
                if (!app.localStream || !app.localStream.getAudioTracks()[0].enabled) {
                    stopVisualizer();
                    return;
                }

                app.visualizerFrameId = requestAnimationFrame(draw);
                app.analyser.getByteFrequencyData(dataArray);

                // Xóa canvas
                canvasCtx.fillStyle = '#1a202c';
                canvasCtx.fillRect(0, 0, canvas.width, canvas.height);

                // Vẽ visualizer
                const barWidth = (canvas.width / bufferLength) * 2.5;
                let x = 0;

                for (let i = 0; i < bufferLength; i++) {
                    const barHeight = (dataArray[i] / 255) * canvas.height;

                    // Màu sắc dựa trên cường độ
                    const hue = i / bufferLength * 360;
                    const saturation = 80 + (dataArray[i] / 255) * 20;
                    const lightness = 40 + (dataArray[i] / 255) * 30;

                    canvasCtx.fillStyle = `hsl(${hue}, ${saturation}%, ${lightness}%)`;
                    canvasCtx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);

                    x += barWidth + 1;
                }

                // Vẽ volume text
                canvasCtx.fillStyle = '#ffffff';
                canvasCtx.font = '12px Arial';
                canvasCtx.fillText(`Volume: ${app.volumeLevel.toFixed(1)}%`, 10, 20);
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
                canvasCtx.fillStyle = '#1a202c';
                canvasCtx.fillRect(0, 0, canvas.width, canvas.height);
            }
        }

        // =============================================
        // XỬ LÝ SỰ KIỆN TRANG ĐÓNG
        // =============================================
        window.addEventListener('beforeunload', () => {
            console.log('🧹 Dọn dẹp trước khi đóng trang...');
            disconnect();
        });
    </script>
</body>

</html>