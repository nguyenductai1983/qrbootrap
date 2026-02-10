<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            {{-- HEADER: Cấu hình lọc --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-filter text-secondary me-2"></i>
                        <select wire:model.live="selectedOrderId" class="form-select form-select-sm border-0 bg-light fw-bold">
                            <option value="">-- Quét tất cả đơn hàng --</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}">PO: {{ $order->code }} ({{ $order->customer_name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-qrcode me-2"></i>QUÉT SẢN PHẨM</h5>
                </div>

                <div class="card-body">

                    {{-- 1. GIAO DIỆN CHÍNH (PC / TABLET / LAPTOP) --}}
                    {{-- Hiển thị trên màn hình lớn --}}
                    <div class="d-none d-md-block mb-4">
                        <div class="alert alert-info py-2 small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-keyboard me-1"></i> Chế độ nhập liệu / Máy quét rời</span>

                            {{-- NÚT BẬT CAMERA CHO LAPTOP/TABLET --}}
                            <button class="btn btn-sm btn-outline-primary bg-white" onclick="toggleCameraDesktop()" id="btn-toggle-cam-pc">
                                <i class="fa-solid fa-camera me-1"></i> Dùng Camera
                            </button>
                        </div>

                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-barcode"></i></span>
                            <input type="text"
                                   id="scannerInput"
                                   wire:model="scannedCodeInput"
                                   wire:keydown.enter="handleKeyInput"
                                   class="form-control fw-bold text-primary"
                                   placeholder="Quét hoặc nhập mã..."
                                   autocomplete="off">
                            <button wire:click="handleKeyInput" class="btn btn-primary px-4">Tra cứu</button>
                        </div>
                    </div>

                    {{-- 2. GIAO DIỆN MOBILE (Chỉ hiện trên màn hình nhỏ) --}}
                    <div class="d-md-none text-center mb-3">
                        <div id="mobile-guide" class="mb-3">
                            <p class="text-muted small mb-2">Sử dụng Camera điện thoại</p>
                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg shadow" id="btn-start-camera-mobile" onclick="startCamera()">
                                    <i class="fa-solid fa-camera me-2"></i> BẬT CAMERA
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- 3. KHUNG CAMERA (DÙNG CHUNG CHO CẢ PC VÀ MOBILE) --}}
                    {{-- Được ẩn mặc định, JS sẽ kích hoạt hiển thị khi cần --}}
                    <div id="camera-container" style="display:none;" class="mb-4">
                        <div class="position-relative border rounded overflow-hidden shadow-sm bg-black">
                            {{-- Khung hình camera --}}
                            <div id="reader" style="width: 100%;"></div>

                            {{-- Nút tắt camera --}}
                            <button onclick="stopCamera()" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                <i class="fa-solid fa-xmark"></i> Đóng
                            </button>
                        </div>
                        <div class="text-center mt-2 small text-muted">Đang tìm mã QR...</div>
                    </div>

                    <hr>

                    {{-- 4. KẾT QUẢ HIỂN THỊ --}}
                    @if ($message)
                        <div class="alert alert-{{ $scanStatus == 'success' ? 'success' : ($scanStatus == 'warning' ? 'warning' : 'danger') }} text-center shadow-sm" role="alert">
                            <h4 class="alert-heading fw-bold fs-5 mb-1">
                                @if($scanStatus == 'success') <i class="fa-solid fa-circle-check"></i>
                                @elseif($scanStatus == 'warning') <i class="fa-solid fa-triangle-exclamation"></i>
                                @else <i class="fa-solid fa-circle-xmark"></i>
                                @endif
                                {{ $message }}
                            </h4>
                        </div>
                    @endif

                    @if (!empty($itemInfo))
                        <div class="table-responsive bg-light rounded p-2 border position-relative">
                            {{-- Nút Reset nhanh --}}
                            <button wire:click="resetScan" class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>

                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted small" width="30%">Mã Vải:</td>
                                    <td class="fw-bold fs-5 text-primary">{{ $itemInfo['MA_VAI'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">Màu:</td>
                                    <td class="fw-bold">{{ $itemInfo['MAU'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">Thông số:</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $itemInfo['MA_CAY_VAI'] ?? 'Lô ?' }}</span>
                                        <span class="badge bg-info text-dark">{{ $itemInfo['SO_MET'] ?? 0 }} m</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">PO:</td>
                                    <td>{{ $itemInfo['PO'] ?? '' }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif

                    {{-- Ảnh minh họa khi chưa quét --}}
                    @if (empty($itemInfo) && empty($message))
                        <div class="text-center text-muted py-4 opacity-50" id="placeholder-icon">
                            <i class="fa-solid fa-barcode fa-4x mb-2"></i>
                            <p>Sẵn sàng quét mã...</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="js/html5-qrcode.min.js" type="text/javascript"></script>
    <script>
        // --- ÂM THANH ---
        const audioSuccess = new Audio('audio/cartoon_boing.ogg');
        const audioError = new Audio('audio/beep_short.ogg');

        let html5QrcodeScanner = null;
        let isCameraRunning = false;

        document.addEventListener('livewire:initialized', () => {

            // Xử lý sự kiện âm thanh
            Livewire.on('play-success-sound', () => { audioSuccess.play().catch(e => {}); });
            Livewire.on('play-error-sound', () => { audioError.play().catch(e => {}); });
            Livewire.on('play-warning-sound', () => { audioError.play().catch(e => {}); });

            // Focus vào ô nhập liệu (Nếu camera đang KHÔNG bật)
            Livewire.on('focus-input', () => {
                if (!isCameraRunning) {
                    const input = document.getElementById('scannerInput');
                    if(input) {
                        input.value = '';
                        input.focus();
                    }
                }
            });

            // Tự động focus lúc đầu (chỉ trên PC)
            if (window.innerWidth >= 768) {
                const input = document.getElementById('scannerInput');
                if(input) input.focus();
            }

            // Dừng camera khi quét xong (Tùy chọn, ở đây mình để nó chạy tiếp để quét liên tục)
            Livewire.on('scan-finished', () => {
                // Nếu muốn quét liên tục thì không cần làm gì
                // Nếu muốn dừng sau mỗi lần quét thì gọi: stopCamera();
            });
        });

        // --- HÀM TOGGLE CHO PC/LAPTOP ---
        function toggleCameraDesktop() {
            if (isCameraRunning) {
                stopCamera();
            } else {
                startCamera();
            }
        }

        // --- HÀM BẬT CAMERA ---
        function startCamera() {
            // Ẩn các nút kích hoạt
            document.getElementById('mobile-guide').style.display = 'none';
            // Hiện khung chứa camera
            document.getElementById('camera-container').style.display = 'block';
            document.getElementById('placeholder-icon').style.display = 'none';

            // Đổi text nút trên PC
            const btnPc = document.getElementById('btn-toggle-cam-pc');
            if(btnPc) btnPc.innerHTML = '<i class="fa-solid fa-xmark me-1"></i> Tắt Camera';

            // Khởi tạo Scanner
            html5QrcodeScanner = new Html5Qrcode("reader");

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            // Cố gắng dùng Camera sau (environment), nếu không có (Laptop) nó sẽ tự dùng Webcam
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    // QUÉT THÀNH CÔNG
                    @this.handleScan(decodedText);
                    // Không stop camera để người dùng quét tiếp
                },
                (errorMessage) => {
                    // Đang quét...
                }
            ).then(() => {
                isCameraRunning = true;
            }).catch(err => {
                alert("Không thể mở Camera. Vui lòng cấp quyền truy cập.");
                stopCamera();
            });
        }

        // --- HÀM TẮT CAMERA ---
        function stopCamera() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner.clear();
                    isCameraRunning = false;
                    resetUI();
                }).catch(err => {
                    console.log("Stop failed: ", err);
                    resetUI();
                });
            } else {
                resetUI();
            }
        }

        function resetUI() {
            document.getElementById('camera-container').style.display = 'none';
            document.getElementById('placeholder-icon').style.display = 'block';

            // Hiện lại nút kích hoạt Mobile
            document.getElementById('mobile-guide').style.display = 'block';

            // Đổi lại text nút PC
            const btnPc = document.getElementById('btn-toggle-cam-pc');
            if(btnPc) btnPc.innerHTML = '<i class="fa-solid fa-camera me-1"></i> Dùng Camera';

            // Focus lại ô input trên PC
            if (window.innerWidth >= 768) {
                const input = document.getElementById('scannerInput');
                if(input) input.focus();
            }
        }
    </script>
</div>
