import os

file_path = r'c:\xampp\htdocs\mix\code.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

target = """                    app.ws.onmessage = (event) => {
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
                         console.log("⚠️ WebSocket PHONE ẢO đã đóng");
                         if (app.heartbeatInterval) {
                              clearInterval(app.heartbeatInterval);
                              app.heartbeatInterval = null;
                         }
                    };"""

replacement = """                    app.ws.onmessage = (event) => {
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
                    };"""

if target in content:
    new_content = content.replace(target, replacement)
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("SUCCESS")
else:
    print("FAIL: Target not found")
    idx = content.find("app.ws.onmessage")
    if idx != -1:
        print("Found start at index:", idx)
        print("Actual content snippet:")
        print(repr(content[idx:idx+len(target)]))
