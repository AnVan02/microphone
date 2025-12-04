<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng</title>
    <script src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, sans-serif;
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
            transform: translateY(-2px);
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
            border-color: #fbbf24;
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
            flex-direction: column;
        }

        .qr-info {
            margin-top: 10px;
            font-size: 14px;
            color: #718096;
        }

        .qr-timer {
            margin-top: 5px;
            font-size: 13px;
            color: #e53e3e;
            font-weight: 600;
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

        #visualizer-receiver-container {
            margin-top: 15px;
            padding: 10px;
            background-color: #f7fafc;
            border-radius: 8px;
        }

        #visualizer-receiver {
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

        .token-info {
            font-size: 12px;
            color: #718096;
            margin-top: 10px;
        }

        .connection-stats {
            background: #f0fff4;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 12px;
            text-align: left;
        }

        .timeout-timer {
            background: #fff5f5;
            padding: 8px;
            border-radius: 6px;
            margin: 10px 0;
            font-size: 13px;
            color: #c53030;
            font-weight: 600;
            border-left: 4px solid #fc8181;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🎙️ Mic Qua Mạng</h1>

        <!-- ĐIỆN THOẠI (GỬI) -->
        <div id="senderDiv" class="hidden">
            <div id="sender-manual-view">
                <div class="info">Quét mã QR trên máy tính để kết nối điện thoại làm micro.</div>
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
                    Nhấn "Quét QR Code" và hướng camera về mã QR trên máy tính
                </div>
            </div>

            <div id="sender-auto-view" class="hidden">
                <button class="btn btn-primary" id="connectBtn">Kết nối với Máy tính</button>
                <div class="token-info" id="senderTokenInfo"></div>
            </div>

            <div id="sender-connected-view" class="hidden">
                <div id="timeoutTimerSender" class="timeout-timer hidden">
                    ⏰ Tự động ngắt sau: <span id="timeoutCountdownSender">05:00</span>
                </div>

                <div class="connection-stats" id="senderStats">
                    Đang kết nối...
                </div>

                <div id="visualizer-container">
                    <canvas id="visualizer"></canvas>
                </div>

                <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                <button id="disconnectBtnSender" class="btn btn-danger" onclick="disconnect()">🔴 Ngắt kết nối</button>
            </div>
            <div id="senderStatus"></div>
        </div>

        <!-- MÁY TÍNH (NHẬN) -->
        <div id="receiverDiv" class="hidden">
            <audio id="remoteAudio" playsinline style="display: none;"></audio>

            <div id="receiver-initial-view">
                <div class="info">
                    Dùng Camera điện thoại quét mã QR này để kết nối biến no thành micro không dây cho máy tính
                </div>

                <div id="qrcode-container">
                    <p>Đang tạo mã QR...</p>
                </div>

                <div class="qr-info">
                    <div id="qrTokenInfo">Đang tạo token...</div>
                    <div id="qrTimer" class="qr-timer"></div>
                </div>

                <div class="token-info">
                    Mỗi QR code chỉ sử dụng được một lần, tự động thay đổi mỗi 1 phút
                </div>
            </div>

            <div id="receiver-connected-view" class="hidden">
                <div id="timeoutTimerReceiver" class="timeout-timer hidden">
                    ⏰ Tự động ngắt sau: <span id="timeoutCountdownReceiver">05:00</span>
                </div>

                <div class="connection-stats" id="receiverStats">
                    Đang nhận âm thanh từ điện thoại...
                </div>

                <div id="visualizer-receiver-container">
                    <canvas id="visualizer-receiver"></canvas>
                </div>

                <div class="info">
                    ✅ <strong>Đã kết nối thành công!</strong><br>
                    - Âm thanh đang được gửi đến Python<br>
                    - Phiên kết nối sẽ tự động kết thúc sau 5 phút
                </div>

                <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                <button class="btn btn-danger" onclick="disconnectReceiver()">
                    🔴 Ngắt kết nối
                </button>
            </div>
            <div id="receiverStatus"></div>
        </div>
    </div>

    <script>
        // ========================================
        // 🎯 CẤU HÌNH VÀ BIẾN TOÀN CỤC
        // ========================================
        const app = {
            peer: null,
            currentCall: null,
            localStream: null,
            audioContext: null,
            analyser: null,
            visualizerFrameId: null,
            ws: null,
            currentToken: null,
            sessionId: null,
            heartbeatInterval: null,
            qrRotateInterval: null,
            timeoutTimer: null,
            timeoutSeconds: 300, // 5 phút
            timeoutInterval: null,
            lastAudioActivity: Date.now(),
            audioActivityCheckInterval: null,
            isMobile: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent),
            isSecondPhoneConnected: false
        };

        const PEER_CONFIG = {
            host: '0.peerjs.com',
            port: 443,
            secure: true,
            path: '/'
        };

        // ========================================
        // 🚀 KHỞI TẠO ỨNG DỤNG
        // ========================================
        document.addEventListener('DOMContentLoaded', () => {
            if (app.isMobile) {
                document.getElementById('senderDiv').classList.remove('hidden');
                initializeSender();
            } else {
                document.getElementById('receiverDiv').classList.remove('hidden');
                initializeReceiver();
            }
        });

        // ========================================
        // 💻 MÁY TÍNH - RECEIVER
        // ========================================
        function initializeReceiver() {
            if (app.peer) app.peer.destroy();

            showStatus('receiver', 'Đang kết nối đến máy chủ PeerJS...', 'info');
            app.peer = new Peer(PEER_CONFIG);

            app.peer.on('open', id => {
                showStatus('receiver', `✅ Sẵn sàng! ID: ${id}`, 'info');
                generateNewQRCode();
                startQrRotation();
            });

            app.peer.on('call', call => {
                // Kiểm tra nếu đã có cuộc gọi khác đang hoạt động
                if (app.currentCall && app.currentCall.open) {
                    showStatus('receiver', '⚠️ Máy tính đang bận. Vui lòng thử lại sau.', 'info');
                    console.log('❌ Từ chối cuộc gọi - đã có cuộc gọi khác hoạt động');

                    // Gửi thông báo lỗi về điện thoại thứ 2
                    try {
                        call.close();
                        // Tạo một kênh data để gửi thông báo lỗi
                        const dataConnection = app.peer.connect(call.peer);
                        dataConnection.on('open', () => {
                            dataConnection.send({
                                type: 'ERROR',
                                message: 'Máy tính đã có điện thoại khác kết nối. Vui lòng thử lại sau.'
                            });
                            setTimeout(() => dataConnection.close(), 1000);
                        });
                    } catch (e) {
                        console.error('Lỗi khi từ chối cuộc gọi:', e);
                    }
                    return;
                }

                showStatus('receiver', '📲 Có cuộc gọi đến, đang kết nối...', 'info');
                app.currentCall = call;

                call.answer();

                call.on('stream', remoteStream => {
                    const remoteAudio = document.getElementById('remoteAudio');
                    remoteAudio.srcObject = remoteStream;

                    onReceiverConnectionSuccess();

                    // Bắt đầu kiểm tra hoạt động âm thanh
                    startAudioActivityCheck(remoteStream);

                    // Chờ audio thực sự xuất hiện trước khi bật visualizer
                    waitForAudioActivity(remoteStream, 0.015, 150, 3000).then(active => {
                        if (active) {
                            try {
                                startRemoteVisualizer(remoteStream);
                            } catch (e) {
                                console.error('Remote visualizer error', e);
                            }
                        } else {
                            console.log('Không phát hiện âm thanh trong thời gian chờ');
                        }
                    });

                    connectWebSocketAndMix(remoteStream);

                    // Bắt đầu đếm ngược timeout
                    startTimeoutTimer();
                });

                call.on('close', () => {
                    showStatus('receiver', '🚫 Điện thoại đã ngắt kết nối.', 'info');
                    cleanupReceiverConnection();
                });

                call.on('error', err => {
                    showStatus('receiver', `❌ Lỗi kết nối: ${err.message}`, 'error');
                    cleanupReceiverConnection();
                });
            });

            app.peer.on('error', err => {
                showStatus('receiver', `❌ Lỗi PeerJS: ${err.message}`, 'error');
            });
        }

        function onReceiverConnectionSuccess() {
            document.getElementById('receiver-initial-view').classList.add('hidden');
            document.getElementById('receiver-connected-view').classList.remove('hidden');

            // Hiển thị bộ đếm timeout
            document.getElementById('timeoutTimerReceiver').classList.remove('hidden');

            // Tạo QR mới cho lượt tiếp theo
            generateNewQRCode();

            showStatus('receiver', '✅ Đã kết nối! QR code mới đã được tạo.', 'connected');
        }

        function cleanupReceiverConnection() {
            // Dừng tất cả bộ đếm và interval
            stopTimeoutTimer();
            stopAudioActivityCheck();
            stopQrRotation();

            if (app.ws) {
                app.ws.close();
                app.ws = null;
            }

            if (app.heartbeatInterval) {
                clearInterval(app.heartbeatInterval);
                app.heartbeatInterval = null;
            }

            if (app.currentCall) {
                app.currentCall.close();
                app.currentCall = null;
            }

            try {
                stopRemoteVisualizer();
            } catch (e) {}

            // Tạo QR mới
            setTimeout(() => {
                generateNewQRCode();
                startQrRotation();
                showStatus('receiver', '🔄 Đã tạo QR code mới cho lượt kết nối tiếp theo', 'info');
            }, 1000);

            resetReceiverUI();
        }

        function resetReceiverUI() {
            document.getElementById('receiver-initial-view').classList.remove('hidden');
            document.getElementById('receiver-connected-view').classList.add('hidden');
            document.getElementById('timeoutTimerReceiver').classList.add('hidden');
            document.getElementById('receiverStatus').innerHTML = '';

            const remoteAudio = document.getElementById('remoteAudio');
            if (remoteAudio.srcObject) {
                remoteAudio.srcObject.getTracks().forEach(track => track.stop());
                remoteAudio.srcObject = null;
            }
        }

        function disconnectReceiver() {
            cleanupReceiverConnection();
        }

        // ========================================
        // 🔗 KẾT NỐI WEBSOCKET VÀ GỬI AUDIO
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

                const remoteSource = audioContext.createMediaStreamSource(remoteStream);
                const localSource = audioContext.createMediaStreamSource(localStream);

                const remoteGain = audioContext.createGain();
                remoteGain.gain.value = 1.0;
                const localGain = audioContext.createGain();
                localGain.gain.value = 1.0;

                remoteSource.connect(remoteGain);
                localSource.connect(localGain);

                const merger = audioContext.createChannelMerger(2);
                remoteGain.connect(merger, 0, 0);
                localGain.connect(merger, 0, 1);

                const processor = audioContext.createScriptProcessor(4096, 2, 2);
                merger.connect(processor);

                const gainNode = audioContext.createGain();
                gainNode.gain.value = 0;
                processor.connect(gainNode);
                gainNode.connect(audioContext.destination);

                // Kết nối WebSocket
                console.log("🔄 Đang kết nối WebSocket...");
                app.ws = new WebSocket("ws://localhost:8765");
                app.ws.binaryType = "arraybuffer";

                app.ws.onopen = () => {
                    console.log("✅ WebSocket đã kết nối!");

                    const authMessage = `AUTH:${app.currentToken}`;
                    app.ws.send(authMessage);

                    processor.onaudioprocess = (event) => {
                        if (app.ws && app.ws.readyState === WebSocket.OPEN) {
                            try {
                                const left = event.inputBuffer.getChannelData(0);
                                const right = event.inputBuffer.getChannelData(1);

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

                    app.heartbeatInterval = setInterval(() => {
                        if (app.ws && app.ws.readyState === WebSocket.OPEN) {
                            const heartbeat = JSON.stringify({
                                type: 'HEARTBEAT',
                                timestamp: Date.now()
                            });
                            app.ws.send(heartbeat);
                        }
                    }, 15000);
                };

                app.ws.onmessage = (event) => {
                    try {
                        const message = event.data;

                        if (typeof message === 'string') {
                            const data = JSON.parse(message);

                            switch (data.type) {
                                case 'CONNECTION_ACCEPTED':
                                    showStatus('receiver', '✅ Đã kết nối đến Python server!', 'connected');
                                    app.sessionId = data.session_id;
                                    updateReceiverStats('Đang gửi âm thanh...');
                                    break;

                                case 'CONNECTION_REFUSED':
                                    showStatus('receiver', `❌ ${data.message}`, 'error');
                                    disconnectReceiver();
                                    break;

                                case 'HEARTBEAT_ACK':
                                    updateReceiverStats(`Kết nối ổn định - ${new Date().toLocaleTimeString()}`);
                                    break;
                            }
                        }
                    } catch (error) {
                        // Binary data (audio), không cần xử lý
                    }
                };

                app.ws.onclose = () => {
                    console.log("⚠️ WebSocket đã đóng");
                    if (app.heartbeatInterval) {
                        clearInterval(app.heartbeatInterval);
                    }
                };

                app.ws.onerror = (error) => {
                    console.error("❌ WebSocket error:", error);
                    showStatus('receiver', '❌ Lỗi kết nối Python server', 'error');
                };

            }).catch(err => {
                console.error("❌ Lỗi truy cập microphone:", err);
                showStatus('receiver', '❌ Không thể truy cập micro máy tính', 'error');
            });
        }

        // ========================================
        // 📱 ĐIỆN THOẠI - SENDER
        // ========================================
        function initializeSender() {
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            const peerId = urlParams.get('peer');

            if (token && peerId) {
                app.currentToken = token;
                document.getElementById('sender-manual-view').classList.add('hidden');
                document.getElementById('sender-auto-view').classList.remove('hidden');
                document.getElementById('senderTokenInfo').textContent = `Token: ${token.substring(0, 8)}...`;

                const connectBtn = document.getElementById('connectBtn');
                connectBtn.onclick = () => {
                    connectBtn.disabled = true;
                    connectToReceiver(peerId, token);
                };
            } else {
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('sender-auto-view').classList.add('hidden');
                initializeQRScanner();
            }
        }

        async function connectToReceiver(receiverId, token) {
            try {
                showStatus('sender', 'Đang xin quyền micro...', 'info');
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

                showStatus('sender', 'Đang kết nối đến máy tính...', 'info');

                if (app.peer) app.peer.destroy();
                app.peer = new Peer(PEER_CONFIG);

                // Lắng nghe thông báo lỗi từ máy tính
                app.peer.on('connection', (dataConnection) => {
                    dataConnection.on('data', (data) => {
                        if (data.type === 'ERROR') {
                            showStatus('sender', `❌ ${data.message}`, 'error');
                            setTimeout(() => {
                                window.location.href = window.location.pathname;
                            }, 3000);
                        }
                    });
                });

                app.peer.on('open', () => {
                    const call = app.peer.call(receiverId, app.localStream);
                    app.currentCall = call;

                    onSenderConnectionSuccess();

                    call.on('stream', remoteStream => {
                        // Bắt đầu kiểm tra hoạt động âm thanh
                        startAudioActivityCheck(remoteStream);
                    });

                    call.on('close', () => {
                        showStatus('sender', '🚫 Máy tính đã ngắt kết nối.', 'info');
                        exitAppOnDisconnect();
                    });

                    call.on('error', (err) => {
                        showStatus('sender', `❌ Lỗi kết nối: ${err.message}`, 'error');
                        setTimeout(() => {
                            exitAppOnDisconnect();
                        }, 2000);
                    });
                });

                app.peer.on('error', err => {
                    showStatus('sender', `❌ Lỗi PeerJS: ${err.message}`, 'error');
                    setTimeout(() => {
                        exitAppOnDisconnect();
                    }, 2000);
                });

            } catch (err) {
                let message = `❌ Lỗi: ${err.message}`;
                if (err.name === 'NotAllowedError') {
                    message = '❌ Từ chối quyền micro. Vui lòng cấp quyền để tiếp tục.';
                }
                showStatus('sender', message, 'error');
                setTimeout(() => {
                    exitAppOnDisconnect();
                }, 3000);
            }
        }

        function onSenderConnectionSuccess() {
            document.getElementById('sender-auto-view').classList.add('hidden');
            document.getElementById('sender-connected-view').classList.remove('hidden');
            document.getElementById('timeoutTimerSender').classList.remove('hidden');

            showStatus('sender', '✅ Đã kết nối! Đang gửi âm thanh...', 'connected');
            startVisualizer();
            updateSenderStats('Đang gửi âm thanh...');

            // Bắt đầu đếm ngược timeout
            startTimeoutTimer();
        }

        // ========================================
        // ⏰ TIMEOUT SYSTEM (5 PHÚT)
        // ========================================
        function startTimeoutTimer() {
            stopTimeoutTimer();

            // Reset thời gian timeout
            app.timeoutSeconds = 300; // 5 phút
            app.lastAudioActivity = Date.now();

            // Cập nhật giao diện
            updateTimeoutDisplay();

            // Bắt đầu đếm ngược
            app.timeoutInterval = setInterval(() => {
                app.timeoutSeconds--;
                updateTimeoutDisplay();

                if (app.timeoutSeconds <= 0) {
                    // Tự động ngắt kết nối khi hết giờ
                    showStatus(app.isMobile ? 'sender' : 'receiver',
                        '⏰ Hết thời gian kết nối (5 phút)', 'info');
                    if (app.isMobile) {
                        disconnect();
                    } else {
                        disconnectReceiver();
                    }
                }
            }, 1000);
        }

        function updateTimeoutDisplay() {
            const minutes = Math.floor(app.timeoutSeconds / 60);
            const seconds = app.timeoutSeconds % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (app.isMobile) {
                document.getElementById('timeoutCountdownSender').textContent = timeString;
            } else {
                document.getElementById('timeoutCountdownReceiver').textContent = timeString;
            }
        }

        function resetTimeoutTimer() {
            // Reset về 5 phút khi có hoạt động âm thanh ở phút thứ 59
            if (app.timeoutSeconds <= 60) { // Nếu còn 1 phút hoặc ít hơn
                app.timeoutSeconds = 300; // Reset về 5 phút
                showStatus(app.isMobile ? 'sender' : 'receiver',
                    '⏰ Đã reset thời gian kết nối về 5 phút', 'info');
            }
        }

        function stopTimeoutTimer() {
            if (app.timeoutInterval) {
                clearInterval(app.timeoutInterval);
                app.timeoutInterval = null;
            }
        }

        // ========================================
        // 🎤 KIỂM TRA HOẠT ĐỘNG ÂM THANH
        // ========================================
        function startAudioActivityCheck(stream) {
            stopAudioActivityCheck();

            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
            const source = audioContext.createMediaStreamSource(stream);
            const analyser = audioContext.createAnalyser();
            analyser.fftSize = 512;
            source.connect(analyser);

            const dataArray = new Float32Array(analyser.fftSize);

            app.audioActivityCheckInterval = setInterval(() => {
                analyser.getFloatTimeDomainData(dataArray);

                // Tính RMS
                let sum = 0;
                for (let i = 0; i < dataArray.length; i++) {
                    sum += dataArray[i] * dataArray[i];
                }
                const rms = Math.sqrt(sum / dataArray.length);

                // Nếu có âm thanh (RMS > ngưỡng)
                if (rms > 0.01) {
                    app.lastAudioActivity = Date.now();
                    resetTimeoutTimer();
                }
            }, 1000);
        }

        function stopAudioActivityCheck() {
            if (app.audioActivityCheckInterval) {
                clearInterval(app.audioActivityCheckInterval);
                app.audioActivityCheckInterval = null;
            }
        }

        // ========================================
        // 🔐 QR CODE SYSTEM (Xoay mỗi 1 phút)
        // ========================================
        function generateRandomToken() {
            return Math.random().toString(36).substring(2, 15) +
                Math.random().toString(36).substring(2, 15);
        }

        function generateNewQRCode() {
            if (app.peer && app.peer.id) {
                app.currentToken = generateRandomToken();

                const pageUrl = window.location.href.split('?')[0];
                const connectUrl = `${pageUrl}?token=${app.currentToken}&peer=${app.peer.id}`;

                const qrContainer = document.getElementById('qrcode-container');
                qrContainer.innerHTML = '';

                new QRCode(qrContainer, {
                    text: connectUrl,
                    width: 256,
                    height: 256,
                    colorDark: "#2d3748",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });

                document.getElementById('qrTokenInfo').innerHTML =
                    `🔐 Mã ID: ${app.currentToken.substring(0, 12)}...`;

                // Reset bộ đếm thời gian QR
                startQrTimer();

                console.log("✅ Đã tạo QR Code mới:", app.currentToken);
                return app.currentToken;
            }
            return null;
        }

        function startQrTimer() {
            let seconds = 60;
            const timerElement = document.getElementById('qrTimer');

            function updateTimer() {
                timerElement.textContent = `⏳ QR hết hạn sau: ${seconds}s`;
                seconds--;

                if (seconds < 0) {
                    clearInterval(timerId);
                    timerElement.textContent = '🔄 Đang tạo QR mới...';
                }
            }

            updateTimer();
            const timerId = setInterval(updateTimer, 1000);

            // Lưu timer ID để clear khi cần
            if (app.qrTimerId) clearInterval(app.qrTimerId);
            app.qrTimerId = timerId;
        }

        function startQrRotation() {
            stopQrRotation();

            if (app.peer && app.peer.id) {
                app.qrRotateInterval = setInterval(() => {
                    const initView = document.getElementById('receiver-initial-view');
                    if (initView && !initView.classList.contains('hidden')) {
                        const newToken = generateNewQRCode();
                        console.log('🔁 QR rotated:', newToken);
                    }
                }, 60 * 1000); // 1 phút
            }
        }

        function stopQrRotation() {
            if (app.qrRotateInterval) {
                clearInterval(app.qrRotateInterval);
                app.qrRotateInterval = null;
            }
            if (app.qrTimerId) {
                clearInterval(app.qrTimerId);
                app.qrTimerId = null;
            }
        }

        // ========================================
        // 🚪 THOÁT ỨNG DỤNG KHI NGẮT KẾT NỐI
        // ========================================
        function exitAppOnDisconnect() {
            // Dọn dẹp tài nguyên
            stopTimeoutTimer();
            stopAudioActivityCheck();

            if (app.localStream) {
                app.localStream.getTracks().forEach(track => track.stop());
                app.localStream = null;
            }

            if (app.currentCall) {
                app.currentCall.close();
                app.currentCall = null;
            }

            if (app.peer) {
                app.peer.destroy();
                app.peer = null;
            }

            stopVisualizer();

            showExitScreen();

            setTimeout(() => {
                try {
                    if (window.close) {
                        window.close();
                    }
                } catch (e) {
                    console.log('Không thể đóng tab tự động:', e);
                }
            }, 2000);
        }

        function showExitScreen() {
            document.body.innerHTML = `
                <div style="
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
                    color: #fff;
                    padding: 20px;
                    text-align: center;
                ">
                    <div style="
                        background: rgba(255, 255, 255, 0.95);
                        color: #2d3748;
                        padding: 30px;
                        border-radius: 15px;
                        max-width: 400px;
                        width: 100%;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                    ">
                        <h2 style="color: #4e4376; margin-bottom: 20px;">🔌 Đã ngắt kết nối</h2>
                        
                        <div style="
                            font-size: 80px;
                            margin: 20px 0;
                            color: #667eea;
                            animation: pulse 1.5s infinite;
                        ">
                            ✅
                        </div>
                        
                        <p style="margin-bottom: 15px; line-height: 1.5;">
                            <strong>Phiên kết nối đã kết thúc</strong>
                        </p>
                        
                        <p style="margin-bottom: 25px; color: #718096; font-size: 14px;">
                            Ứng dụng sẽ tự động đóng trong vài giây...
                        </p>
                        
                        <div style="
                            background: #f7fafc;
                            border-radius: 10px;
                            padding: 15px;
                            margin-top: 20px;
                            border-left: 4px solid #38a169;
                        ">
                            <p style="margin: 0; color: #2d3748; font-size: 13px;">
                                <strong>💡 Lưu ý:</strong> Nếu tab không tự động đóng, bạn có thể đóng thủ công.
                            </p>
                        </div>
                        
                        <button id="manualCloseBtn" style="
                            margin-top: 25px;
                            padding: 12px 24px;
                            border-radius: 8px;
                            border: none;
                            background: #667eea;
                            color: white;
                            font-weight: 600;
                            width: 100%;
                            cursor: pointer;
                            transition: all 0.3s;
                        ">
                            📱 Đóng ứng dụng
                        </button>
                    </div>
                </div>
            `;

            const style = document.createElement('style');
            style.textContent = `
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                    100% { transform: scale(1); }
                }
            `;
            document.head.appendChild(style);

            document.getElementById('manualCloseBtn').onclick = () => {
                try {
                    if (window.close && !window.closed) {
                        window.close();
                    } else {
                        document.body.innerHTML = `
                            <div style="
                                min-height: 100vh;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
                                color: #fff;
                                padding: 20px;
                                text-align: center;
                            ">
                                <div style="background: white; color: #2d3748; padding: 30px; border-radius: 15px; max-width: 400px;">
                                    <h3>✅ Hoàn thành!</h3>
                                    <p>Bạn có thể đóng tab này thủ công.</p>
                                    <p style="font-size: 14px; color: #718096; margin-top: 10px;">
                                        (Trình duyệt không cho phép đóng tab tự động)
                                    </p>
                                </div>
                            </div>
                        `;
                    }
                } catch (e) {
                    console.log('Không thể đóng tab:', e);
                }
            };
        }

        function disconnect() {
            if (app.isMobile) {
                showStatus('sender', '🔄 Đang ngắt kết nối và thoát...', 'info');
                exitAppOnDisconnect();
            } else {
                if (app.currentCall) {
                    app.currentCall.close();
                }
                if (app.localStream) {
                    app.localStream.getTracks().forEach(track => track.stop());
                    app.localStream = null;
                }
                if (app.peer) {
                    app.peer.destroy();
                    app.peer = null;
                }
                stopVisualizer();
                resetSenderUI();
            }
        }

        function resetSenderUI() {
            if (!app.isMobile) {
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.add('hidden');
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('senderStatus').innerHTML = '';

                const connectBtn = document.getElementById('connectBtn');
                if (connectBtn) connectBtn.disabled = false;

                stopVisualizer();
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        // ========================================
        // 🎨 UTILITY FUNCTIONS
        // ========================================
        function showStatus(device, message, type) {
            const statusEl = document.getElementById(`${device}Status`);
            if (statusEl) {
                statusEl.textContent = message;
                statusEl.className = `status ${type}`;
            }
            console.log(`[${device.toUpperCase()}] ${message}`);
        }

        function updateSenderStats(message) {
            const statsEl = document.getElementById('senderStats');
            if (statsEl) {
                statsEl.innerHTML = `📊 ${message}`;
            }
        }

        function updateReceiverStats(message) {
            const statsEl = document.getElementById('receiverStats');
            if (statsEl) {
                statsEl.innerHTML = `📊 ${message}`;
            }
        }

        // ========================================
        // 📷 QR SCANNER (Điện thoại)
        // ========================================
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
                if (error.name === 'NotAllowedError') message = '❌ Quyền truy cập camera bị từ chối. Vui lòng cho phép camera để quét QR code.';
                else if (error.name === 'NotFoundError') message = '❌ Không tìm thấy camera.';
                else message = `❌ Lỗi: ${error.message}`;
                showStatus('sender', message, 'error');
            }
        }

        function stopQRScanner() {
            if (app.qrVideo && app.qrVideo.srcObject) {
                app.qrVideo.srcObject.getTracks().forEach(t => t.stop());
                app.qrVideo.srcObject = null;
            }
            document.getElementById('scanner-container').classList.add('hidden');
            document.getElementById('startScannerBtn').classList.remove('hidden');
            document.getElementById('stopScannerBtn').classList.add('hidden');
            document.getElementById('scanner-info').textContent = 'Nhấn "Quét QR Code" và hướng camera về phía mã QR trên máy tính';
            showStatus('sender', 'Đã dừng quét QR code', 'info');
        }

        function scanQRCode() {
            if (!app.qrVideo || !app.qrVideo.srcObject) return;
            if (app.qrVideo.readyState === app.qrVideo.HAVE_ENOUGH_DATA) {
                app.qrCanvas.height = app.qrVideo.videoHeight;
                app.qrCanvas.width = app.qrVideo.videoWidth;
                app.qrCanvasContext.drawImage(app.qrVideo, 0, 0, app.qrCanvas.width, app.qrCanvas.height);
                try {
                    const imageData = app.qrCanvasContext.getImageData(0, 0, app.qrCanvas.width, app.qrCanvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: 'dontInvert'
                    });
                    if (code) {
                        handleQRCodeDetected(code.data);
                        return;
                    }
                } catch (e) {
                    console.log('QR scan error:', e);
                }
            }
            requestAnimationFrame(scanQRCode);
        }

        function handleQRCodeDetected(qrData) {
            try {
                const url = new URL(qrData);
                const token = url.searchParams.get('token');
                const peer = url.searchParams.get('peer');

                if (token && peer) {
                    stopQRScanner();
                    window.location.search = `?token=${token}&peer=${peer}`;
                }
            } catch (e) {
                showStatus('sender', '❌ QR code không hợp lệ', 'error');
            }
        }

        // ========================================
        // 📊 AUDIO VISUALIZER
        // ========================================
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

        function startRemoteVisualizer(remoteStream) {
            if (!remoteStream) return;

            if (!app.remoteAudioContext) {
                app.remoteAudioContext = new(window.AudioContext || window.webkitAudioContext)();
            }

            if (app.remoteAnalyser) {
                try {
                    app.remoteAnalyser.disconnect();
                } catch (e) {}
                app.remoteAnalyser = null;
            }

            app.remoteAnalyser = app.remoteAudioContext.createAnalyser();
            app.remoteAnalyser.fftSize = 256;

            try {
                const source = app.remoteAudioContext.createMediaStreamSource(remoteStream);
                source.connect(app.remoteAnalyser);
            } catch (e) {
                console.error('Không thể tạo MediaStreamSource cho remote visualizer', e);
                return;
            }

            const bufferLength = app.remoteAnalyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);
            const canvas = document.getElementById('visualizer-receiver');
            if (!canvas) return;
            const canvasCtx = canvas.getContext('2d');

            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            function drawRemote() {
                app.remoteVisualizerFrameId = requestAnimationFrame(drawRemote);
                app.remoteAnalyser.getByteFrequencyData(dataArray);

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

            drawRemote();
        }

        function stopRemoteVisualizer() {
            if (app.remoteVisualizerFrameId) {
                cancelAnimationFrame(app.remoteVisualizerFrameId);
                app.remoteVisualizerFrameId = null;
            }
            const canvas = document.getElementById('visualizer-receiver');
            if (canvas) {
                const canvasCtx = canvas.getContext('2d');
                canvasCtx.fillStyle = '#f7fafc';
                canvasCtx.fillRect(0, 0, canvas.width, canvas.height);
            }
            if (app.remoteAnalyser) {
                try {
                    app.remoteAnalyser.disconnect();
                } catch (e) {}
                app.remoteAnalyser = null;
            }
        }

        function waitForAudioActivity(stream, threshold = 0.02, requiredMs = 150, maxWait = 3000) {
            return new Promise((resolve) => {
                if (!stream) return resolve(false);

                const audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                let source;
                try {
                    source = audioCtx.createMediaStreamSource(stream);
                } catch (e) {
                    resolve(false);
                    return;
                }

                const analyser = audioCtx.createAnalyser();
                analyser.fftSize = 512;
                source.connect(analyser);
                const data = new Float32Array(analyser.fftSize);

                let aboveSince = null;
                const startTime = Date.now();

                function check() {
                    analyser.getFloatTimeDomainData(data);
                    let sum = 0;
                    for (let i = 0; i < data.length; i++) sum += data[i] * data[i];
                    const rms = Math.sqrt(sum / data.length);

                    if (rms >= threshold) {
                        if (aboveSince === null) aboveSince = Date.now();
                        else if (Date.now() - aboveSince >= requiredMs) {
                            cleanup();
                            resolve(true);
                            return;
                        }
                    } else {
                        aboveSince = null;
                    }

                    if (Date.now() - startTime > maxWait) {
                        cleanup();
                        resolve(false);
                        return;
                    }

                    rafId = requestAnimationFrame(check);
                }

                let rafId = requestAnimationFrame(check);

                function cleanup() {
                    if (rafId) cancelAnimationFrame(rafId);
                    try {
                        analyser.disconnect();
                    } catch (e) {}
                    try {
                        source.disconnect();
                    } catch (e) {}
                    try {
                        audioCtx.close();
                    } catch (e) {}
                }
            });
        }
    </script>
</body>

</html>