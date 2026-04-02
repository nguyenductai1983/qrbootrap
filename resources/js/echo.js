import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Đóng gói việc khởi tạo Echo vào một hàm để thiết lập theo nhu cầu
window.initWebSocket = function() {
    if (!window.Echo) {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
        console.log('Khởi tạo kết nối Echo/Reverb thành công!');
    }
};

// --- CHỈ BẬT TỰ ĐỘNG WEBSOCKET KHI ĐANG Ở TRANG TRẠM IN ---
if (window.location.pathname.includes('/print-station')) {
    window.initWebSocket();
}
