import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Đóng gói việc khởi tạo Echo vào một hàm để thiết lập theo nhu cầu
window.initWebSocket = function() {
    if (!window.Echo) {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            // Thay vì dùng VITE_REVERB_HOST (thường bị dính 127.0.0.1 khi build ở local), ta linh động theo URL đang chạy
            wsHost: window.location.hostname,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
        console.log('Khởi tạo kết nối Echo/Reverb thành công, kết nối tới: ' + window.location.hostname);
    }
};

// --- CHỈ BẬT TỰ ĐỘNG WEBSOCKET KHI ĐANG Ở TRANG TRẠM IN ---
if (window.location.pathname.includes('/print-station')) {
    window.initWebSocket();
}
