<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng </title>
    <script src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>

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

        /* Receiver visualizer */
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
    </style>
</head>

<body>
    <div class="container">
        <h1>🎙️ Mic Qua Mạng </h1>

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
                <button class="btn btn-primary" id="connectBtn">🎤 Kết nối với Máy tính</button>
                <div class="token-info" id="senderTokenInfo"></div>
            </div>

            div id=""

            <div id="sender-connected-view" class="hidden">
                <div class="connection-stats" id="senderStats">
                    📊 Đang kết nối...
                </div>


                <div id="visualizer-container">
                    <canvas id="visualizer"></canvas>
                </div>

                <div>
                    <button id="muteBtn" class="btn btn-warning" onclick="toggleMicrophone(false)">
                        🔇 Tạm dừng
                    </button>
                    <button id="unmuteBtnSender" class="btn btn-secondary hidden" onclick="toggleMicrophone(true)">
                        🎤 Bật âm thanh
                    </button>
                    <button id="doneBtn" class="btn btn-primary hidden" onclick="doneSpeaking()">
                        ✋ Xong
                    </button>
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
                    📱 <strong>Hướng dẫn:</strong><br>
                    1. Quét mã QR bằng điện thoại<br>
                    2. Nhấn "Kết nối với Máy tính"<br>
                    3. Cho phép quyền microphone<br>
                </div>

                <div id="qrcode-container">
                    <p>Đang tạo mã QR...</p>
                </div>


                <div class="token-info" id="receiverTokenInfo">
                    🔒 Mỗi QR code chỉ sử dụng được một lần
                </div>
            </div>

            <div id="receiver-connected-view" class="hidden">
                <div class="connection-stats" id="receiverStats">
                    📊 Đang nhận âm thanh từ điện thoại...
                </div>

                <div id="visualizer-receiver-container">
                    <canvas id="visualizer-receiver"></canvas>
                </div>

                <div class="info">
                    ✅ <strong>Đã kết nối thành công!</strong><br>
                    - Âm thanh đang được gửi đến Python<br>
                    - QR code mới đã sẵn sàng cho lượt tiếp theo
                </div>

                <button id="unmuteBtn" class="btn btn-secondary" onclick="playAudio()">
                    🔊 Nghe thử âm thanh
                </button>

                <div class="info" style="font-size: 12px; margin-top: 20px; background: #fff5f5;">
                    💡 <strong>Lưu ý quan trọng:</strong> Để sử dụng làm micro hệ thống,
                    bạn cần cài đặt VB-CABLE và định tuyến âm thanh từ trình duyệt vào thiết bị ảo.
                </div>

                <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                <button class="btn btn-danger" onclick="disconnectReceiver()">
                    🔴 Ngắt kết nối
                </button>
            </div>
            <div id="receiverStatus"></div>
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
                heartbeatInterval: null
            };

            const PEER_CONFIG = {
                host: '0.peerjs.com',
                port: 443,
                secure: true,
                path: '/'
            };

            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

            // ========================================
            // 🚀 KHỞI TẠO ỨNG DỤNG
            // ========================================
            document.addEventListener('DOMContentLoaded', () => {
                if (isMobile) {
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
                });

                app.peer.on('call', call => {
                    showStatus('receiver', '📲 Có cuộc gọi đến, đang kết nối...', 'info');
                    app.currentCall = call;

                    call.answer();

                    call.on('stream', remoteStream => {
                        const remoteAudio = document.getElementById('remoteAudio');
                        remoteAudio.srcObject = remoteStream;

                        // KẾT NỐI THÀNH CÔNG - Ẩn QR và hiển thị view đã kết nối
                        try {
                            onReceiverConnectionSuccess();
                        } catch (e) {
                            console.error(e);
                        }

                        // Chờ audio thực sự xuất hiện trước khi bật visualizer để tránh "nhảy" giả
                        waitForAudioActivity(remoteStream, 0.015, 150, 3000).then(active => {
                            if (active) {
                                try {
                                    startRemoteVisualizer(remoteStream);
                                } catch (e) {
                                    console.error('Remote visualizer error', e);
                                }
                            } else {
                                console.log('No remote audio activity detected within timeout; visualizer not started.');
                            }
                        });

                        connectWebSocketAndMix(remoteStream);
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
                // Ẩn view ban đầu, hiển thị view đã kết nối
                document.getElementById('receiver-initial-view').classList.add('hidden');
                document.getElementById('receiver-connected-view').classList.remove('hidden');

                // TẠO QR CODE MỚI NGAY LẬP TỨC
                generateNewQRCode();

                showStatus('receiver', '✅ Đã kết nối! QR code mới đã được tạo.', 'connected');
            }

            function cleanupReceiverConnection() {
                if (app.ws) {
                    app.ws.close();
                    app.ws = null;
                }
                if (app.heartbeatInterval) {
                    clearInterval(app.heartbeatInterval);
                    app.heartbeatInterval = null;
                }
                resetReceiverUI();
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

                showStatus('receiver', '🔄 Sẵn sàng cho kết nối mới', 'info');
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

                    // Tạo nguồn âm thanh
                    const remoteSource = audioContext.createMediaStreamSource(remoteStream);
                    const localSource = audioContext.createMediaStreamSource(localStream);

                    // Điều chỉnh âm lượng
                    const remoteGain = audioContext.createGain();
                    remoteGain.gain.value = 1.0;
                    const localGain = audioContext.createGain();
                    localGain.gain.value = 1.0;

                    remoteSource.connect(remoteGain);
                    localSource.connect(localGain);

                    // Merge thành stereo
                    const merger = audioContext.createChannelMerger(2);
                    remoteGain.connect(merger, 0, 0);
                    localGain.connect(merger, 0, 1);

                    // Xử lý audio
                    const processor = audioContext.createScriptProcessor(4096, 2, 2);
                    merger.connect(processor);

                    // Mute loa
                    const gainNode = audioContext.createGain();
                    gainNode.gain.value = 0;
                    processor.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    // Kết nối WebSocket với AUTH
                    console.log("🔄 Đang kết nối WebSocket...");
                    app.ws = new WebSocket("ws://localhost:8765");
                    app.ws.binaryType = "arraybuffer";

                    app.ws.onopen = () => {
                        console.log("✅ WebSocket đã kết nối!");

                        // GỬI TOKEN ĐẦU TIÊN để xác thực
                        const authMessage = `AUTH:${app.currentToken}`;
                        app.ws.send(authMessage);

                        // Bắt đầu gửi audio sau khi auth
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

                        // Heartbeat
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

                    app.peer.on('open', () => {
                        const call = app.peer.call(receiverId, app.localStream);
                        app.currentCall = call;

                        // Coi như kết nối thành công ngay khi cuộc gọi được tạo (đảm bảo UI sender thay đổi)
                        try {
                            onSenderConnectionSuccess();
                        } catch (e) {
                            console.error(e);
                        }

                        call.on('stream', remoteStream => {
                            // Khi có remote stream, chờ audio thực sự trước khi bật visualizer
                            waitForAudioActivity(remoteStream, 0.015, 150, 3000).then(active => {
                                if (active) {
                                    try {
                                        startVisualizer();
                                    } catch (e) {
                                        console.error('Sender visualizer error', e);
                                    }
                                } else {
                                    console.log('No remote audio detected for sender; sender visualizer not auto-started.');
                                }
                            });
                        });

                        call.on('close', () => {
                            showStatus('sender', '🚫 Máy tính đã ngắt kết nối.', 'info');
                            resetSenderUI();
                        });

                        call.on('error', (err) => {
                            showStatus('sender', `❌ Lỗi kết nối: ${err.message}`, 'error');
                            resetSenderUI();
                        });
                    });

                    app.peer.on('error', err => {
                        showStatus('sender', `❌ Lỗi PeerJS: ${err.message}`, 'error');
                        resetSenderUI();
                    });

                } catch (err) {
                    let message = `❌ Lỗi: ${err.message}`;
                    if (err.name === 'NotAllowedError') {
                        message = '❌ Từ chối quyền micro. Vui lòng cấp quyền để tiếp tục.';
                    }
                    showStatus('sender', message, 'error');
                    resetSenderUI();
                }
            }

            function onSenderConnectionSuccess() {
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.remove('hidden');
                document.getElementById('muteBtn').classList.remove('hidden');
                document.getElementById('unmuteBtnSender').classList.add('hidden');
                // Hiển thị nút "Xong" để người dùng kết thúc việc nói mà vẫn giữ kết nối
                const doneBtn = document.getElementById('doneBtn');
                if (doneBtn) doneBtn.classList.remove('hidden');
                // Hiển thị nút ngắt kết nối rõ ràng trên điện thoại
                const disconnectBtnSender = document.getElementById('disconnectBtnSender');
                if (disconnectBtnSender) disconnectBtnSender.classList.remove('hidden');

                showStatus('sender', '✅ Đã kết nối! Đang gửi âm thanh...', 'connected');
                startVisualizer();
                updateSenderStats('Đang gửi âm thanh...');
            }

            function resetSenderUI() {
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.add('hidden');
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('senderStatus').innerHTML = '';

                const connectBtn = document.getElementById('connectBtn');
                if (connectBtn) connectBtn.disabled = false;

                stopVisualizer();
                window.history.replaceState({}, document.title, window.location.pathname);

                // ẩn nút ngắt kết nối khi reset UI
                const disconnectBtnSender = document.getElementById('disconnectBtnSender');
                if (disconnectBtnSender) disconnectBtnSender.classList.add('hidden');
            }

            // ========================================
            // 🔐 QR CODE SYSTEM
            // ========================================
            // ========================================
            // 🔐 QR CODE SYSTEM - TỰ ĐỘNG RANDOM KHI KẾT NỐI
            // ========================================
            function generateRandomToken() {
                return Math.random().toString(36).substring(2, 15) +
                    Math.random().toString(36).substring(2, 15);
            }

            function generateNewQRCode() {
                if (app.peer && app.peer.id) {
                    // Tạo token RANDOM mới
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

                    document.getElementById('receiverTokenInfo').innerHTML =
                        `🔐 Token: ${app.currentToken.substring(0, 12)}...`;

                    console.log("✅ Đã tạo QR Code mới:", app.currentToken);
                    return app.currentToken;
                }
                return null;
            }

            function initializeReceiver() {
                if (app.peer) app.peer.destroy();

                showStatus('receiver', 'Đang kết nối đến máy chủ PeerJS...', 'info');
                app.peer = new Peer(PEER_CONFIG);

                app.peer.on('open', id => {
                    showStatus('receiver', `✅ Sẵn sàng! ID: ${id}`, 'info');
                    generateNewQRCode(); // Tạo QR code đầu tiên
                });

                app.peer.on('call', call => {
                    showStatus('receiver', '📲 Có cuộc gọi đến, đang kết nối...', 'info');
                    app.currentCall = call;

                    call.answer();

                    call.on('stream', remoteStream => {
                        const remoteAudio = document.getElementById('remoteAudio');
                        remoteAudio.srcObject = remoteStream;

                        // KẾT NỐI THÀNH CÔNG - Ẩn QR và hiển thị view đã kết nối
                        try {
                            onReceiverConnectionSuccess();
                        } catch (e) {
                            console.error(e);
                        }

                        // Chờ audio thực sự xuất hiện trước khi bật visualizer để tránh "nhảy" giả
                        waitForAudioActivity(remoteStream, 0.015, 150, 3000).then(active => {
                            if (active) {
                                try {
                                    startRemoteVisualizer(remoteStream);
                                } catch (e) {
                                    console.error('Remote visualizer error', e);
                                }
                            } else {
                                console.log('No remote audio activity detected within timeout; visualizer not started.');
                            }
                        });

                        // Tạo QR mới cho lượt sau (không hiển thị trên web hiện tại vì đã ẩn view ban đầu)
                        setTimeout(() => {
                            const newToken = generateNewQRCode();
                            showStatus('receiver', `✅ Đã kết nối! QR code mới đã được tạo (Token: ${newToken.substring(0, 8)}...)`, 'connected');
                        }, 500);

                        connectWebSocketAndMix(remoteStream);
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

            function cleanupReceiverConnection() {
                if (app.ws) {
                    app.ws.close();
                    app.ws = null;
                }
                if (app.heartbeatInterval) {
                    clearInterval(app.heartbeatInterval);
                    app.heartbeatInterval = null;
                }

                // Dừng visualizer remote nếu đang chạy
                try {
                    stopRemoteVisualizer();
                } catch (e) {
                    /* ignore */
                }

                // 🔄 TỰ ĐỘNG TẠO QR CODE MỚI KHI NGẮT KẾT NỐI
                setTimeout(() => {
                    generateNewQRCode();
                    showStatus('receiver', '🔄 Đã tạo QR code mới cho lượt kết nối tiếp theo', 'info');
                }, 1000);

                resetReceiverUI();
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
                    app.currentCall = null;
                }
                cleanupReceiverConnection();
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

            function toggleMicrophone(shouldBeEnabled) {
                if (app.localStream) {
                    app.localStream.getAudioTracks().forEach(track => {
                        track.enabled = shouldBeEnabled;
                    });
                    document.getElementById('muteBtn').classList.toggle('hidden', shouldBeEnabled);
                    document.getElementById('unmuteBtnSender').classList.toggle('hidden', !shouldBeEnabled);

                    // Điều khiển hiển thị nút "Xong" theo trạng thái micro
                    const doneBtn = document.getElementById('doneBtn');
                    if (doneBtn) {
                        if (shouldBeEnabled) doneBtn.classList.remove('hidden');
                        else doneBtn.classList.add('hidden');
                    }

                    if (shouldBeEnabled) {
                        showStatus('sender', '🎤 Đã bật âm thanh.', 'connected');
                        startVisualizer();
                    } else {
                        showStatus('sender', '🔇 Đã tắt âm thanh.', 'info');
                        stopVisualizer();
                    }
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
                if (app.peer) {
                    app.peer.destroy();
                    app.peer = null;
                }
                stopVisualizer();
                resetSenderUI();
            }

            function doneSpeaking() {
                // Tạm dừng gửi âm thanh nhưng để kết nối PeerJS vẫn còn
                if (app.localStream) {
                    app.localStream.getAudioTracks().forEach(track => track.enabled = false);
                }

                // Cập nhật UI: ẩn nút Xong, dừng visualizer và hiện trạng thái tạm dừng
                const doneBtn = document.getElementById('doneBtn');
                if (doneBtn) doneBtn.classList.add('hidden');

                // Đồng thời cập nhật nút mute/unmute theo trạng thái tắt micro
                const muteBtn = document.getElementById('muteBtn');
                const unmuteBtnSender = document.getElementById('unmuteBtnSender');
                if (muteBtn) muteBtn.classList.remove('hidden');
                if (unmuteBtnSender) unmuteBtnSender.classList.add('hidden');

                // Hiện nút ngắt kết nối rõ ràng trên điện thoại sau khi người dùng dừng nói
                const disconnectBtnSender = document.getElementById('disconnectBtnSender');
                if (disconnectBtnSender) disconnectBtnSender.classList.remove('hidden');

                stopVisualizer();
                showStatus('sender', '✋ Đã xong. Âm thanh tạm dừng.', 'info');
            }

            function playAudio() {
                const remoteAudio = document.getElementById('remoteAudio');
                remoteAudio.play()
                    .then(() => {
                        showStatus('receiver', '✅ Đang phát âm thanh qua loa!', 'connected');
                        document.getElementById('unmuteBtn').classList.add('hidden');
                    })
                    .catch(e => showStatus('receiver', `❌ Lỗi phát âm thanh: ${e.message}`, 'error'));
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

            // Remote (receiver) visualizer
            function startRemoteVisualizer(remoteStream) {
                if (!remoteStream) return;

                if (!app.remoteAudioContext) {
                    app.remoteAudioContext = new(window.AudioContext || window.webkitAudioContext)();
                }

                // Stop previous analyser if any
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
                if (app.remoteAudioContext) {
                    try {
                        /* keep context for resume later */
                    } catch (e) {}
                }
            }

            // Wait for audio activity on a MediaStream. Resolves true if activity detected within maxWait.
            function waitForAudioActivity(stream, threshold = 0.02, requiredMs = 150, maxWait = 3000) {
                return new Promise((resolve) => {
                    if (!stream) return resolve(false);

                    const audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                    let source;
                    try {
                        source = audioCtx.createMediaStreamSource(stream);
                    } catch (e) {
                        // can't create source (maybe no tracks)
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
                        // compute RMS
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