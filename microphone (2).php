<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎙️ Mic Qua Mạng - Phát triển bởi DevGPT</title>

    <!-- THƯ VIỆN CẦN THIẾT -->
    <script src="https://unpkg.com/peerjs@1.4.7/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>

    <style>
        /* ... (giữ nguyên tất cả CSS cũ) ... */

        /* === STYLES MỚI CHO RANDOM TRÒN 1 PHÚT === */
        .random-round-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a2980 0%, #26d0ce 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .random-round-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .random-round-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 40px;
            text-align: center;
            max-width: 90%;
            width: 400px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
            animation: float 3s ease-in-out infinite;
            border: 3px solid #fff;
        }

        .random-round-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #e53e3e 0%, #d69e2e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .random-round-timer {
            font-size: 72px;
            font-weight: 900;
            color: #2d3748;
            margin: 30px 0;
            text-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            font-family: 'Courier New', monospace;
        }

        .random-round-timer.warning {
            color: #e53e3e;
            animation: pulse 1s infinite;
        }

        .random-round-message {
            font-size: 18px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .random-round-progress {
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
            margin: 25px 0;
        }

        .random-round-progress-bar {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #38a169, #48bb78, #38a169);
            background-size: 200% 100%;
            animation: gradient 2s linear infinite;
            transform-origin: left;
            transition: transform 1s linear;
        }

        .random-round-info {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            padding: 15px;
            border-radius: 12px;
            margin-top: 25px;
            border-left: 5px solid #d69e2e;
            font-size: 14px;
            color: #92400e;
        }

        .random-round-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .random-round-btn {
            flex: 1;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .random-round-btn-continue {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(56, 161, 105, 0.4);
        }

        .random-round-btn-disconnect {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(229, 62, 62, 0.4);
        }

        .random-round-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
        }

        @keyframes countdownPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* === DISCONNECT NOTIFICATION === */
        .disconnect-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(229, 62, 62, 0.5);
            z-index: 3000;
            max-width: 350px;
            transform: translateX(400px);
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.4);
            border: 2px solid #fff;
            backdrop-filter: blur(10px);
        }

        .disconnect-notification.show {
            transform: translateX(0);
        }

        .disconnect-notification-title {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .disconnect-notification-message {
            font-size: 15px;
            opacity: 0.9;
            line-height: 1.5;
        }

        .disconnect-notification-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .disconnect-notification-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        /* === TIMER INDICATOR === */
        .timer-indicator {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #2d3748;
            backdrop-filter: blur(5px);
            border: 2px solid #48bb78;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .timer-indicator.warning {
            border-color: #e53e3e;
            animation: warningFlash 1s infinite;
        }

        @keyframes warningFlash {

            0%,
            100% {
                border-color: #e53e3e;
            }

            50% {
                border-color: #f56565;
            }
        }

        .timer-indicator-icon {
            font-size: 24px;
        }

        .timer-indicator-time {
            font-size: 20px;
            font-family: 'Courier New', monospace;
            min-width: 60px;
            text-align: center;
        }

        /* === AUTO-DISCONNECT WARNING === */
        .auto-disconnect-warning {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: linear-gradient(135deg, #d69e2e 0%, #b7791f 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(214, 158, 46, 0.5);
            z-index: 1000;
            max-width: 90%;
            width: 400px;
            text-align: center;
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.4);
        }

        .auto-disconnect-warning.show {
            transform: translateX(-50%) translateY(0);
        }

        .auto-disconnect-warning-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .auto-disconnect-warning-message {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.5;
        }

        /* ... (giữ nguyên phần CSS còn lại) ... */
    </style>
</head>

<body>
    <div class="container">
        <h1>🎙️ Mic Qua Mạng</h1>

        <!-- ... (giữ nguyên giao diện hiện có) ... -->

    </div>

    <!-- ALERT BOX -->
    <div id="alertOverlay" class="alert-overlay">
        <!-- ... (giữ nguyên) ... -->
    </div>

    <!-- CUSTOM CONFIRM DIALOG -->
    <div id="customConfirm" class="custom-confirm">
        <!-- ... (giữ nguyên) ... -->
    </div>

    <!-- === RANDOM ROUND 1 PHÚT OVERLAY === -->
    <div id="randomRoundOverlay" class="random-round-overlay">
        <div class="random-round-container">
            <div class="random-round-title">⏰ Random Tròn 1 Phút</div>

            <div class="random-round-timer" id="randomRoundTimer">01:00</div>

            <div class="random-round-message" id="randomRoundMessage">
                Thời gian kết nối của bạn còn <span style="font-weight: 800; color: #e53e3e;">1 phút</span>.<br>
                Khi hết giờ, kết nối sẽ tự động ngắt!
            </div>

            <div class="random-round-progress">
                <div class="random-round-progress-bar" id="randomRoundProgressBar"></div>
            </div>

            <div class="random-round-info">
                ⚠️ Tính năng này giúp đảm bảo công bằng cho nhiều người dùng.<br>
                Bạn có thể gia hạn thêm thời gian nếu cần.
            </div>

            <div class="random-round-buttons">
                <button class="random-round-btn random-round-btn-continue" id="continueBtn">
                    <span style="font-size: 20px;">⏳</span>
                    <span>GIA HẠN THÊM 1 PHÚT</span>
                </button>
                <button class="random-round-btn random-round-btn-disconnect" id="disconnectNowBtn">
                    <span style="font-size: 20px;">🔴</span>
                    <span>NGẮT NGAY</span>
                </button>
            </div>
        </div>
    </div>

    <!-- === DISCONNECT NOTIFICATION === -->
    <div id="disconnectNotification" class="disconnect-notification">
        <button class="disconnect-notification-close" onclick="hideDisconnectNotification()">×</button>
        <div class="disconnect-notification-title">
            <span style="font-size: 24px;">🔴</span>
            <span>KẾT NỐI ĐÃ NGẮT</span>
        </div>
        <div class="disconnect-notification-message" id="disconnectNotificationMessage">
            Thời gian sử dụng của bạn đã hết. Kết nối đã tự động ngắt để nhường cho người dùng khác.
        </div>
    </div>

    <!-- === TIMER INDICATOR === -->
    <div id="timerIndicator" class="timer-indicator">
        <span class="timer-indicator-icon">⏰</span>
        <span class="timer-indicator-time" id="timerIndicatorTime">01:00</span>
        <span>Thời gian còn lại</span>
    </div>

    <!-- === AUTO-DISCONNECT WARNING === -->
    <div id="autoDisconnectWarning" class="auto-disconnect-warning">
        <div class="auto-disconnect-warning-title">
            <span style="font-size: 20px;">⚠️</span>
            <span>SẮP HẾT THỜI GIAN!</span>
        </div>
        <div class="auto-disconnect-warning-message">
            Kết nối sẽ tự động ngắt sau <span id="warningSeconds" style="font-weight: 800;">10</span> giây nữa.<br>
            Nhấn "Gia hạn" nếu bạn cần thêm thời gian.
        </div>
    </div>

    <script>
        // ============================================
        // 🎙️ ỨNG DỤNG MIC QUA MẠNG - PHIÊN BẢN HOÀN CHỈNH
        // ============================================

        // --- CẤU HÌNH VÀ BIẾN TOÀN CỤC ---
        const app = {
            peer: null,
            currentCall: null,
            localStream: null,
            audioContext: null,
            analyser: null,
            visualizerFrameId: null,
            receiverAnalyser: null,
            receiverVisualizerFrameId: null,
            ws: null,
            qrVideo: null,
            qrCanvas: null,
            qrCanvasContext: null,
            isConnected: false,
            connectionTimeout: null,
            connectionStartTime: null,
            connectionTimerInterval: null,
            receiverConnectionTimerInterval: null,
            volumeCheckInterval: null,
            receiverVolumeCheckInterval: null,
            debugMode: true,

            // Biến mới cho Random Tròn 1 Phút
            randomRoundTimer: null,
            randomRoundSeconds: 60, // 1 phút = 60 giây
            randomRoundActive: false,
            randomRoundInterval: null,
            autoDisconnectTimeout: null,
            warningTimeout: null
        };

        // ... (giữ nguyên các cấu hình khác) ...

        // --- HÀM RANDOM TRÒN 1 PHÚT ---
        function startRandomRound() {
            debugLog('⏰ Bắt đầu Random Tròn 1 Phút', 'info');

            // Reset thời gian
            app.randomRoundSeconds = 60;
            app.randomRoundActive = true;

            // Hiển thị overlay
            const overlay = document.getElementById('randomRoundOverlay');
            overlay.classList.add('active');

            // Cập nhật timer indicator
            updateTimerIndicator();
            document.getElementById('timerIndicator').style.display = 'flex';

            // Cập nhật tin nhắn
            document.getElementById('randomRoundMessage').innerHTML =
                `Thời gian kết nối của bạn còn <span style="font-weight: 800; color: #e53e3e;">1 phút</span>.<br>
            Khi hết giờ, kết nối sẽ tự động ngắt!`;

            // Reset progress bar
            const progressBar = document.getElementById('randomRoundProgressBar');
            progressBar.style.transform = 'scaleX(1)';

            // Bắt đầu đếm ngược
            startRandomRoundCountdown();

            // Thiết lập cảnh báo ở giây thứ 10
            app.warningTimeout = setTimeout(() => {
                showAutoDisconnectWarning();
            }, 50000); // 50 giây (còn 10 giây)

            // Thiết lập tự động ngắt kết nối
            app.autoDisconnectTimeout = setTimeout(() => {
                autoDisconnect();
            }, 60000); // 60 giây
        }

        function startRandomRoundCountdown() {
            if (app.randomRoundInterval) {
                clearInterval(app.randomRoundInterval);
            }

            app.randomRoundInterval = setInterval(() => {
                app.randomRoundSeconds--;

                // Cập nhật timer
                updateRandomRoundTimer();

                // Cập nhật progress bar
                const progress = app.randomRoundSeconds / 60;
                const progressBar = document.getElementById('randomRoundProgressBar');
                progressBar.style.transform = `scaleX(${progress})`;

                // Hiệu ứng khi còn 10 giây
                if (app.randomRoundSeconds <= 10) {
                    const timerEl = document.getElementById('randomRoundTimer');
                    timerEl.classList.add('warning');
                    timerEl.style.animation = 'countdownPulse 0.5s infinite';

                    const indicator = document.getElementById('timerIndicator');
                    indicator.classList.add('warning');
                }

                // Khi hết giờ
                if (app.randomRoundSeconds <= 0) {
                    clearInterval(app.randomRoundInterval);
                    app.randomRoundInterval = null;
                }
            }, 1000);
        }

        function updateRandomRoundTimer() {
            const minutes = Math.floor(app.randomRoundSeconds / 60);
            const seconds = app.randomRoundSeconds % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            document.getElementById('randomRoundTimer').textContent = timeString;
            document.getElementById('timerIndicatorTime').textContent = timeString;
        }

        function updateTimerIndicator() {
            const minutes = Math.floor(app.randomRoundSeconds / 60);
            const seconds = app.randomRoundSeconds % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            document.getElementById('timerIndicatorTime').textContent = timeString;
        }

        function showAutoDisconnectWarning() {
            const warningEl = document.getElementById('autoDisconnectWarning');
            const warningSecondsEl = document.getElementById('warningSeconds');

            // Cập nhật đếm ngược cảnh báo
            let warningSeconds = 10;
            warningSecondsEl.textContent = warningSeconds;

            const warningInterval = setInterval(() => {
                warningSeconds--;
                warningSecondsEl.textContent = warningSeconds;

                if (warningSeconds <= 0) {
                    clearInterval(warningInterval);
                }
            }, 1000);

            // Hiển thị cảnh báo
            setTimeout(() => {
                warningEl.classList.add('show');
            }, 100);

            // Tự động ẩn sau 10 giây
            setTimeout(() => {
                warningEl.classList.remove('show');
            }, 10000);
        }

        async function autoDisconnect() {
            debugLog('🔴 Tự động ngắt kết nối do hết thời gian', 'warning');

            // Ẩn overlay
            document.getElementById('randomRoundOverlay').classList.remove('active');
            document.getElementById('autoDisconnectWarning').classList.remove('show');
            document.getElementById('timerIndicator').style.display = 'none';

            // Ngắt kết nối
            app.randomRoundActive = false;

            if (app.isConnected) {
                // Hiển thị thông báo ngắt kết nối
                showDisconnectNotification();

                // Ngắt kết nối thực tế
                await disconnect();
            }
        }

        function showDisconnectNotification() {
            const notification = document.getElementById('disconnectNotification');
            notification.classList.add('show');

            // Tự động ẩn sau 8 giây
            setTimeout(() => {
                hideDisconnectNotification();
            }, 8000);
        }

        function hideDisconnectNotification() {
            document.getElementById('disconnectNotification').classList.remove('show');
        }

        function continueRandomRound() {
            debugLog('⏳ Gia hạn thêm 1 phút', 'info');

            // Gia hạn thêm 60 giây
            app.randomRoundSeconds += 60;

            // Reset các timeout
            clearTimeout(app.autoDisconnectTimeout);
            clearTimeout(app.warningTimeout);

            // Đặt lại warning timeout
            app.warningTimeout = setTimeout(() => {
                showAutoDisconnectWarning();
            }, (app.randomRoundSeconds - 10) * 1000);

            // Đặt lại auto disconnect timeout
            app.autoDisconnectTimeout = setTimeout(() => {
                autoDisconnect();
            }, app.randomRoundSeconds * 1000);

            // Cập nhật UI
            document.getElementById('randomRoundMessage').innerHTML =
                `Đã gia hạn thành công! Thời gian còn lại: <span style="font-weight: 800; color: #38a169;">${Math.floor(app.randomRoundSeconds/60)} phút</span>.<br>
            Kết nối sẽ tiếp tục hoạt động bình thường.`;

            // Reset progress bar
            const progressBar = document.getElementById('randomRoundProgressBar');
            progressBar.style.transform = 'scaleX(1)';

            // Ẩn cảnh báo nếu đang hiển thị
            document.getElementById('autoDisconnectWarning').classList.remove('show');

            // Hiệu ứng xác nhận
            const continueBtn = document.getElementById('continueBtn');
            const originalText = continueBtn.innerHTML;
            continueBtn.innerHTML = '<span style="font-size: 20px;">✅</span><span>ĐÃ GIA HẠN!</span>';
            continueBtn.style.background = 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)';

            setTimeout(() => {
                continueBtn.innerHTML = originalText;
                continueBtn.style.background = 'linear-gradient(135deg, #38a169 0%, #2f855a 100%)';
            }, 2000);

            debugLog(`✅ Đã gia hạn thành công. Thời gian mới: ${app.randomRoundSeconds} giây`, 'success');
        }

        // --- MODIFY CONNECT FUNCTION TO START RANDOM ROUND ---
        async function connectToReceiver(receiverId) {
            try {
                showStatus('sender', '🎤 Đang xin quyền truy cập micro...', 'info');
                debugLog('🎤 Đang yêu cầu quyền microphone...', 'info');

                const timeout = setTimeout(() => {
                    if (!app.isConnected) {
                        debugLog('⏱️ Quá thời gian chờ kết nối', 'error');
                        showAlert('⏱️ QUÁ THỜI GIAN CHỜ',
                            'Không thể kết nối đến máy tính sau 5 giây.\n\nNguyên nhân có thể do:\n\n• Máy tính chưa mở trang web\n• Mạng internet có vấn đề\n• Máy tính đã có kết nối khác\n• Tường lửa chặn kết nối');
                        resetUI();
                    }
                }, 5000);

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

                debugLog('✅ Đã lấy được stream microphone', 'success');

                showStatus('sender', '📞 Đang thực hiện cuộc gọi đến máy tính...', 'info');
                const call = app.peer.call(receiverId, app.localStream);
                app.currentCall = call;
                clearTimeout(timeout);

                call.on('stream', (remoteStream) => {
                    debugLog('🎵 Đã nhận stream từ máy tính', 'success');
                    app.isConnected = true;
                    startConnectionTimer();
                    startVolumeCheck();
                    showStatus('sender', '✅ Đã kết nối! Đang gửi âm thanh...', 'connected');

                    // Chuyển sang view đã kết nối
                    document.getElementById('sender-auto-view').classList.add('hidden');
                    document.getElementById('sender-connected-view').classList.remove('hidden');
                    document.getElementById('muteBtn').classList.remove('hidden');
                    document.getElementById('unmuteBtnSender').classList.add('hidden');

                    // Đợi một chút để đảm bảo mọi thứ đã sẵn sàng
                    setTimeout(() => {
                        startVisualizer();
                    }, 300);

                    const connectBtn = document.getElementById('connectBtn');
                    if (connectBtn) {
                        connectBtn.disabled = false;
                        connectBtn.innerHTML = '<span style="font-size: 24px;">🎤</span><span>Kết nối với Máy tính</span>';
                    }

                    // ⭐⭐ BẮT ĐẦU RANDOM TRÒN 1 PHÚT ⭐⭐
                    setTimeout(() => {
                        startRandomRound();
                    }, 1000);
                });

                function stopConnectionTimer() {
                    if (app.connectionTimerInterval) {
                        clearInterval(app.connectionTimerInterval);
                        app.connectionTimerInterval = null;
                        debugLog('⏱️ Đã dừng timer kết nối', 'info');
                    }
                }

                function stopReceiverConnectionTimer() {
                    if (app.receiverConnectionTimerInterval) {
                        clearInterval(app.receiverConnectionTimerInterval);
                        app.receiverConnectionTimerInterval = null;
                    }
                }


            } catch (err) {
                clearTimeout(timeout);
                app.isConnected = false;
                stopConnectionTimer();
                stopVolumeCheck();

                debugLog(`❌ Lỗi kết nối: ${err.message}`, 'error');

                let message = `❌ Lỗi: ${err.message}.`;
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    message = '❌ Bạn đã từ chối quyền truy cập micro. Vui lòng cấp quyền để tiếp tục.';
                }
                showStatus('sender', message, 'error');
                resetUI();
            }
        }
        // --- GẮN SỰ KIỆN CHO CÁC NÚT RANDOM ROUND ---
        document.addEventListener('DOMContentLoaded', () => {
            // Gắn sự kiện cho nút Continue
            const continueBtn = document.getElementById('continueBtn');
            if (continueBtn) {
                continueBtn.onclick = continueRandomRound;
            }

            // Gắn sự kiện cho nút Disconnect Now
            const disconnectNowBtn = document.getElementById('disconnectNowBtn');
            if (disconnectNowBtn) {
                disconnectNowBtn.onclick = async function() {
                    const confirmed = await showConfirm(
                        'Bạn có chắc chắn muốn ngắt kết nối ngay bây giờ?\n\nKết nối sẽ bị ngắt lập tức và bạn không thể tiếp tục sử dụng.',
                        'NGẮT KẾT NỐI NGAY'
                    );

                    if (confirmed) {
                        // Ẩn overlay
                        document.getElementById('randomRoundOverlay').classList.remove('active');
                        document.getElementById('timerIndicator').style.display = 'none';

                        // Dọn dẹp timeout
                        clearTimeout(app.autoDisconnectTimeout);
                        clearTimeout(app.warningTimeout);
                        clearInterval(app.randomRoundInterval);

                        // Ngắt kết nối
                        await disconnect();
                    }
                };
            }
        });

        // --- NÚT NGẮT KẾT NỐI TRÊN ĐIỆN THOẠI ---
        async function disconnect() {
            const confirmed = await showConfirm(
                'Bạn có chắc chắn muốn ngắt kết nối với máy tính?\n\nSau khi ngắt kết nối, bạn cần quét lại mã QR để kết nối lại.',
                'NGẮT KẾT NỐI'
            );

            if (confirmed) {
                debugLog('🔴 Đang ngắt kết nối...', 'info');

                app.isConnected = false;
                clearTimeout(app.connectionTimeout);
                stopConnectionTimer();
                stopVolumeCheck();
                stopVisualizer();

                // Dọn dẹp stream
                if (app.localStream) {
                    debugLog('🛑 Đang dừng local stream...', 'info');
                    app.localStream.getTracks().forEach(track => {
                        track.stop();
                    });
                    app.localStream = null;
                }

                // Đóng kết nối PeerJS
                if (app.currentCall) {
                    debugLog('📞 Đang đóng cuộc gọi...', 'info');
                    app.currentCall.close();
                    app.currentCall = null;
                }

                // Đóng WebSocket
                if (app.ws) {
                    debugLog('🔌 Đang đóng WebSocket...', 'info');
                    app.ws.close();
                    app.ws = null;
                }

                // Reset UI
                resetUI();

                // Hiển thị thông báo
                showStatus('sender', '✅ Đã ngắt kết nối với máy tính', 'info');

                // Hiệu ứng feedback
                setTimeout(() => {
                    showStatus('sender', '📱 Sẵn sàng kết nối mới', 'info');
                }, 1500);

                debugLog('✅ Đã ngắt kết nối hoàn tất', 'success');
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
                    debugLog('🎤 Đã bật micro', 'info');
                    showStatus('sender', '🎤 Đã bật lại micro', 'connected');

                    // Đợi một chút rồi khởi động visualizer
                    setTimeout(() => {
                        if (app.isConnected) {
                            startVisualizer();
                            startVolumeCheck();
                        }
                    }, 300);
                } else {
                    debugLog('🔇 Đã tắt micro', 'info');
                    showStatus('sender', '🔇 Đã tạm dừng micro', 'info');

                    // Dừng visualizer khi tắt micro
                    stopVisualizer();
                    stopVolumeCheck();
                }
            }
        }

        function showStatus(device, message, type) {
            const statusEl = document.getElementById(`${device}Status`);
            if (statusEl) {
                statusEl.textContent = message;
                statusEl.className = `status ${type}`;
                statusEl.classList.add('shake');
                setTimeout(() => {
                    statusEl.classList.remove('shake');
                }, 500);

                debugLog(`📢 Status [${device}]: ${message}`, type);
            }
        }

        function resetUI() {
            app.isConnected = false;
            clearTimeout(app.connectionTimeout);
            stopConnectionTimer();
            stopReceiverConnectionTimer();
            stopVolumeCheck();
            stopReceiverVolumeCheck();
            stopVisualizer();
            stopReceiverVisualizer();

            if (isMobile) {
                document.getElementById('sender-auto-view').classList.add('hidden');
                document.getElementById('sender-connected-view').classList.add('hidden');
                document.getElementById('sender-manual-view').classList.remove('hidden');
                document.getElementById('senderStatus').innerHTML = '';
                const connectBtn = document.getElementById('connectBtn');
                if (connectBtn) {
                    connectBtn.disabled = false;
                    connectBtn.innerHTML = '<span style="font-size: 24px;">🎤</span><span>Kết nối với Máy tính</span>';
                }
                window.history.replaceState({}, document.title, window.location.pathname);
                debugLog('🔄 Đã reset UI cho điện thoại', 'info');
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
                debugLog('🔄 Đã reset UI cho máy tính', 'info');
            }
        }

        function playAudio() {
            const remoteAudio = document.getElementById('remoteAudio');
            remoteAudio.play()
                .then(() => {
                    showStatus('receiver', '🔊 Đang phát âm thanh qua loa!', 'connected');
                    document.getElementById('unmuteBtn').classList.add('hidden');
                    debugLog('🔊 Đã bật âm thanh ra loa', 'success');
                })
                .catch(e => {
                    showStatus('receiver', `❌ Lỗi phát âm thanh: ${e.message}.`, 'error');
                    debugLog(`❌ Lỗi phát âm thanh: ${e.message}`, 'error');
                });
        }

        // --- KIỂM TRA VÀ KHỞI ĐỘNG LẠI VISUALIZER TỰ ĐỘNG ---
        function checkAndRestartVisualizer() {
            if (app.isConnected && app.localStream && app.localStream.active) {
                // Kiểm tra visualizer
                if (!app.visualizerFrameId && document.getElementById('visualizer')) {
                    debugLog('🔄 Tự động khởi động lại visualizer...', 'info');
                    setTimeout(() => {
                        startVisualizer();
                    }, 500);
                }

                // Kiểm tra volume check
                if (!app.volumeCheckInterval) {
                    startVolumeCheck();
                }
            }
        }

        // Chạy kiểm tra định kỳ
        setInterval(checkAndRestartVisualizer, 5000);

        // --- XỬ LÝ KHI ĐÓNG TRANG ---
        window.addEventListener('beforeunload', () => {
            if (app.isConnected) {
                debugLog('🔄 Đang dọn dẹp trước khi đóng trang...', 'warning');
                if (app.currentCall) {
                    app.currentCall.close();
                }
                if (app.localStream) {
                    app.localStream.getTracks().forEach(track => track.stop());
                }
                if (app.ws) {
                    app.ws.close();
                }
            }
        });

        // --- KIỂM TRA DEBUG MODE TRÊN URL ---
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('debug') === '1') {
            app.debugMode = true;
            debugLog('🔧 Chế độ debug đã được bật qua URL', 'info');
        }

        debugLog('✅ Ứng dụng đã sẵn sàng!', 'success');
    </script>
</body>

</html>