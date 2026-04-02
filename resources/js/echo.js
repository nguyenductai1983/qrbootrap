import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Đóng gói việc khởi tạo Echo vào một hàm để thiết lập theo nhu cầu
window.initWebSocket = function () {
    if (!window.Echo) {
        // Tự động kiểm tra xem web đang chạy http hay https
        const isSecure = window.location.protocol === 'https:';

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: window.location.hostname,

            // 🌟 CHỐT CỨNG CỔNG CHUẨN CỦA WEB, KHÔNG DÙNG 8080 Ở FRONTEND NỮA
            wsPort: 80,
            wssPort: 443,

            // 🌟 BẬT BẢO MẬT DỰA VÀO URL HIỆN TẠI
            forceTLS: isSecure,

            enabledTransports: ['ws', 'wss'],
        });

        console.log('Khởi tạo kết nối Echo/Reverb thành công, kết nối tới: ' + window.location.hostname + (isSecure ? ' (Bảo mật WSS)' : ' (WS)'));
    }
};

// --- CHỈ BẬT TỰ ĐỘNG WEBSOCKET KHI ĐANG Ở TRANG TRẠM IN ---
if (window.location.pathname.includes('/print-station')) {
    window.initWebSocket();
}