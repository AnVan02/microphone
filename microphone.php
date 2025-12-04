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

        .btn-danger {
            background: #e53e3e;
            color: white;
        }

        .btn-warning {
            background: #d69e2e;
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

        .warning {
            background: #fffbeb;
            color: #92400e;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
            font-size: 14px;
            line-height: 1.6;
            border-left: 4px solid #fbbF24;
            text-align: left;
        }

        .error-message {
            background: #fed7d7;
            color: #742a2a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #f56565;
            text-align: left;
            line-height: 1.6;
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
            flex-direction: column;
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

        .error-screen {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .error-screen-content {
            background: rgba(255, 255, 255, 0.98);
            color: #2d3748;
            padding: 30px;
            border-radius: 15px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .exit-screen {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .exit-screen-content {
            background: rgba(255, 255, 255, 0.95);
            color: #2d3748;
            padding: 30px;
            border-radius: 15px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .pulse {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
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
                <button class="btn btn-danger hidden" id="stopScannerBtn">🛑 Dừng Quét</button>

                <div class="info" id="scanner-info">
                    Nhấn "Quét QR Code" và hướng camera về mã QR trên máy tính
                </div>
            </div>

            <div id="sender-auto-view" class="hidden">
                <div class="info" id="auto-connect-info">
                    Đã nhận thông tin kết nối từ QR code
                </div>
                <button class="btn btn-primary" id="connectBtn">🔗 Kết nối với Máy tính</button>
                <div class="token-info" id="senderTokenInfo"></div>
            </div>

            <div id="sender-connected-view" class="hidden">
                <div class="connection-stats" id="senderStats">
                    Đang kết nối đến máy tính...
                </div>

                <div id="visualizer-container">
                    <canvas id="visualizer"></canvas>
                </div>

                <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                <button id="disconnectBtnSender" class="btn btn-danger" onclick="disconnectPhone()">
                    🔴 Ngắt kết nối
                </button>
            </div>
            <div id="senderStatus"></div>
        </div>

        <!-- MÁY TÍNH (NHẬN) -->
        <div id="receiverDiv" class="hidden">
            <audio id="remoteAudio" playsinline style="display: none;"></audio>

            <div id="receiver-initial-view">
                <div class="info">
                    <strong>Hướng dẫn:</strong> Dùng Camera điện thoại quét mã QR này để kết nối và biến điện thoại thành micro không dây cho máy tính.
                </div>

                <div id="qrcode-container">
                    <p>Đang tạo mã QR...</p>
                </div>

                <div class="warning">
                    <strong>⚠️ Lưu ý quan trọng:</strong> Hệ thống chỉ cho phép <strong>1 người kết nối tại 1 thời điểm</strong>. Người dùng thứ 2 sẽ nhận thông báo "Máy tính đang bận".
                </div>

                <div class="token-info" id="receiverTokenInfo">
                    Mỗi QR code chỉ sử dụng được một lần
                </div>
            </div>

            <div id="receiver-connected-view" class="hidden">
                <div class="connection-stats" id="receiverStats">
                    Đang nhận âm thanh từ điện thoại...
                </div>

                <div id="visualizer-receiver-container">
                    <canvas id="visualizer-receiver"></canvas>
                </div>

                <div class="info">
                    ✅ <strong>Đã kết nối thành công!</strong><br>
                    - Âm thanh đang được gửi đến Python server<br>
                    - Đang xử lý Speech-to-Text (chuyển giọng nói thành văn bản)
                </div>

                <div class="warning" style="font-size: 12px; margin-top: 20px;">
                    💡 <strong>Lưu ý:</strong> Để sử dụng làm micro hệ thống, bạn cần:<br>
                    1. Cài đặt VB-CABLE (Virtual Audio Cable)<br>
                    2. Chọn "CABLE Input" làm thiết bị phát âm thanh<br>
                    3. Chọn "CABLE Output" làm microphone trong ứng dụng
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
            remoteAnalyser: null,
            remoteVisualizerFrameId: null,
            ws: null,
            currentToken: null,
            heartbeatInterval: null,
            qrRotateInterval: null,
            connectionTimeout: null,
            isMobile: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent)
        };

        const PEER_CONFIG = {
            host: '0.peerjs.com',
            port: 443,
            secure: true,
            path: '/',
            config: {
                iceServers: [{
                        urls: 'stun:stun.l.google.com:19302'
                    },
                    {
                        urls: 'stun:stun1.l.google.com:19302'
                    }
                ]
            },
            debug: 0
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
            cleanupReceiver();

            showStatus('receiver', 'Đang kết nối đến máy chủ PeerJS...', 'info');
            app.peer = new Peer(PEER_CONFIG);

            app.peer.on('open', id => {
                console.log(`✅ PeerJS connected. ID: ${id}`);
                showStatus('receiver', `✅ Sẵn sàng! ID: ${id.substring(0, 8)}...`, 'info');
                generateNewQRCode();
                startQrRotation();
            });

            app.peer.on('call', call => {
                // ⛔ KIỂM TRA NẾU ĐÃ CÓ KẾT NỐI - TỪ CHỐI NGƯỜI THỨ 2
                if (app.currentCall && app.currentCall.open) {
                    console.log('⛔ Từ chối cuộc gọi - máy tính đang bận');
                    showStatus('receiver', '⚠️ Máy tính đang bận. Đã từ chối kết nối mới.', 'info');

                    try {
                        call.close();
                    } catch (e) {}
                    return;
                }

                console.log('📞 Nhận cuộc gọi từ:', call.peer);
                showStatus('receiver', '📲 Có cuộc gọi đến, đang kết nối...', 'info');

                app.currentCall = call;

                // Thiết lập timeout cho kết nối
                app.connectionTimeout = setTimeout(() => {
                    if (app.currentCall === call && !call.open) {
                        console.log('⏰ Timeout kết nối');
                        call.close();
                    }
                }, 10000);

                call.answer();

                call.on('stream', remoteStream => {
                    clearTimeout(app.connectionTimeout);

                    console.log('✅ Nhận được audio stream từ điện thoại');
                    const remoteAudio = document.getElementById('remoteAudio');
                    remoteAudio.srcObject = remoteStream;

                    onReceiverConnectionSuccess();
                    startRemoteVisualizer(remoteStream);
                    connectWebSocketAndMix(remoteStream);
                });

                call.on('close', () => {
                    console.log('🔌 Cuộc gọi đã đóng');
                    clearTimeout(app.connectionTimeout);
                    showStatus('receiver', '🚫 Điện thoại đã ngắt kết nối.', 'info');
                    cleanupReceiverConnection();
                });

                call.on('error', err => {
                    console.error('❌ Lỗi cuộc gọi:', err);
                    clearTimeout(app.connectionTimeout);
                    showStatus('receiver', `❌ Lỗi kết nối: ${err.message}`, 'error');
                    cleanupReceiverConnection();
                });
            });

            app.peer.on('error', err => {
                console.error('❌ Lỗi PeerJS:', err);
                showStatus('receiver', `❌ Lỗi PeerJS: ${err.message}`, 'error');
            });

            app.peer.on('disconnected', () => {
                console.log('🔌 PeerJS disconnected');
                showStatus('receiver', '⚠️ Mất kết nối với máy chủ PeerJS', 'info');
            });
        }

        function onReceiverConnectionSuccess() {
            document.getElementById('receiver-initial-view').classList.add('hidden');
            document.getElementById('receiver-connected-view').classList.remove('hidden');

            stopQrRotation();

            setTimeout(() => {
                generateNewQRCode();
            }, 1000);

            showStatus('receiver', '✅ Đã kết nối thành công với điện thoại!', 'connected');
            updateReceiverStats('Đang nhận âm thanh từ điện thoại...');
        }

        function cleanupReceiverConnection() {
            if (app.ws) {
                try {
                    app.ws.close();
                } catch (e) {}
                app.ws = null;
            }

            if (app.heartbeatInterval) {
                clearInterval(app.heartbeatInterval);
                app.heartbeatInterval = null;
            }

            stopRemoteVisualizer();
            app.currentCall = null;
            resetReceiverUI();

            setTimeout(() => {
                generateNewQRCode();
                startQrRotation();
                showStatus('receiver', '🔄 Đã tạo QR code mới cho lượt kết nối tiếp theo', 'info');
            }, 500);
        }

        function cleanupReceiver() {
            if (app.peer) {
                app.peer.destroy();
                app.peer = null;
            }
            cleanupReceiverConnection();
        }

        function resetReceiverUI() {
            document.getElementById('receiver-initial-view').classList.remove('hidden');
            document.getElementById('receiver-connected-view').classList.add('hidden');
            document.getElementById('receiverStatus').innerHTML = '';

            const remoteAudio = document.getElementById('remoteAudio');
            if (remoteAudio.srcObject) {
                remoteAudio.srcObject.getTracks().forEach(track => track.stop());
                remoteAudio.srcObject = null;
            }
        }

        function disconnectReceiver() {
            if (app.currentCall) {
                app.currentCall.close();
            }
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

                console.log("🔄 Đang kết nối WebSocket...");
                // ⭐⭐ PORT 8766 (ĐÃ SỬA TỪ 8765) ⭐⭐
                app.ws = new WebSocket("ws://localhost:8766");
                app.ws.binaryType = "arraybuffer";

                app.ws.onopen = () => {
                    console.log("✅ WebSocket đã kết nối!");
                    updateReceiverStats('✅ Đã kết nối đến Python server');

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
                            app.ws.send(JSON.stringify({
                                type: 'HEARTBEAT',
                                timestamp: Date.now()
                            }));
                        }
                    }, 15000);
                };

                app.ws.onmessage = (event) => {
                    try {
                        if (typeof event.data === 'string') {
                            const data = JSON.parse(event.data);
                            if (data.type === 'STT_RESULT') {
                                updateReceiverStats(`🗣️ STT: ${data.text}`);
                            }
                        }
                    } catch (error) {}
                };

                app.ws.onclose = () => {
                    console.log("⚠️ WebSocket đã đóng");
                    if (app.heartbeatInterval) {
                        clearInterval(app.heartbeatInterval);
                        app.heartbeatInterval = null;
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
                document.getElementById('auto-connect-info').textContent = 'Đã nhận thông tin kết nối từ QR code. Nhấn nút bên dưới để bắt đầu.';

                const connectBtn = document.getElementById('connectBtn');
                connectBtn.onclick = () => {
                    connectBtn.disabled = true;
                    connectBtn.textContent = 'Đang kết nối...';
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
                showStatus('sender', '🎤 Đang xin quyền micro...', 'info');

                app.localStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        channelCount: 1,
                        sampleRate: 48000,
                        echoCancellation: true,
                        noiseSuppression: true,
                        autoGainControl: true
                    },
                    video: false
                });

                showStatus('sender', '📡 Đang kết nối đến máy tính...', 'info');

                if (app.peer) app.peer.destroy();
                app.peer = new Peer(PEER_CONFIG);

                app.peer.on('open', () => {
                    console.log(`📞 Gọi đến receiver: ${receiverId}`);
                    const call = app.peer.call(receiverId, app.localStream, {
                        metadata: {
                            token: token
                        }
                    });

                    app.currentCall = call;
                    onSenderConnectionSuccess();

                    call.on('stream', remoteStream => {
                        console.log('✅ Nhận được stream từ receiver');
                    });

                    call.on('close', () => {
                        console.log('🔌 Cuộc gọi bị đóng');
                        showStatus('sender', '🚫 Máy tính đã ngắt kết nối.', 'info');
                        exitAppOnDisconnect();
                    });

                    call.on('error', (err) => {
                        console.error('❌ Lỗi cuộc gọi:', err);

                        let errorMessage = '❌ KẾT NỐI KHÔNG THÀNH CÔNG';
                        let errorDetail = '';

                        if (err.type === 'peer-unavailable') {
                            errorMessage = '❌ Máy tính không khả dụng';
                            errorDetail = 'QR code đã hết hạn. Vui lòng quét mã mới.';
                        } else if (err.type === 'busy' || err.message.includes('busy')) {
                            errorMessage = '❌ KẾT NỐI KHÔNG THÀNH CÔNG';
                            errorDetail = '⚠️ <strong>Máy tính đang được sử dụng bởi người khác.</strong><br><br>' +
                                'Hệ thống chỉ cho phép 1 người kết nối tại 1 thời điểm.<br><br>' +
                                'Vui lòng chờ và thử lại sau khi người dùng hiện tại hoàn thành.<br><br>' +
                                '⏳ QR code mới sẽ xuất hiện tự động trên máy tính.';
                        } else if (err.message.includes('NotAllowedError')) {
                            errorMessage = '❌ Từ chối quyền micro';
                            errorDetail = 'Bạn cần cấp quyền truy cập microphone để sử dụng tính năng này.';
                        } else {
                            errorDetail = `Lỗi: ${err.message}`;
                        }

                        showErrorScreen(errorMessage, errorDetail);
                    });
                });

                app.peer.on('error', err => {
                    console.error('❌ Lỗi PeerJS:', err);
                    showErrorScreen('❌ Lỗi hệ thống', `Lỗi kết nối: ${err.message}`);
                });

            } catch (err) {
                console.error('❌ Lỗi kết nối:', err);
                let message = `❌ Lỗi: ${err.message}`;
                let detail = '';

                if (err.name === 'NotAllowedError') {
                    message = '❌ Từ chối quyền micro';
                    detail = 'Vui lòng cấp quyền truy cập microphone để tiếp tục.';
                } else if (err.name === 'NotFoundError') {
                    message = '❌ Không tìm thấy microphone';
                    detail = 'Không tìm thấy thiết bị microphone trên điện thoại.';
                } else {
                    detail = err.message;
                }

                showErrorScreen(message, detail);
            }
        }

        function onSenderConnectionSuccess() {
            document.getElementById('sender-auto-view').classList.add('hidden');
            document.getElementById('sender-connected-view').classList.remove('hidden');
            showStatus('sender', '✅ Đã kết nối! Đang gửi âm thanh...', 'connected');
            startVisualizer();
            updateSenderStats('Đang gửi âm thanh đến máy tính...');
        }

        // ========================================
        // 🚪 THOÁT ỨNG DỤNG KHI NGẮT KẾT NỐI
        // ========================================
        function exitAppOnDisconnect() {
            cleanupPhone();
            showExitScreen();

            setTimeout(() => {
                try {
                    if (window.close && !window.closed) {
                        window.close();
                    }
                } catch (e) {
                    console.log('Không thể đóng tab tự động');
                }
            }, 3000);
        }

        function showExitScreen() {
            document.body.innerHTML = `
            <div class="exit-screen">
                <div class="exit-screen-content">
                    <h2 style="color: #4e4376; margin-bottom: 20px;">✅ Hoàn thành</h2>
                    
                    <div style="font-size: 80px; margin: 20px 0; color: #667eea;" class="pulse">
                        🎤
                    </div>
                    
                    <p style="margin-bottom: 15px; line-height: 1.5;">
                        <strong>Phiên kết nối đã kết thúc</strong>
                    </p>
                    
                    <p style="margin-bottom: 25px; color: #718096; font-size: 14px;">
                        Cảm ơn bạn đã sử dụng dịch vụ mic không dây.
                    </p>
                    
                    <div style="background: #f7fafc; border-radius: 10px; padding: 15px; margin-top: 20px;">
                        <p style="margin: 0; color: #2d3748; font-size: 13px;">
                            Ứng dụng sẽ tự động đóng trong vài giây...
                        </p>
                    </div>
                    
                    <button onclick="window.close()" class="btn btn-danger" style="margin-top: 20px; padding: 12px 24px;">
                        ❌ Đóng ngay
                    </button>
                </div>
            </div>`;
        }

        function showErrorScreen(title, message) {
            document.body.innerHTML = `
            <div class="error-screen">
                <div class="error-screen-content">
                    <h2 style="color: #e53e3e; margin-bottom: 20px;">${title}</h2>
                    
                    <div style="font-size: 80px; margin: 20px 0; color: #e53e3e;">
                        ⚠️
                    </div>
                    
                    <div class="error-message">
                        ${message}
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button onclick="location.reload()" class="btn btn-primary" 
                            style="margin: 5px; padding: 12px 24px; width: 45%;">
                            🔄 Thử lại
                        </button>
                        
                        <button onclick="window.close()" class="btn btn-danger" 
                            style="margin: 5px; padding: 12px 24px; width: 45%;">
                            ❌ Đóng
                        </button>
                    </div>
                    
                    <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                        <p style="color: #718096; font-size: 12px;">
                            <strong>💡 Thông tin:</strong> Hệ thống chỉ cho phép 1 người kết nối tại 1 thời điểm.
                            Vui lòng chờ lượt của bạn.
                        </p>
                    </div>
                </div>
            </div>`;
        }

        function disconnectPhone() {
            showStatus('sender', '🔄 Đang ngắt kết nối...', 'info');
            exitAppOnDisconnect();
        }

        function cleanupPhone() {
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
        }

        // ========================================
        // 🔐 QR CODE SYSTEM
        // ========================================
        function generateRandomToken() {
            return Math.random().toString(36).substring(2, 15) +
                Math.random().toString(36).substring(2, 15) +
                Date.now().toString(36);
        }

        function generateNewQRCode() {
            if (app.peer && app.peer.id) {
                app.currentToken = generateRandomToken();

                const pageUrl = window.location.href.split('?')[0];
                const connectUrl = `${pageUrl}?token=${app.currentToken}&peer=${app.peer.id}&t=${Date.now()}`;

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

                document.getElementById('receiverTokenInfo').innerHTML =
                    `🔐 Token: ${app.currentToken.substring(0, 16)}...<br>
                     ⏱️ QR code sẽ thay đổi sau 1 phút`;

                console.log("✅ Đã tạo QR Code mới");
                return app.currentToken;
            }
            return null;
        }

        function startQrRotation() {
            stopQrRotation();

            app.qrRotateInterval = setInterval(() => {
                const initView = document.getElementById('receiver-initial-view');
                if (initView && !initView.classList.contains('hidden')) {
                    const newToken = generateNewQRCode();
                    console.log('🔄 QR code đã được thay đổi');
                    showStatus('receiver', '🔄 QR code đã được làm mới', 'info');
                }
            }, 60000);
        }

        function stopQrRotation() {
            if (app.qrRotateInterval) {
                clearInterval(app.qrRotateInterval);
                app.qrRotateInterval = null;
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
                await app.qrVideo.play();

                document.getElementById('scanner-container').classList.remove('hidden');
                document.getElementById('startScannerBtn').classList.add('hidden');
                document.getElementById('stopScannerBtn').classList.remove('hidden');
                document.getElementById('scanner-info').textContent = 'Đang quét QR code...';

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
            if (app.qrVideo && app.qrVideo.srcObject) {
                app.qrVideo.srcObject.getTracks().forEach(t => t.stop());
                app.qrVideo.srcObject = null;
            }
            document.getElementById('scanner-container').classList.add('hidden');
            document.getElementById('startScannerBtn').classList.remove('hidden');
            document.getElementById('stopScannerBtn').classList.add('hidden');
            document.getElementById('scanner-info').textContent = 'Nhấn "Quét QR Code" và hướng camera về phía mã QR trên máy tính';
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
            console.log('✅ Đã quét được QR code:', qrData);

            try {
                const url = new URL(qrData);
                const token = url.searchParams.get('token');
                const peerId = url.searchParams.get('peer');

                if (token && peerId) {
                    stopQRScanner();
                    showStatus('sender', '✅ Đã quét thành công! Chuyển đến trang kết nối...', 'connected');

                    setTimeout(() => {
                        window.location.href = `?token=${token}&peer=${peerId}`;
                    }, 1000);
                } else {
                    showStatus('sender', '❌ QR code không hợp lệ', 'error');
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
                let x = 0;

                for (let i = 0; i < bufferLength; i++) {
                    const barHeight = (dataArray[i] / 255) * canvas.height;
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
                console.error('Không thể tạo visualizer:', e);
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
                let x = 0;

                for (let i = 0; i < bufferLength; i++) {
                    const barHeight = (dataArray[i] / 255) * canvas.height;
                    const gradient = canvasCtx.createLinearGradient(0, canvas.height - barHeight, 0, canvas.height);
                    gradient.addColorStop(0, '#38a169');
                    gradient.addColorStop(1, '#2f855a');

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
    </script>
</body>

</html>