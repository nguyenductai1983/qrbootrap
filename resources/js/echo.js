import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.initWebSocket = function () {
    if (!window.Echo) {
        // 🌟 Tự động kiểm tra xem web đang chạy HTTP (Local) hay HTTPS (Server)
        const isSecure = window.location.protocol === 'https:';

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: window.location.hostname,

            // 🌟 LOGIC THÔNG MINH:
            // - Ở Server (isSecure = true) -> Dùng cổng 443/80
            // - Ở Local (isSecure = false) -> Dùng cổng 8080 gốc của Reverb
            wsPort: isSecure ? 80 : 8080,
            wssPort: isSecure ? 443 : 8080,

            forceTLS: isSecure,
            enabledTransports: ['ws', 'wss'],
        });

        console.log('Echo/Reverb connected to: ' + window.location.hostname + (isSecure ? ' (WSS:443)' : ' (WS:8080)'));
    }
};

// --- CHỈ BẬT TỰ ĐỘNG WEBSOCKET KHI ĐANG Ở TRANG TRẠM IN ---
if (window.location.pathname.includes('/print-station')) {
    window.initWebSocket();
}