<!DOCTYPE html>
<html lang="vi" data-bs-theme="dark">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Trạm In - {{ strtoupper($station_id) }} - Xưởng Tráng</title>

        {{-- Chèn CSS & JS của Laravel (Có chứa Laravel Echo) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Sử dụng local CSS & JS --}}
        <link rel="stylesheet" href="{{ asset('css/fontawesome/css/all.min.css') }}">
        <script src="{{ asset('js/sweetalert2@11.js') }}"></script>
        {{-- QRCode library --}}
        <script src="{{ asset('js/qrcode.min.js') }}"></script>

        <style>
            body {
                background-color: #121212;
                color: #fff;
            }

            .log-box {
                max-height: 70vh;
                overflow-y: auto;
            }

            /* --- CẤU HÌNH GIAO DIỆN KHI MÁY IN KÍCH HOẠT --- */
            @media print {

                /* 1. Ẩn toàn bộ giao diện Web (Nền đen, cột trái phải, menu...) */
                body>*:not(#print-area) {
                    display: none !important;
                }

                /* 2. Chỉ hiển thị duy nhất vùng #print-area để in ra giấy (Nền trắng, chữ đen) */
                #print-area {
                    display: flex !important;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    background-color: white !important;
                    color: black !important;
                    padding: 10px;
                }
            }
        </style>
    </head>

    <body>
        <!-- KHU VỰC IN (Bình thường sẽ ẩn, chỉ hiện lên mặt giấy in) -->
        <div id="print-area" style="display: none;">
            <div id="qrcode-wrapper"></div>
            <div id="print-text"
                style="margin-top: 10px; font-family: monospace; font-weight: bold; font-size: 16px; text-align: center;">
            </div>
            <div id="print-length" style="font-family: monospace; font-size: 14px; text-align: center;"></div>
        </div>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow bg-dark text-white border-top border-4 border-success">
                        <div
                            class="card-header bg-dark border-bottom border-secondary d-flex justify-content-between align-items-center py-3">
                            <h4 class="mb-0 fw-bold text-success">
                                <i class="fa-solid fa-print me-2"></i> Trạm in: <span class="text-warning">{{ strtoupper($station_id) }}</span>
                            </h4>
                            <div class="d-flex align-items-center">
                                <div class="spinner-grow spinner-grow-sm text-danger me-2" role="status"></div>
                                <span class="text-light fw-bold small">Đang kết nối Reverb...</span>
                            </div>
                        </div>

                        <!-- THANH CÀI ĐẶT FONT CHỮ IN THEO YÊU CẦU -->
                        <div class="bg-black p-2 border-bottom border-secondary d-flex justify-content-around flex-wrap gap-2 text-warning"
                            style="font-size: 0.85rem">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-text-height"></i> Cỡ chữ "Mã Tem":
                                <input type="number" id="conf-text"
                                    class="form-control form-control-sm bg-dark text-white border-secondary"
                                    style="width: 60px" min="8" max="60" value="16">
                            </div>
                            {{-- <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-text-width"></i> Cỡ chữ "Mét":
                                <input type="number" id="conf-length" class="form-control form-control-sm bg-dark text-white border-secondary" style="width: 60px" min="8" max="60" value="14">
                            </div> --}}
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-qrcode"></i> Cỡ mã QR (Vuông):
                                <input type="number" id="conf-qr"
                                    class="form-control form-control-sm bg-dark text-white border-secondary"
                                    style="width: 70px" min="50" max="300" step="10" value="120">
                            </div>
                        </div>

                        <div class="card-body log-box p-0">
                            <ul id="print-logs" class="list-group list-group-flush">
                                <li class="list-group-item bg-dark text-secondary text-center py-4" id="empty-log">
                                    <i class="fa-solid fa-satellite-dish fa-3x mb-3 opacity-50"></i><br>
                                    Đang chờ lệnh in từ quét mã tráng
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kịch bản JS lắng nghe WebSocket --}}
        <script type="module">
            // Đảm bảo Echo đã sẵn sàng
            document.addEventListener('DOMContentLoaded', function() {

                // NẠP VÀ LƯU CẤU HÌNH FONT CHỮ VÀO BỘ NHỚ TRÌNH DUYỆT (Nhớ Size)
                const confText = document.getElementById('conf-text');
                const confLength = document.getElementById('conf-length');
                const confQr = document.getElementById('conf-qr');

                if (localStorage.getItem('print_conf_text') && confText) confText.value = localStorage.getItem(
                    'print_conf_text');
                if (localStorage.getItem('print_conf_length') && confLength) confLength.value = localStorage.getItem(
                    'print_conf_length');
                if (localStorage.getItem('print_conf_qr') && confQr) confQr.value = localStorage.getItem(
                    'print_conf_qr');

                const savePrintConfig = () => {
                    if (confText) localStorage.setItem('print_conf_text', confText.value);
                    if (confLength) localStorage.setItem('print_conf_length', confLength.value);
                    if (confQr) localStorage.setItem('print_conf_qr', confQr.value);
                };
                if (confText) confText.addEventListener('input', savePrintConfig);
                if (confLength) confLength.addEventListener('input', savePrintConfig);
                if (confQr) confQr.addEventListener('input', savePrintConfig);

                // Đẩy hàm vào đợi Echo khởi tạo xong
                const subscribeToPrinter = () => {
                    if (window.Echo) {
                        // Bắt tín hiệu kết nối thành công để tắt Spinner đỏ
                        window.Echo.connector.pusher.connection.bind('connected', () => {
                            const statusDiv = document.querySelector(
                                '.card-header .d-flex.align-items-center');
                            if (statusDiv) statusDiv.innerHTML =
                                '<span class="text-success fw-bold small"><i class="fa-solid fa-circle text-success me-1"></i> Đã kết nối với Reverb</span>';
                        });

                        window.Echo.channel('printer.{{ $station_id }}')
                            .listen('.print.command', (event) => {
                                // Dấu chấm '.' ở trước 'print.command' là BẮT BUỘC vì ta dùng broadcastAs() trong PHP

                                console.log("Đã nhận lệnh in!", event);

                                // Xóa dòng "Đang chờ lệnh"
                                const emptyLog = document.getElementById('empty-log');
                                if (emptyLog) emptyLog.remove();

                                // Thêm dòng log mới lên đầu danh sách
                                const logContainer = document.getElementById('print-logs');
                                const time = new Date().toLocaleTimeString('vi-VN');

                                const newItemHTML = `
                        <li class="list-group-item bg-dark border-secondary text-white border-start border-4 border-success">
                            <div class="d-flex justify-content-between">
                                <span class="text-success fw-bold">
                                    <i class="fa-solid fa-check-circle me-1"></i> NHẬN LỆNH LÚC ${time}
                                </span>
                            </div>
                            <div class="mt-2">
                                <b>Mã tem:</b> <span class="text-warning fs-5">${event.item.code}</span><br>
                            </div>
                        </li>
                    `;

                                logContainer.insertAdjacentHTML('afterbegin', newItemHTML);
                                //<b>Chiều dài:</b> ${event.item.length} mét
                                // TODO: GỌI HÀM ĐẨY RA MÁY IN VẬT LÝ TẠI ĐÂY (VD: Gửi code ZPL ra máy Zebra)
                                // executeZebraPrint(event.item.code);

                                // --- THỰC THI LỆNH IN KIOSK ---
                                const qrContainer = document.getElementById('qrcode-wrapper');
                                qrContainer.innerHTML = ''; // Xóa ảnh QR ngầm nếu có lệnh trước đó

                                // Điền thông tin biến cố vào vùng in thầm lặng
                                if (document.getElementById('print-text')) {
                                    document.getElementById('print-text').innerText = event.item.code;
                                }
                                document.getElementById('print-length').innerText = 'Dài ' + event.item.length +
                                    ' m';

                                // Áp dụng cấu hình cỡ chữ User vừa đặt
                                if (confText && document.getElementById('print-text')) {
                                    document.getElementById('print-text').style.fontSize = confText.value +
                                        'px';
                                }
                                if (confLength && document.getElementById('print-length')) {
                                    document.getElementById('print-length').style.fontSize = confLength.value +
                                        'px';
                                }
                                const userQrSize = parseInt(confQr?.value || 120);

                                // Gọi thư viện vẽ mã QR Code mới nhất
                                new QRCode(qrContainer, {
                                    text: event.item.code,
                                    width: userQrSize, // Kích cỡ QR
                                    height: userQrSize,
                                    colorDark: "#000000",
                                    colorLight: "#ffffff",
                                    correctLevel: QRCode.CorrectLevel.M
                                });

                                // Đợi trình duyệt render hình ảnh QR lên DOM mất khoảng 300ms, rồi bóp cò máy in
                                setTimeout(() => {
                                    window.print();
                                }, 300);
                            });
                    } else {
                        setTimeout(subscribeToPrinter, 100);
                    }
                };
                subscribeToPrinter();
            });
        </script>
    </body>

</html>
