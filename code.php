<!DOCTYPE html>
<html lang="vi">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>🎙️ Mic Qua Mạng - Phone Ảo</title>
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
               display: flex;
               align-items: center;
               justify-content: center;
               gap: 10px;
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
               display: flex;
               align-items: center;
               justify-content: center;
               gap: 10px;
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
               padding: 15px;
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
               padding: 15px;
               border-radius: 8px;
               margin: 15px 0;
               font-size: 14px;
               line-height: 1.6;
               border-left: 4px solid #fbbF24;
               text-align: left;
          }

          .success {
               background: #c6f6d5;
               color: #22543d;
               padding: 15px;
               border-radius: 8px;
               margin: 15px 0;
               font-size: 14px;
               line-height: 1.6;
               border-left: 4px solid #48bb78;
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
               background: white;
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
               background: #f7fafc;
               padding: 8px;
               border-radius: 6px;
          }

          .connection-stats {
               background: #f0fff4;
               padding: 12px;
               border-radius: 8px;
               margin: 10px 0;
               font-size: 13px;
               text-align: left;
               border-left: 4px solid #38a169;
          }

          .device-info {
               background: #e6fffa;
               padding: 12px;
               border-radius: 8px;
               margin: 15px 0;
               font-size: 13px;
               border-left: 4px solid #38b2ac;
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

          .step-number {
               display: inline-block;
               width: 24px;
               height: 24px;
               background: #667eea;
               color: white;
               border-radius: 50%;
               text-align: center;
               line-height: 24px;
               margin-right: 10px;
               font-size: 12px;
               font-weight: bold;
          }

          .phone-icon {
               font-size: 24px;
               animation: float 3s ease-in-out infinite;
          }

          @keyframes float {

               0%,
               100% {
                    transform: translateY(0);
               }

               50% {
                    transform: translateY(-5px);
               }
          }
     </style>
</head>

<body>
     <div class="container">
          <h1><span class="phone-icon">📱</span> Phone Ảo - Mic Qua Mạng</h1>

          <!-- ĐIỆN THOẠI (GỬI) -->
          <div id="senderDiv" class="hidden">
               <div id="sender-manual-view">
                    <div class="info">
                         <div class="step-number">1</div>
                         <strong>Quét mã QR trên máy tính</strong><br>
                         Để kết nối điện thoại làm PHONE ẢO cho máy tính
                    </div>

                    <div id="scanner-container" class="hidden">
                         <video id="qr-video" playsinline></video>
                         <canvas id="qr-canvas"></canvas>
                         <div class="scanner-overlay">
                              <div class="scanner-line"></div>
                         </div>
                    </div>

                    <button class="btn btn-primary" id="startScannerBtn">
                         <span>📷</span> Quét QR Code
                    </button>
                    <button class="btn btn-danger hidden" id="stopScannerBtn">
                         <span>🛑</span> Dừng Quét
                    </button>

                    <div class="info" id="scanner-info">
                         <div class="step-number">2</div>
                         Nhấn "Quét QR Code" và hướng camera về mã QR trên máy tính
                    </div>
               </div>

               <div id="sender-auto-view" class="hidden">
                    <div class="info" id="auto-connect-info">
                         <div class="step-number">1</div>
                         Đã nhận thông tin kết nối từ QR code
                    </div>

                    <button class="btn btn-primary" id="connectBtn">
                         <span>🔗</span> Kết nối với PHONE ẢO
                    </button>

                    <div class="token-info" id="senderTokenInfo"></div>
               </div>

               <div id="sender-connected-view" class="hidden">
                    <div class="success">
                         <strong>✅ ĐÃ KẾT NỐI THÀNH CÔNG!</strong><br>
                         Điện thoại của bạn đang hoạt động như PHONE ẢO
                    </div>

                    <div class="connection-stats" id="senderStats">
                         <span>📊</span> Đang gửi âm thanh đến máy tính...
                    </div>

                    <div id="visualizer-container">
                         <canvas id="visualizer"></canvas>
                    </div>

                    <div class="info">
                         <strong>💡 Cách sử dụng:</strong><br>
                         1. Nói vào điện thoại - âm thanh sẽ truyền đến máy tính<br>
                         2. Máy tính sẽ phát âm thanh qua PHONE ẢO<br>
                         3. Chọn PHONE ẢO làm microphone trong ứng dụng của bạn
                    </div>

                    <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                    <button id="disconnectBtnSender" class="btn btn-danger" onclick="disconnectPhone()">
                         <span>🔴</span> Ngắt kết nối PHONE ẢO
                    </button>
               </div>
               <div id="senderStatus"></div>
          </div>

          <!-- MÁY TÍNH (NHẬN) -->
          <div id="receiverDiv" class="hidden">
               <audio id="remoteAudio" playsinline style="display: none;"></audio>

               <div id="receiver-initial-view">
                    <div class="info">
                         <div class="step-number">1</div>
                         <strong>Tạo PHONE ẢO từ điện thoại</strong><br>
                         Dùng Camera điện thoại quét mã QR để biến điện thoại thành PHONE ẢO cho máy tính
                    </div>

                    <div id="qrcode-container">
                         <p>Đang tạo mã QR...</p>
                    </div>

                    <div class="device-info" id="deviceInfo">
                         <strong>🔧 Thiết bị PHONE ẢO:</strong><br>
                         <span id="deviceName">Đang xác định...</span>
                    </div>

                    <div class="warning">
                         <strong>⚠️ Lưu ý quan trọng:</strong><br>
                         1. Hệ thống chỉ cho phép <strong>1 PHONE ẢO tại 1 thời điểm</strong><br>
                         2. Người dùng thứ 2 sẽ nhận thông báo "PHONE ẢO đang bận"<br>
                         3. Sau khi kết nối, chọn thiết bị PHONE ẢO làm microphone trong ứng dụng
                    </div>

                    <div class="token-info" id="receiverTokenInfo">
                         Mỗi QR code chỉ sử dụng được một lần - Tự động làm mới sau 1 phút
                    </div>
               </div>

               <div id="receiver-connected-view" class="hidden">
                    <div class="success">
                         <strong>📱 PHONE ẢO ĐANG HOẠT ĐỘNG!</strong><br>
                         Điện thoại đã kết nối và sẵn sàng sử dụng
                    </div>

                    <div class="connection-stats" id="receiverStats">
                         <span>📊</span> Đang nhận âm thanh từ điện thoại...
                    </div>

                    <div id="visualizer-receiver-container">
                         <canvas id="visualizer-receiver"></canvas>
                    </div>

                    <div class="info">
                         <strong>✅ Thiết lập hoàn tất:</strong><br>
                         1. <strong>Âm thanh</strong>: Điện thoại → Máy tính → PHONE ẢO<br>
                         2. <strong>Speech-to-Text</strong>: Đang chuyển giọng nói thành văn bản<br>
                         3. <strong>Sử dụng</strong>: Chọn PHONE ẢO làm microphone trong ứng dụng
                    </div>

                    <div class="device-info">
                         <strong>🔧 Thiết bị PHONE ẢO đang dùng:</strong><br>
                         <span id="currentDeviceName">Đang xác định...</span><br>
                         <small>Chọn thiết bị này làm microphone trong Zoom/Skype/Game</small>
                    </div>

                    <hr style="margin: 15px 0; border: 1px solid #e2e8f0;">
                    <button class="btn btn-danger" onclick="disconnectReceiver()">
                         <span>🔴</span> Ngắt kết nối PHONE ẢO
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
                    document.title = "📱 Điện Thoại - Phone Ảo";
                    initializeSender();
               } else {
                    document.getElementById('receiverDiv').classList.remove('hidden');
                    document.title = "💻 Máy Tính - Phone Ảo";
                    initializeReceiver();
               }
          });

          // ========================================
          // 💻 MÁY TÍNH - RECEIVER (PHONE ẢO)
          // ========================================
          function initializeReceiver() {
               cleanupReceiver();

               showStatus('receiver', '📡 Đang kết nối đến máy chủ PeerJS...', 'info');
               app.peer = new Peer(PEER_CONFIG);

               app.peer.on('open', id => {
                    console.log(`✅ PeerJS connected. ID: ${id}`);
                    showStatus('receiver', `✅ Sẵn sàng tạo PHONE ẢO!`, 'info');
                    generateNewQRCode();
                    startQrRotation();

                    // Cập nhật thông tin thiết bị
                    updateDeviceInfo();
               });

               app.peer.on('call', call => {
                    // ⛔ KIỂM TRA NẾU ĐÃ CÓ KẾT NỐI - TỪ CHỐI NGƯỜI THỨ 2
                    if (app.currentCall && app.currentCall.open) {
                         console.log('⛔ Từ chối cuộc gọi - PHONE ẢO đang bận');
                         showStatus('receiver', '⚠️ PHONE ẢO đang được sử dụng. Đã từ chối kết nối mới.', 'info');

                         try {
                              call.close();
                         } catch (e) {}
                         return;
                    }

                    console.log('📞 Nhận cuộc gọi PHONE ẢO từ:', call.peer);
                    showStatus('receiver', '📱 Có điện thoại muốn kết nối làm PHONE ẢO...', 'info');

                    app.currentCall = call;

                    call.answer();

                    call.on('stream', remoteStream => {
                         console.log('✅ Nhận được audio stream từ điện thoại');
                         const remoteAudio = document.getElementById('remoteAudio');
                         remoteAudio.srcObject = remoteStream;

                         onReceiverConnectionSuccess();
                         startRemoteVisualizer(remoteStream);
                         connectWebSocketAndMix(remoteStream);
                    });

                    call.on('close', () => {
                         console.log('🔌 Cuộc gọi PHONE ẢO đã đóng');
                         showStatus('receiver', '📴 Điện thoại đã ngắt kết nối PHONE ẢO.', 'info');
                         cleanupReceiverConnection();
                    });

                    call.on('error', err => {
                         console.error('❌ Lỗi cuộc gọi PHONE ẢO:', err);
                         showStatus('receiver', `❌ Lỗi kết nối PHONE ẢO: ${err.message}`, 'error');
                         cleanupReceiverConnection();
                    });
               });

               app.peer.on('error', err => {
                    console.error('❌ Lỗi PeerJS:', err);
                    showStatus('receiver', `❌ Lỗi hệ thống PHONE ẢO: ${err.message}`, 'error');
               });
          }

          function updateDeviceInfo() {
               // Hiển thị thông tin thiết bị
               const deviceInfo = document.getElementById('deviceInfo');
               const deviceName = "PHONE ẢO (VB-CABLE/Virtual Audio)";
               if (deviceInfo) {
                    deviceInfo.innerHTML = `<strong>🔧 Thiết bị PHONE ẢO:</strong><br>${deviceName}`;
               }

               const currentDevice = document.getElementById('currentDeviceName');
               if (currentDevice) {
                    currentDevice.textContent = deviceName;
               }
          }

          function onReceiverConnectionSuccess() {
               document.getElementById('receiver-initial-view').classList.add('hidden');
               document.getElementById('receiver-connected-view').classList.remove('hidden');

               stopQrRotation();

               setTimeout(() => {
                    generateNewQRCode();
               }, 1000);

               showStatus('receiver', '✅ Đã kết nối PHONE ẢO thành công!', 'connected');
               updateReceiverStats('📱 Đang nhận âm thanh từ điện thoại...');
               updateDeviceInfo();
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
                    showStatus('receiver', '🔄 Đã tạo QR code mới cho PHONE ẢO tiếp theo', 'info');
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
               // Lấy microphone của máy tính để trộn âm thanh
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
                    localGain.gain.value = 0.3; // Giảm volume mic máy tính

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

                    console.log("🔄 Đang kết nối WebSocket PHONE ẢO...");
                    // ⭐⭐ PORT 8766 cho PHONE ẢO ⭐⭐
                    app.ws = new WebSocket("ws://localhost:8766");
                    app.ws.binaryType = "arraybuffer";

                    app.ws.onopen = () => {
                         console.log("✅ WebSocket PHONE ẢO đã kết nối!");
                         updateReceiverStats('✅ Đã kết nối đến server PHONE ẢO');

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
                                        console.error("❌ Lỗi gửi audio PHONE ẢO:", error);
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
                                   } else if (data.type === 'DISCONNECT') {
                                        console.log(`⚠️ Server ngắt kết nối: ${data.reason}`);
                                        showStatus('receiver', `⚠️ ${data.message}`, 'warning');
                                        
                                        if (app.currentCall) {
                                             app.currentCall.close();
                                             app.currentCall = null;
                                        }
                                        resetReceiverUI();
                                   }
                              }
                         } catch (error) {}
                    };

                    app.ws.onclose = (event) => {
                         console.log("⚠️ WebSocket PHONE ẢO đã đóng", event.code, event.reason);
                         if (app.heartbeatInterval) {
                              clearInterval(app.heartbeatInterval);
                              app.heartbeatInterval = null;
                         }
                         
                         if (app.currentCall) {
                              app.currentCall.close();
                              app.currentCall = null;
                              showStatus('receiver', '⚠️ Kết nối đã kết thúc (Timeout hoặc Lỗi Server)', 'warning');
                              resetReceiverUI();
                         }
                    };

                    app.ws.onerror = (error) => {
                         console.error("❌ WebSocket PHONE ẢO error:", error);
                         showStatus('receiver', '❌ Lỗi kết nối server PHONE ẢO', 'error');
                    };

               }).catch(err => {
                    console.error("❌ Lỗi truy cập microphone PHONE ẢO:", err);
                    showStatus('receiver', '❌ Không thể truy cập microphone máy tính', 'error');
               });
          }

          // ========================================
          // 📱 ĐIỆN THOẠI - SENDER (PHONE ẢO)
          // ========================================
          function initializeSender() {
               const urlParams = new URLSearchParams(window.location.search);
               const token = urlParams.get('token');
               const peerId = urlParams.get('peer');

               if (token && peerId) {
                    app.currentToken = token;
                    document.getElementById('sender-manual-view').classList.add('hidden');
                    document.getElementById('sender-auto-view').classList.remove('hidden');
                    document.getElementById('senderTokenInfo').textContent = `🔐 Mã kết nối: ${token.substring(0, 8)}...`;
                    document.getElementById('auto-connect-info').innerHTML = `
                    <div class="step-number">1</div>
                    <strong>Đã nhận thông tin PHONE ẢO</strong><br>
                    Sẵn sàng kết nối điện thoại làm PHONE ẢO cho máy tính
                `;

                    const connectBtn = document.getElementById('connectBtn');
                    connectBtn.onclick = () => {
                         connectBtn.disabled = true;
                         connectBtn.innerHTML = '<span>⏳</span> Đang kết nối PHONE ẢO...';
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
                    showStatus('sender', '🎤 Đang xin quyền microphone điện thoại...', 'info');

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

                    showStatus('sender', '📡 Đang kết nối đến PHONE ẢO...', 'info');

                    if (app.peer) app.peer.destroy();
                    app.peer = new Peer(PEER_CONFIG);

                    app.peer.on('open', () => {
                         console.log(`📞 Gọi đến PHONE ẢO: ${receiverId}`);
                         const call = app.peer.call(receiverId, app.localStream, {
                              metadata: {
                                   token: token,
                                   type: 'phone-ao'
                              }
                         });

                         app.currentCall = call;
                         onSenderConnectionSuccess();

                         call.on('stream', remoteStream => {
                              console.log('✅ Nhận được stream từ PHONE ẢO');
                         });

                         call.on('close', () => {
                              console.log('🔌 Kết nối PHONE ẢO bị đóng');
                              showStatus('sender', '📴 Máy tính đã ngắt kết nối PHONE ẢO.', 'info');
                              exitAppOnDisconnect();
                         });

                         call.on('error', (err) => {
                              console.error('❌ Lỗi kết nối PHONE ẢO:', err);

                              let errorMessage = '❌ KẾT NỐI PHONE ẢO THẤT BẠI';
                              let errorDetail = '';

                              if (err.type === 'peer-unavailable') {
                                   errorMessage = '❌ PHONE ẢO không khả dụng';
                                   errorDetail = 'QR code đã hết hạn. Vui lòng quét mã mới.';
                              } else if (err.type === 'busy' || err.message.includes('busy')) {
                                   errorMessage = '❌ PHONE ẢO ĐANG BẬN';
                                   errorDetail = '⚠️ <strong>PHONE ẢO đang được sử dụng bởi người khác.</strong><br><br>' +
                                        'Hệ thống chỉ cho phép 1 PHONE ẢO tại 1 thời điểm.<br><br>' +
                                        'Vui lòng chờ và thử lại sau khi người dùng hiện tại hoàn thành.<br><br>' +
                                        '⏳ QR code mới sẽ xuất hiện tự động trên máy tính.';
                              } else if (err.message.includes('NotAllowedError')) {
                                   errorMessage = '❌ Từ chối quyền microphone';
                                   errorDetail = 'Bạn cần cấp quyền truy cập microphone để sử dụng PHONE ẢO.';
                              } else {
                                   errorDetail = `Lỗi PHONE ẢO: ${err.message}`;
                              }

                              showErrorScreen(errorMessage, errorDetail);
                         });
                    });

                    app.peer.on('error', err => {
                         console.error('❌ Lỗi PeerJS PHONE ẢO:', err);
                         showErrorScreen('❌ Lỗi hệ thống PHONE ẢO', `Lỗi kết nối: ${err.message}`);
                    });

               } catch (err) {
                    console.error('❌ Lỗi kết nối PHONE ẢO:', err);
                    let message = `❌ Lỗi PHONE ẢO: ${err.message}`;
                    let detail = '';

                    if (err.name === 'NotAllowedError') {
                         message = '❌ Từ chối quyền microphone';
                         detail = 'Vui lòng cấp quyền truy cập microphone để sử dụng PHONE ẢO.';
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
               showStatus('sender', '✅ Đã kết nối PHONE ẢO thành công!', 'connected');
               startVisualizer();
               updateSenderStats('📱 Đang gửi âm thanh đến PHONE ẢO...');
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
                    <h2 style="color: #4e4376; margin-bottom: 20px;">✅ PHONE ẢO ĐÃ NGẮT KẾT NỐI</h2>
                    
                    <div style="font-size: 80px; margin: 20px 0; color: #667eea;" class="pulse">
                        📱
                    </div>
                    
                    <p style="margin-bottom: 15px; line-height: 1.5;">
                        <strong>Phiên PHONE ẢO đã kết thúc</strong>
                    </p>
                    
                    <p style="margin-bottom: 25px; color: #718096; font-size: 14px;">
                        Cảm ơn bạn đã sử dụng dịch vụ PHONE ẢO.
                    </p>
                    
                    <div style="background: #f7fafc; border-radius: 10px; padding: 15px; margin-top: 20px;">
                        <p style="margin: 0; color: #2d3748; font-size: 13px;">
                            Ứng dụng sẽ tự động đóng trong vài giây...
                        </p>
                    </div>
                    
                    <button onclick="window.close()" class="btn btn-danger" style="margin-top: 20px; padding: 12px 24px;">
                        <span>❌</span> Đóng ngay
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
                            <span>🔄</span> Thử lại
                        </button>
                        
                        <button onclick="window.close()" class="btn btn-danger" 
                            style="margin: 5px; padding: 12px 24px; width: 45%;">
                            <span>❌</span> Đóng
                        </button>
                    </div>
                    
                    <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                        <p style="color: #718096; font-size: 12px;">
                            <strong>💡 Thông tin PHONE ẢO:</strong> Hệ thống chỉ cho phép 1 PHONE ẢO tại 1 thời điểm.
                            Vui lòng chờ lượt của bạn.
                        </p>
                    </div>
                </div>
            </div>`;
          }

          function disconnectPhone() {
               showStatus('sender', '🔄 Đang ngắt kết nối PHONE ẢO...', 'info');
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
                         `🔐 Mã PHONE ẢO: ${app.currentToken.substring(0, 16)}...<br>
                     ⏱️ QR code sẽ thay đổi sau 1 phút`;

                    console.log("✅ Đã tạo QR Code PHONE ẢO mới");
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
                         console.log('🔄 QR code PHONE ẢO đã được thay đổi');
                         showStatus('receiver', '🔄 Đã làm mới QR code PHONE ẢO', 'info');
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
                    showStatus('sender', '🔄 Đang khởi động camera PHONE ẢO...', 'info');
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
                    document.getElementById('scanner-info').innerHTML = `
                    <div class="step-number">2</div>
                    <strong>Đang quét mã QR PHONE ẢO...</strong><br>
                    Hướng camera về mã QR trên máy tính
                `;

                    requestAnimationFrame(scanQRCode);
               } catch (error) {
                    let message = 'Lỗi không xác định';
                    if (error.name === 'NotAllowedError') {
                         message = '❌ Quyền truy cập camera bị từ chối. Vui lòng cho phép camera để quét QR code PHONE ẢO.';
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
               document.getElementById('scanner-info').innerHTML = `
                <div class="step-number">2</div>
                Nhấn "Quét QR Code" và hướng camera về phía mã QR PHONE ẢO trên máy tính
            `;
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
               console.log('✅ Đã quét được QR code PHONE ẢO:', qrData);

               try {
                    const url = new URL(qrData);
                    const token = url.searchParams.get('token');
                    const peerId = url.searchParams.get('peer');

                    if (token && peerId) {
                         stopQRScanner();
                         showStatus('sender', '✅ Đã quét thành công mã PHONE ẢO!', 'connected');

                         setTimeout(() => {
                              window.location.href = `?token=${token}&peer=${peerId}`;
                         }, 1000);
                    } else {
                         showStatus('sender', '❌ QR code PHONE ẢO không hợp lệ', 'error');
                    }
               } catch (e) {
                    showStatus('sender', '❌ QR code PHONE ẢO không hợp lệ', 'error');
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
                    console.error('Không thể tạo visualizer PHONE ẢO:', e);
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
               console.log(`[${device.toUpperCase()} - PHONE ẢO] ${message}`);
          }

          function updateSenderStats(message) {
               const statsEl = document.getElementById('senderStats');
               if (statsEl) {
                    statsEl.innerHTML = `<span>📊</span> ${message}`;
               }
          }

          function updateReceiverStats(message) {
               const statsEl = document.getElementById('receiverStats');
               if (statsEl) {
                    statsEl.innerHTML = `<span>📊</span> ${message}`;
               }
          }
     </script>
</body>

</html>