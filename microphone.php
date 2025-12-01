<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng </title>
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

        .visualizer-label {
            font-size: 12px;
            color: #718096;
            margin-bottom: 5px;
            text-align: center;
        }

        .visualizer-pulse {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.7;
            }
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
                <button class="btn btn-primary" id="connectBtn"> Kết nối với Máy tính</button>
                <div class="token-info" id="senderTokenInfo"></div>
            </div>

            <div id="sender-connected-view" class="hidden">
                <div class="connection-stats" id="senderStats">
                    Đang thiết lập kết nối...
                </div>

                <div class="visualizer-label">🎤 MIC ĐIỆN THOẠI</div>
                <div id="visualizer-container">
                    <canvas id="visualizer"></canvas>
                </div>

                <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                <button id="disconnectBtnSender" class="btn btn-danger" onclick="disconnect()">🔴 Ngắt kết nối</button>
            </div>
            <div id="senderStatus" class="status"></div>
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

                <div class="token-info" id="receiverTokenInfo">
                    Mỗi QR code chỉ sử dụng được một lần
                </div>
            </div>

            <div id="receiver-connected-view" class="hidden">
                <div class="connection-stats" id="receiverStats">
                    Đang thiết lập kết nối âm thanh...
                </div>

                <div class="visualizer-label">🔊 ÂM THANH NHẬN ĐƯỢC</div>
                <div id="visualizer-receiver-container">
                    <canvas id="visualizer-receiver"></canvas>
                </div>

                <div class="info">
                    ✅ <strong>Đã kết nối thành công!</strong><br>
                    - Âm thanh đang được gửi đến Python<br>
                </div>

                <div class="info" style="font-size: 12px; margin-top: 20px; background: #fff5f5;">
                    💡 <strong>Lưu ý quan trọng:</strong> Để sử dụng làm micro hệ thống,
                    bạn cần cài đặt VB-CABLE và định tuyến âm thanh từ trình duyệt vào thiết bị ảo.
                </div>

                <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                <button class="btn btn-danger" onclick="disconnectReceiver()">
                    🔴 Ngắt kết nối
                </button>
            </div>
            <div id="receiverStatus" class="status"></div>
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
            remoteAudioContext: null,
            remoteAnalyser: null,
            remoteVisualizerFrameId: null,
            ws: null,
            currentToken: null,
            sessionId: null,
            heartbeatInterval: null,
            qrRotateInterval: null,
            qrVideo: null,
            isVisualizerActive: false,
            isRemoteVisualizerActive: false
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
            if (app.peer) {
                app.peer.destroy();
                app.peer = null;
            }

            showStatus('receiver', 'Đang kết nối đến máy chủ PeerJS...', 'info');
            app.peer = new Peer(PEER_CONFIG);

            app.peer.on('open', id => {
                console.log('✅ PeerJS Receiver ID:', id);
                showStatus('receiver', `✅ Sẵn sàng! ID: ${id.substring(0, 8)}...`, 'info');
                generateNewQRCode();
                startQrRotation();
            });

            app.peer.on('call', call => {
                // Kiểm tra nếu đã có cuộc gọi khác đang hoạt động
                if (app.currentCall && app.currentCall.open) {
                    showStatus('receiver', '⚠️ Máy tính đang bận. Vui lòng thử lại sau.', 'info');
                    console.log('❌ Từ chối cuộc gọi - đã có cuộc gọi khác hoạt động');
                    try {
                        call.close();
                    } catch (e) {}
                    return;
                }

                showStatus('receiver', '📲 Có cuộc gọi đến, đang kết nối...', 'info');
                app.currentCall = call;

                call.answer();

                call.on('stream', remoteStream => {
                    console.log('✅ Nhận được audio stream từ điện thoại');

                    const remoteAudio = document.getElementById('remoteAudio');
                    remoteAudio.srcObject = remoteStream;

                    // Ẩn QR và hiển thị view đã kết nối
                    try {
                        onReceiverConnectionSuccess();
                    } catch (e) {
                        console.error('Lỗi khi hiển thị receiver connected view:', e);
                    }

                    // ĐỢI audio thực sự có dữ liệu trước khi bật visualizer
                    setTimeout(() => {
                        checkAudioActivity(remoteStream).then(hasAudio => {
                            if (hasAudio) {
                                console.log('✅ Phát hiện âm thanh từ điện thoại');
                                updateReceiverStats('✅ Đang nhận âm thanh từ điện thoại...');
                                startRemoteVisualizer(remoteStream);
                            } else {
                                console.log('⚠️ Chưa nhận được âm thanh từ điện thoại');
                                updateReceiverStats('🔇 Chưa phát hiện âm thanh - hãy nói thử vào điện thoại');
                                // Vẫn bật visualizer nhưng với thanh tĩnh
                                startRemoteVisualizer(remoteStream);
                            }
                        });
                    }, 500);

                    connectWebSocketAndMix(remoteStream);
                });

                call.on('close', () => {
                    console.log('🚫 Cuộc gọi đã đóng');
                    showStatus('receiver', '🚫 Điện thoại đã ngắt kết nối.', 'info');
                    cleanupReceiverConnection();
                });

                call.on('error', err => {
                    console.error('❌ Lỗi cuộc gọi:', err);
                    showStatus('receiver', `❌ Lỗi kết nối: ${err.message || err}`, 'error');
                    cleanupReceiverConnection();
                });
            });

            app.peer.on('error', err => {
                console.error('❌ Lỗi PeerJS receiver:', err);
                showStatus('receiver', `❌ Lỗi PeerJS: ${err.message || err}`, 'error');
            });
        }

        function onReceiverConnectionSuccess() {
            // Ẩn view ban đầu, hiển thị view đã kết nối
            document.getElementById('receiver-initial-view').classList.add('hidden');
            document.getElementById('receiver-connected-view').classList.remove('hidden');

            // Dừng xoay QR tự động
            stopQrRotation();

            // Tạo QR code mới cho lượt kết nối tiếp theo
            generateNewQRCode();

            showStatus('receiver', '✅ Đã kết nối với điện thoại!', 'connected');
        }

        function cleanupReceiverConnection() {
            console.log('🧹 Dọn dẹp kết nối receiver');

            // Đóng WebSocket
            if (app.ws) {
                app.ws.close();
                app.ws = null;
            }

            // Dừng heartbeat
            if (app.heartbeatInterval) {
                clearInterval(app.heartbeatInterval);
                app.heartbeatInterval = null;
            }

            // Dừng visualizer remote
            stopRemoteVisualizer();

            // Dừng remote audio
            const remoteAudio = document.getElementById('remoteAudio');
            if (remoteAudio && remoteAudio.srcObject) {
                try {
                    remoteAudio.srcObject.getTracks().forEach(track => track.stop());
                    remoteAudio.srcObject = null;
                } catch (e) {}
            }

            // Xoá reference cuộc gọi cũ
            if (app.currentCall) {
                try {
                    app.currentCall.close();
                } catch (e) {}
                app.currentCall = null;
            }

            // Tạo QR code mới
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

            const receiverStatus = document.getElementById('receiverStatus');
            if (receiverStatus) {
                receiverStatus.innerHTML = '';
                receiverStatus.className = 'status';
            }
        }

        function disconnectReceiver() {
            console.log('🔴 Người dùng yêu cầu ngắt kết nối receiver');
            cleanupReceiverConnection();
        }

        // ========================================
        // 🔗 KẾT NỐI WEBSOCKET VÀ GỬI AUDIO
        // ========================================
        function connectWebSocketAndMix(remoteStream) {
            console.log('🔄 Đang thiết lập WebSocket và mix audio...');

            // Chỉ lấy microphone máy tính nếu cần mix
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
                localGain.gain.value = 0.0; // Tắt microphone máy tính mặc định

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

                    updateReceiverStats('✅ Đang gửi âm thanh đến Python...');

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
                                    updateReceiverStats('✅ Đang gửi âm thanh đến Python...');
                                    break;

                                case 'CONNECTION_REFUSED':
                                    showStatus('receiver', `❌ ${data.message}`, 'error');
                                    disconnectReceiver();
                                    break;

                                case 'HEARTBEAT_ACK':
                                    updateReceiverStats(`✅ Kết nối ổn định - ${new Date().toLocaleTimeString()}`);
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
                    updateReceiverStats('🔌 Mất kết nối Python server');
                };

                app.ws.onerror = (error) => {
                    console.error("❌ WebSocket error:", error);
                    showStatus('receiver', '❌ Lỗi kết nối Python server', 'error');
                };

            }).catch(err => {
                console.error("❌ Lỗi truy cập microphone máy tính:", err);
                // Vẫn tiếp tục với remote stream nếu không lấy được microphone máy tính
                updateReceiverStats('⚠️ Không thể truy cập micro máy tính, chỉ gửi âm thanh từ điện thoại');
            });
        }

        // ========================================
        // 📱 ĐIỆN THOẠI - SENDER
        // ========================================
        function initializeSender() {
            console.log('📱 Khởi tạo sender (điện thoại)');

            // Dừng mọi visualizer đang chạy
            stopVisualizer();

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

            // Đảm bảo visualizer tắt khi khởi tạo
            clearVisualizerCanvas('visualizer');
        }

        async function connectToReceiver(receiverId, token) {
            try {
                showStatus('sender', '🎤 Đang xin quyền micro...', 'info');

                // Dừng visualizer trước khi bắt đầu
                stopVisualizer();

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

                showStatus('sender', '📡 Đang kết nối đến máy tính...', 'info');

                // Xóa canvas visualizer
                clearVisualizerCanvas('visualizer');

                if (app.peer) {
                    app.peer.destroy();
                }

                app.peer = new Peer(PEER_CONFIG);

                app.peer.on('open', () => {
                    console.log('✅ PeerJS Sender mở, đang gọi đến:', receiverId);
                    const call = app.peer.call(receiverId, app.localStream);
                    app.currentCall = call;

                    updateSenderStats('📞 Đang gọi đến máy tính...');

                    call.on('stream', remoteStream => {
                        console.log('✅ Nhận được remote stream từ máy tính');
                        onSenderConnectionSuccess();

                        // Kiểm tra audio activity
                        setTimeout(() => {
                            checkAudioActivity(app.localStream).then(hasAudio => {
                                if (hasAudio) {
                                    console.log('✅ Phát hiện âm thanh từ micro điện thoại');
                                    updateSenderStats('✅ Đang gửi âm thanh...');
                                } else {
                                    console.log('⚠️ Micro im lặng');
                                    updateSenderStats('🔇 Micro im lặng - hãy nói thử');
                                }
                            });
                        }, 500);
                    });

                    call.on('close', () => {
                        console.log('🚫 Cuộc gọi đã đóng');
                        showStatus('sender', '🚫 Máy tính đã ngắt kết nối.', 'info');
                        exitAppOnDisconnect();
                    });

                    call.on('error', (err) => {
                        console.error('❌ Lỗi cuộc gọi:', err);
                        showStatus('sender', `❌ Lỗi kết nối: ${err.message || err}`, 'error');
                        setTimeout(() => {
                            exitAppOnDisconnect();
                        }, 2000);
                    });
                });

                app.peer.on('error', err => {
                    console.error('❌ Lỗi PeerJS sender:', err);
                    showStatus('sender', `❌ Lỗi PeerJS: ${err.message || err}`, 'error');
                    setTimeout(() => {
                        exitAppOnDisconnect();
                    }, 2000);
                });

            } catch (err) {
                console.error('❌ Lỗi khi kết nối:', err);
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
            console.log('✅ Sender kết nối thành công');

            document.getElementById('sender-auto-view').classList.add('hidden');
            document.getElementById('sender-connected-view').classList.remove('hidden');
            showStatus('sender', '✅ Đã kết nối với máy tính!', 'connected');

            // Bắt đầu visualizer
            startVisualizer();
            updateSenderStats('✅ Đã kết nối, đang gửi âm thanh...');
        }

        // ========================================
        // 🚪 THOÁT ỨNG DỤNG KHI NGẮT KẾT NỐI
        // ========================================
        function exitAppOnDisconnect() {
            console.log('🚪 Thoát ứng dụng khi ngắt kết nối');

            // Dọn dẹp tài nguyên
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

            // Hiển thị màn hình thoát
            showExitScreen();

            // Thử đóng tab tự động sau 2 giây
            setTimeout(() => {
                try {
                    if (window.close && !window.closed) {
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
                    0% { transform: scale(1); opacity: 0.7; }
                    50% { transform: scale(1.1); opacity: 1; }
                    100% { transform: scale(1); opacity: 0.7; }
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
            if (isMobile) {
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
            if (!isMobile) {
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.add('hidden');
                document.getElementById('sender-manual-view').classList.remove('hidden');

                const senderStatus = document.getElementById('senderStatus');
                if (senderStatus) {
                    senderStatus.innerHTML = '';
                    senderStatus.className = 'status';
                }

                const connectBtn = document.getElementById('connectBtn');
                if (connectBtn) connectBtn.disabled = false;

                stopVisualizer();
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        // ========================================
        // 🔐 QR CODE SYSTEM
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

                document.getElementById('receiverTokenInfo').innerHTML =
                    `🔐 Mã ID: ${app.currentToken.substring(0, 12)}...`;

                console.log("✅ Đã tạo QR Code mới, Token:", app.currentToken.substring(0, 8) + '...');
                return app.currentToken;
            }
            return null;
        }

        function startQrRotation() {
            stopQrRotation();
            if (app.peer && app.peer.id) {
                app.qrRotateInterval = setInterval(() => {
                    const initView = document.getElementById('receiver-initial-view');
                    if (initView && !initView.classList.contains('hidden')) {
                        const newToken = generateNewQRCode();
                        console.log('🔁 QR rotated:', newToken.substring(0, 8) + '...');
                    }
                }, 60 * 1000);
            }
        }

        function stopQrRotation() {
            if (app.qrRotateInterval) {
                clearInterval(app.qrRotateInterval);
                app.qrRotateInterval = null;
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
            console.log('📷 Khởi tạo QR scanner');
            app.qrVideo = document.getElementById('qr-video');
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
                showStatus('sender', '📷 Đang quét QR code...', 'info');

                startQRScanning();
            } catch (error) {
                console.error('❌ Lỗi camera:', error);
                let message = 'Lỗi không xác định';
                if (error.name === 'NotAllowedError') message = '❌ Quyền truy cập camera bị từ chối. Vui lòng cho phép camera để quét QR code.';
                else if (error.name === 'NotFoundError') message = '❌ Không tìm thấy camera.';
                else message = `❌ Lỗi: ${error.message}`;
                showStatus('sender', message, 'error');
            }
        }

        function startQRScanning() {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');

            function scan() {
                if (!app.qrVideo || !app.qrVideo.srcObject) return;

                if (app.qrVideo.readyState === app.qrVideo.HAVE_ENOUGH_DATA) {
                    canvas.width = app.qrVideo.videoWidth;
                    canvas.height = app.qrVideo.videoHeight;
                    context.drawImage(app.qrVideo, 0, 0, canvas.width, canvas.height);

                    try {
                        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: 'dontInvert'
                        });

                        if (code) {
                            console.log('✅ Phát hiện QR code:', code.data.substring(0, 50) + '...');
                            handleQRCodeDetected(code.data);
                            return;
                        }
                    } catch (e) {
                        console.log('QR scan error:', e);
                    }
                }
                requestAnimationFrame(scan);
            }

            scan();
        }

        function handleQRCodeDetected(url) {
            try {
                console.log('🔗 QR Code URL:', url);
                const urlObj = new URL(url);
                const token = urlObj.searchParams.get('token');
                const peer = urlObj.searchParams.get('peer');

                if (token && peer) {
                    stopQRScanner();

                    // Cập nhật URL với token và peer ID
                    const newUrl = `${window.location.pathname}?token=${token}&peer=${peer}`;
                    window.history.pushState({}, '', newUrl);

                    // Tải lại view sender với thông tin mới
                    app.currentToken = token;
                    document.getElementById('sender-manual-view').classList.add('hidden');
                    document.getElementById('sender-auto-view').classList.remove('hidden');
                    document.getElementById('senderTokenInfo').textContent = `Token: ${token.substring(0, 8)}...`;

                    const connectBtn = document.getElementById('connectBtn');
                    connectBtn.disabled = false;
                    connectBtn.onclick = () => {
                        connectBtn.disabled = true;
                        connectToReceiver(peer, token);
                    };

                    showStatus('sender', '✅ Đã quét QR code thành công!', 'connected');
                } else {
                    showStatus('sender', '❌ QR code không hợp lệ', 'error');
                }
            } catch (e) {
                console.error('❌ Lỗi xử lý QR code:', e);
                showStatus('sender', '❌ Không thể đọc QR code', 'error');
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

        // ========================================
        // 📊 AUDIO VISUALIZER FUNCTIONS
        // ========================================
        function clearVisualizerCanvas(canvasId) {
            const canvas = document.getElementById(canvasId);
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#f7fafc';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            }
        }

        function startVisualizer() {
            console.log('🎨 Bắt đầu visualizer sender');

            if (!app.localStream || !app.localStream.active) {
                console.log('⚠️ Không thể bật visualizer: stream không khả dụng');
                return;
            }

            // Kiểm tra micro có bật không
            const audioTracks = app.localStream.getAudioTracks();
            if (audioTracks.length === 0 || !audioTracks[0].enabled) {
                console.log('⚠️ Micro chưa bật');
                return;
            }

            // Dừng visualizer cũ nếu có
            stopVisualizer();

            try {
                app.audioContext = new(window.AudioContext || window.webkitAudioContext)();
                app.analyser = app.audioContext.createAnalyser();
                const source = app.audioContext.createMediaStreamSource(app.localStream);
                source.connect(app.analyser);
            } catch (e) {
                console.error('❌ Không thể tạo AudioContext:', e);
                return;
            }

            app.analyser.fftSize = 256;
            const bufferLength = app.analyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);
            const canvas = document.getElementById('visualizer');

            if (!canvas) {
                console.error('❌ Không tìm thấy canvas visualizer');
                return;
            }

            const canvasCtx = canvas.getContext('2d');
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            // Xóa canvas
            canvasCtx.fillStyle = '#f7fafc';
            canvasCtx.fillRect(0, 0, canvas.width, canvas.height);

            let silentFrames = 0;
            const SILENT_THRESHOLD = 10;

            function draw() {
                if (!app.localStream || !app.localStream.active || !app.analyser) {
                    stopVisualizer();
                    return;
                }

                app.visualizerFrameId = requestAnimationFrame(draw);
                app.analyser.getByteFrequencyData(dataArray);

                // Tính mức độ âm thanh
                let sum = 0;
                for (let i = 0; i < bufferLength; i++) {
                    sum += dataArray[i];
                }
                const average = sum / bufferLength;

                // Xóa canvas
                canvasCtx.fillStyle = '#f7fafc';
                canvasCtx.fillRect(0, 0, canvas.width, canvas.height);

                // Phát hiện im lặng
                if (average < 5) { // Ngưỡng rất thấp cho im lặng
                    silentFrames++;
                    if (silentFrames > SILENT_THRESHOLD) {
                        // Vẽ thanh tĩnh khi im lặng lâu
                        drawSilentBars(canvasCtx, canvas.width, canvas.height, bufferLength);
                        return;
                    }
                } else {
                    silentFrames = 0;
                }

                // Vẽ thanh âm thanh bình thường
                drawAudioBars(canvasCtx, canvas.width, canvas.height, bufferLength, dataArray);
            }

            draw();
            app.isVisualizerActive = true;
            console.log('✅ Visualizer sender đã bật');
        }

        function drawSilentBars(ctx, width, height, bufferLength) {
            const barWidth = (width / bufferLength) * 2.5;
            let x = 0;

            for (let i = 0; i < bufferLength; i += 3) { // Vẽ thưa hơn
                const barHeight = 2 + Math.random() * 3; // Rất nhỏ
                const gradient = ctx.createLinearGradient(0, height - barHeight, 0, height);
                gradient.addColorStop(0, '#e2e8f0');
                gradient.addColorStop(1, '#cbd5e0');

                ctx.fillStyle = gradient;
                ctx.fillRect(x, height - barHeight, barWidth, barHeight);
                x += barWidth + 1;
            }
        }

        function drawAudioBars(ctx, width, height, bufferLength, dataArray) {
            const barWidth = (width / bufferLength) * 2.5;
            let x = 0;

            for (let i = 0; i < bufferLength; i++) {
                const barHeight = (dataArray[i] / 255) * height;
                const gradient = ctx.createLinearGradient(0, height - barHeight, 0, height);

                // Màu sắc dựa trên cường độ âm thanh
                if (barHeight > height * 0.7) {
                    gradient.addColorStop(0, '#e53e3e'); // Đỏ khi to
                    gradient.addColorStop(1, '#c53030');
                } else if (barHeight > height * 0.4) {
                    gradient.addColorStop(0, '#d69e2e'); // Vàng khi trung bình
                    gradient.addColorStop(1, '#b7791f');
                } else {
                    gradient.addColorStop(0, '#667eea'); // Xanh khi nhỏ
                    gradient.addColorStop(1, '#764ba2');
                }

                ctx.fillStyle = gradient;
                ctx.fillRect(x, height - barHeight, barWidth, barHeight);
                x += barWidth + 1;
            }
        }

        function stopVisualizer() {
            console.log('🛑 Dừng visualizer sender');

            if (app.visualizerFrameId) {
                cancelAnimationFrame(app.visualizerFrameId);
                app.visualizerFrameId = null;
            }

            clearVisualizerCanvas('visualizer');

            if (app.audioContext) {
                try {
                    app.audioContext.close();
                } catch (e) {}
                app.audioContext = null;
            }

            app.analyser = null;
            app.isVisualizerActive = false;
        }

        function startRemoteVisualizer(remoteStream) {
            console.log('🎨 Bắt đầu remote visualizer');

            if (!remoteStream || !remoteStream.active) {
                console.log('⚠️ Remote stream không khả dụng');
                return;
            }

            // Dừng visualizer cũ
            stopRemoteVisualizer();

            try {
                app.remoteAudioContext = new(window.AudioContext || window.webkitAudioContext)();
                app.remoteAnalyser = app.remoteAudioContext.createAnalyser();
                app.remoteAnalyser.fftSize = 256;

                const source = app.remoteAudioContext.createMediaStreamSource(remoteStream);
                source.connect(app.remoteAnalyser);
            } catch (e) {
                console.error('❌ Không thể tạo remote AudioContext:', e);
                return;
            }

            const bufferLength = app.remoteAnalyser.frequencyBinCount;
            const dataArray = new Uint8Array(bufferLength);
            const canvas = document.getElementById('visualizer-receiver');

            if (!canvas) {
                console.error('❌ Không tìm thấy canvas remote visualizer');
                return;
            }

            const canvasCtx = canvas.getContext('2d');
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            // Xóa canvas
            canvasCtx.fillStyle = '#f7fafc';
            canvasCtx.fillRect(0, 0, canvas.width, canvas.height);

            let silentFrames = 0;
            const SILENT_THRESHOLD = 10;

            function drawRemote() {
                if (!remoteStream || !remoteStream.active || !app.remoteAnalyser) {
                    stopRemoteVisualizer();
                    return;
                }

                app.remoteVisualizerFrameId = requestAnimationFrame(drawRemote);
                app.remoteAnalyser.getByteFrequencyData(dataArray);

                // Tính mức độ âm thanh
                let sum = 0;
                for (let i = 0; i < bufferLength; i++) {
                    sum += dataArray[i];
                }
                const average = sum / bufferLength;

                // Xóa canvas
                canvasCtx.fillStyle = '#f7fafc';
                canvasCtx.fillRect(0, 0, canvas.width, canvas.height);

                // Phát hiện im lặng
                if (average < 5) {
                    silentFrames++;
                    if (silentFrames > SILENT_THRESHOLD) {
                        drawSilentBars(canvasCtx, canvas.width, canvas.height, bufferLength);
                        return;
                    }
                } else {
                    silentFrames = 0;
                }

                // Vẽ thanh âm thanh
                drawAudioBars(canvasCtx, canvas.width, canvas.height, bufferLength, dataArray);
            }

            drawRemote();
            app.isRemoteVisualizerActive = true;
            console.log('✅ Remote visualizer đã bật');
        }

        function stopRemoteVisualizer() {
            console.log('🛑 Dừng remote visualizer');

            if (app.remoteVisualizerFrameId) {
                cancelAnimationFrame(app.remoteVisualizerFrameId);
                app.remoteVisualizerFrameId = null;
            }

            clearVisualizerCanvas('visualizer-receiver');

            if (app.remoteAudioContext) {
                try {
                    app.remoteAudioContext.close();
                } catch (e) {}
                app.remoteAudioContext = null;
            }

            app.remoteAnalyser = null;
            app.isRemoteVisualizerActive = false;
        }

        // ========================================
        // 🔊 AUDIO ACTIVITY DETECTION
        // ========================================
        async function checkAudioActivity(stream, threshold = 0.01, checkDuration = 500) {
            return new Promise((resolve) => {
                if (!stream || !stream.active) {
                    resolve(false);
                    return;
                }

                let audioContext = null;
                let analyser = null;
                let source = null;

                try {
                    audioContext = new(window.AudioContext || window.webkitAudioContext)();
                    analyser = audioContext.createAnalyser();
                    analyser.fftSize = 512;
                    source = audioContext.createMediaStreamSource(stream);
                    source.connect(analyser);
                } catch (e) {
                    console.error('❌ Lỗi tạo audio context cho kiểm tra:', e);
                    if (audioContext) audioContext.close();
                    resolve(false);
                    return;
                }

                const dataArray = new Float32Array(analyser.fftSize);
                let hasAudio = false;
                const startTime = Date.now();

                function check() {
                    if (Date.now() - startTime > checkDuration) {
                        cleanup();
                        resolve(hasAudio);
                        return;
                    }

                    analyser.getFloatTimeDomainData(dataArray);

                    // Tính RMS
                    let sum = 0;
                    for (let i = 0; i < dataArray.length; i++) {
                        sum += dataArray[i] * dataArray[i];
                    }
                    const rms = Math.sqrt(sum / dataArray.length);

                    if (rms > threshold) {
                        hasAudio = true;
                        cleanup();
                        resolve(true);
                        return;
                    }

                    requestAnimationFrame(check);
                }

                function cleanup() {
                    if (analyser) {
                        try {
                            analyser.disconnect();
                        } catch (e) {}
                    }
                    if (source) {
                        try {
                            source.disconnect();
                        } catch (e) {}
                    }
                    if (audioContext) {
                        try {
                            audioContext.close();
                        } catch (e) {}
                    }
                }

                check();
            });
        }
    </script>
</body>

</html>