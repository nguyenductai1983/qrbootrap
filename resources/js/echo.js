import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.initWebSocket = function () {
    if (!window.Echo) {
        // 🌟 Tự động kiểm tra xem web đang chạy HTTP (Local) hay HTTPS (Server)
        const isSecure = window.location.protocol === 'https:';
        const isLocalHost = window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost' || window.location.hostname.endsWith('.test');

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: window.location.hostname,

            // Trực tiếp trỏ về cổng Reverb 8080 nếu là Localhost/.test
            wsPort: isLocalHost ? 8080 : (isSecure ? 80 : 8080),
            wssPort: isLocalHost ? 8080 : (isSecure ? 443 : 8080),

            // Bật forceTLS để ép trình duyệt nói chuyện với Reverb thông qua HTTPS (wss://)
            forceTLS: isSecure,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });

        console.log('Echo/Reverb connected to: ' + window.location.hostname + (isLocalHost ? ' (Port:8080)' : (isSecure ? ' (WSS:443)' : ' (WS:8080)')));
    }
};

// --- CHỈ BẬT TỰ ĐỘNG WEBSOCKET KHI ĐANG Ở TRANG TRẠM IN (KIOSK) ---
if (window.location.pathname.startsWith('/print-station')) {
    window.initWebSocket();
}