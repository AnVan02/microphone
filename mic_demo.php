<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng (Tự động ngắt kết nối)</title>
    <!-- THƯ VIỆN CẦN THIẾT -->
    <script src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
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

        /* Auto-disconnect timer */
        .auto-disconnect-timer {
            background: #fff5f5;
            border: 2px solid #fed7d7;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 14px;
        }

        .session-info {
            background: #f0fff4;
            border: 2px solid #9ae6b4;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🎙️ Mic Qua Mạng</h1>
        <div class="session-info" id="sessionTimer">⏰ Phiên: Đang chờ...</div>

        <!-- === GIAO DIỆN ĐIỆN THOẠI (GỬI) === -->
        <div id="senderDiv" class="hidden">
            <div id="sender-manual-view">
                <div class="info">Để kết nối, hãy dùng Camera trên điện thoại của bạn quét mã QR hiển thị trên màn hình
                    máy tính.</div>
                <button class="btn btn-primary" id="startScannerBtn">📷 Quét QR Code</button>
                <div class="auto-disconnect-timer">
                    ⏰ Tự động ngắt sau: <span id="senderAutoDisconnect">05:00</span>
                </div>
            </div>
            <div id="sender-auto-view" class="hidden">
                <button class="btn btn-primary" id="connectBtn">🎤 Kết nối với Máy tính</button>
                <div class="auto-disconnect-timer">
                    ⏰ Tự động ngắt sau: <span id="senderConnectTimer">05:00</span>
                </div>
            </div>
            <div id="sender-connected-view" class="hidden">
                <div class="session-info">
                    ✅ Đã kết nối! Đang gửi âm thanh...
                    <br>⏰ Tự động ngắt sau: <span id="senderActiveTimer">05:00</span>
                </div>
                <div>
                    <button id="muteBtn" class="btn btn-warning" onclick="toggleMicrophone(false)">🔇 Tạm dừng âm thanh</button>
                    <button id="unmuteBtnSender" class="btn btn-secondary hidden" onclick="toggleMicrophone(true)">🎤 Bật lại âm thanh</button>
                </div>
                <button class="btn btn-danger" onclick="disconnect()">🔴 Dừng Kết Nối</button>
            </div>
            <div id="senderStatus"></div>
        </div>

        <!-- === GIAO DIỆN MÁY TÍNH (NHẬN) === -->
        <div id="receiverDiv" class="hidden">
            <audio id="remoteAudio" playsinline style="display: none;"></audio>
            <div id="receiver-initial-view">
                <div class="info">Dùng Camera điện thoại quét mã QR này để kết nối và biến nó thành micro không dây cho máy tính.</div>
                <div class="auto-disconnect-timer">
                    ⏰ Tự động reset sau: <span id="receiverAutoReset">05:00</span>
                </div>
                <div id="qrcode-container">
                    <p>Đang kết nối đến máy chủ...</p>
                </div>
            </div>
            <div id="receiver-connected-view" class="hidden">
                <div class="session-info">
                    ✅ Đã kết nối! Âm thanh từ điện thoại đang được nhận.
                    <br>⏰ Tự động reset sau: <span id="receiverActiveTimer">05:00</span>
                </div>
                <button id="unmuteBtn" class="btn btn-secondary" onclick="playAudio()">🔊 Bật Âm Thanh Ra Loa</button>
                <button class="btn btn-danger" onclick="disconnect()">🔴 Reset Kết Nối</button>
            </div>
            <div id="receiverStatus"></div>
        </div>
    </div>

    <script>
        // --- CẤU HÌNH TỰ ĐỘNG NGẮT KẾT NỐI ---
        const AUTO_DISCONNECT_TIME = 5 * 60 * 1000; // 5 phút (có thể điều chỉnh)
        const app = {
            peer: null,
            currentCall: null,
            localStream: null,
            audioContext: null,
            analyser: null,
            visualizerFrameId: null,
            ws: null,
            // TIMERS
            disconnectTimer: null,
            sessionStartTime: null,
            currentSessionId: null
        };

        const PEER_CONFIG = {
            host: '0.peerjs.com',
            port: 443,
            secure: true,
            path: '/'
        };

        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

        // --- KHỞI TẠO PHIÊN MỚI ---
        function generateSessionId() {
            return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        function startNewSession() {
            app.currentSessionId = generateSessionId();
            app.sessionStartTime = Date.now();
            updateSessionTimer();

            // Hiển thị thông tin phiên
            document.getElementById('sessionTimer').textContent =
                `⏰ Phiên: ${app.currentSessionId.substring(0, 8)}... | Bắt đầu: ${new Date().toLocaleTimeString()}`;
        }

        function updateSessionTimer() {
            if (app.sessionStartTime) {
                const elapsed = Math.floor((Date.now() - app.sessionStartTime) / 1000);
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                document.getElementById('sessionTimer').textContent =
                    `⏰ Phiên: ${app.currentSessionId.substring(0, 8)}... | Thời gian: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }

        // --- TỰ ĐỘNG NGẮT KẾT NỐI ---
        function startAutoDisconnectTimer() {
            stopAutoDisconnectTimer(); // Dừng timer cũ nếu có

            let timeLeft = AUTO_DISCONNECT_TIME;
            const timerElement = isMobile ?
                document.getElementById('senderActiveTimer') :
                document.getElementById('receiverActiveTimer');

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60000);
                const seconds = Math.floor((timeLeft % 60000) / 1000);

                if (timerElement) {
                    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }

                if (timeLeft <= 0) {
                    autoDisconnect();
                } else {
                    timeLeft -= 1000;
                    app.disconnectTimer = setTimeout(updateTimer, 1000);
                }
            }

            updateTimer();
        }

        function stopAutoDisconnectTimer() {
            if (app.disconnectTimer) {
                clearTimeout(app.disconnectTimer);
                app.disconnectTimer = null;
            }
        }

        function autoDisconnect() {
            console.log('🔄 Tự động ngắt kết nối sau thời gian chờ');
            showStatus(isMobile ? 'sender' : 'receiver',
                '🔄 Tự động ngắt kết nối để sẵn sàng cho phiên mới', 'info');
            disconnect();

            // Tự động khởi tạo lại sau 2 giây
            setTimeout(() => {
                if (isMobile) {
                    initializeSender();
                } else {
                    initializeReceiver();
                }
                showStatus(isMobile ? 'sender' : 'receiver',
                    '✅ Đã sẵn sàng cho kết nối mới!', 'connected');
            }, 2000);
        }

        // --- PHÁT HIỆN HOẠT ĐỘNG ---
        function setupActivityDetection() {
            // Reset timer khi có hoạt động
            const activityEvents = ['click', 'mousemove', 'keypress', 'touchstart', 'speaking'];

            activityEvents.forEach(event => {
                document.addEventListener(event, () => {
                    if (app.currentCall || app.localStream) {
                        startAutoDisconnectTimer(); // Reset timer
                    }
                });
            });

            // Phát hiện âm thanh (cho điện thoại)
            if (isMobile && app.localStream) {
                setupAudioActivityDetection();
            }
        }

        function setupAudioActivityDetection() {
            if (!app.audioContext && app.localStream) {
                app.audioContext = new(window.AudioContext || window.webkitAudioContext)();
                app.analyser = app.audioContext.createAnalyser();
                const source = app.audioContext.createMediaStreamSource(app.localStream);
                source.connect(app.analyser);
                app.analyser.fftSize = 256;
            }

            function checkAudioLevel() {
                if (app.analyser && app.localStream) {
                    const dataArray = new Uint8Array(app.analyser.frequencyBinCount);
                    app.analyser.getByteFrequencyData(dataArray);

                    const average = dataArray.reduce((a, b) => a + b) / dataArray.length;
                    if (average > 10) { // Ngưỡng âm thanh
                        startAutoDisconnectTimer(); // Reset timer khi có âm thanh
                    }

                    requestAnimationFrame(checkAudioLevel);
                }
            }

            if (app.localStream) {
                checkAudioLevel();
            }
        }

        // --- LOGIC CHÍNH ---
        document.addEventListener('DOMContentLoaded', () => {
            startNewSession();
            setInterval(updateSessionTimer, 1000);

            if (isMobile) {
                document.getElementById('senderDiv').classList.remove('hidden');
                initializeSender();
            } else {
                document.getElementById('receiverDiv').classList.remove('hidden');
                initializeReceiver();
            }

            setupActivityDetection();
        });

        // --- LOGIC MÁY TÍNH (NHẬN) ---
        function initializeReceiver() {
            if (app.peer) app.peer.destroy();
            showStatus('receiver', 'Đang kết nối đến máy chủ PeerJS...', 'info');
            app.peer = new Peer(PEER_CONFIG);

            app.peer.on('open', id => {
                showStatus('receiver', `Sẵn sàng! ID: ${id} - Chờ điện thoại kết nối...`, 'info');
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

                // Start auto-reset timer
                startAutoDisconnectTimer();
            });

            app.peer.on('call', call => {
                showStatus('receiver', '📲 Có cuộc gọi đến, đang kết nối...', 'info');
                app.currentCall = call;
                call.answer();

                call.on('stream', remoteStream => {
                    const remoteAudio = document.getElementById('remoteAudio');
                    remoteAudio.srcObject = remoteStream;
                    document.getElementById('receiver-initial-view').classList.add('hidden');
                    document.getElementById('receiver-connected-view').classList.remove('hidden');
                    showStatus('receiver', '✅ Đã kết nối với điện thoại!', 'connected');

                    // Bắt đầu timer khi có kết nối
                    startAutoDisconnectTimer();
                    connectWebSocketAndMix(remoteStream);
                });

                call.on('close', () => {
                    handleConnectionEnd('receiver', '🚫 Điện thoại đã ngắt kết nối');
                });

                call.on('error', err => {
                    handleConnectionEnd('receiver', `❌ Lỗi: ${err.message}`);
                });
            });

            app.peer.on('error', err => {
                showStatus('receiver', `❌ Lỗi kết nối: ${err.message}`, 'error');
            });
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
                    app.peer.on('error', err => showStatus('sender', `❌ Lỗi: ${err.message}`, 'error'));
                };

                startAutoDisconnectTimer();
            } else {
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('sender-auto-view').classList.add('hidden');
                startAutoDisconnectTimer();
            }
        }

        async function connectToReceiver(receiverId) {
            try {
                showStatus('sender', 'Đang xin quyền truy cập micro...', 'info');
                app.localStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        channelCount: 1,
                        sampleRate: 48000
                    },
                    video: false
                });

                showStatus('sender', 'Đang kết nối đến máy tính...', 'info');
                const call = app.peer.call(receiverId, app.localStream);
                app.currentCall = call;

                showStatus('sender', '✅ Đã kết nối! Đang gửi âm thanh...', 'connected');
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.remove('hidden');

                startAutoDisconnectTimer();
                setupAudioActivityDetection();

                call.on('close', () => {
                    handleConnectionEnd('sender', '🚫 Máy tính đã ngắt kết nối');
                });

                call.on('error', (err) => {
                    handleConnectionEnd('sender', `❌ Lỗi: ${err.message}`);
                });

            } catch (err) {
                showStatus('sender', `❌ Lỗi: ${err.message}`, 'error');
                resetUI();
            }
        }

        function handleConnectionEnd(device, message) {
            showStatus(device, message, 'info');
            stopAutoDisconnectTimer();

            // Tự động reset sau 3 giây
            setTimeout(() => {
                resetUI();
                if (device === 'receiver') {
                    initializeReceiver();
                } else {
                    initializeSender();
                }
                showStatus(device, '✅ Đã sẵn sàng cho kết nối mới!', 'connected');
            }, 3000);
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
                    startAutoDisconnectTimer(); // Reset timer khi bật mic
                }
            }
        }

        function showStatus(device, message, type) {
            const statusEl = document.getElementById(`${device}Status`);
            if (statusEl) {
                statusEl.textContent = message;
                statusEl.className = `status ${type}`;
            }
            console.log(`[${device.toUpperCase()}] ${message}`);
        }

        function disconnect() {
            console.log('🔄 Người dùng yêu cầu ngắt kết nối');
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
            stopAutoDisconnectTimer();
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
            }
        }

        function playAudio() {
            const remoteAudio = document.getElementById('remoteAudio');
            remoteAudio.play().then(() => {
                showStatus('receiver', '✅ Đang phát âm thanh qua loa!', 'connected');
                startAutoDisconnectTimer(); // Reset timer khi có hoạt động
            }).catch(e => showStatus('receiver', `❌ Lỗi: ${e.message}`, 'error'));
        }

        // WebSocket và Visualizer functions giữ nguyên...
        function connectWebSocketAndMix(remoteStream) {
            // ... (giữ nguyên code WebSocket từ trước)
        }

        function startVisualizer() {
            // ... (giữ nguyên code visualizer từ trước)
        }

        function stopVisualizer() {
            // ... (giữ nguyên code visualizer từ trước)
        }
    </script>
</body>

</html>