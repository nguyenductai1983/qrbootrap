<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            {{-- HEADER: CẤU HÌNH LỌC & GÁN DỮ LIỆU --}}
            <div class="card shadow-sm mb-3 border-primary">
                <div class="card-header bg-primary text-white py-1">
                    <small class="fw-bold"><i class="fa-solid fa-gears me-1"></i> Thiết lập quét (Để trống = Giữ
                        nguyên)</small>
                </div>
                <div class="card-body py-2 ">
                    <div class="row g-2">
                        {{-- 1. Chọn Đơn Hàng --}}
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Gán Đơn Hàng (PO)</label>
                            <select wire:model.live="selectedOrderId"
                                class="form-select form-select-sm border-secondary fw-bold text-primary">
                                <option value="">-- Mặc định --</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}">{{ $order->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Chọn Model --}}
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Gán Model</label>
                            <select wire:model.live="selectedModelId"
                                class="form-select form-select-sm border-secondary fw-bold text-success">
                                <option value="">-- Mặc định --</option>
                                @foreach ($models as $model)
                                    <option value="{{ $model->id }}">{{ $model->code }} - {{ $model->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-qrcode me-2"></i>QUÉT SẢN PHẨM</h5>
                </div>

                <div class="card-body">

                    {{-- 1. GIAO DIỆN PC (Có ô nhập liệu) --}}
                    <div class="d-none d-md-block mb-4">
                        <div class="alert alert-info py-2 small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-keyboard me-1"></i> Chế độ nhập liệu / Máy quét rời</span>
                            <button class="btn btn-sm btn-outline-primary bg-white" onclick="toggleCameraDesktop()"
                                id="btn-toggle-cam-pc">
                                <i class="fa-solid fa-camera me-1"></i> Dùng Camera
                            </button>
                        </div>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text "><i class="fa-solid fa-barcode"></i></span>
                            <input type="text" id="scannerInput" wire:model="scannedCodeInput"
                                wire:keydown.enter="handleKeyInput" class="form-control fw-bold text-primary"
                                placeholder="Quét hoặc nhập mã..." autocomplete="off">
                            <button wire:click="handleKeyInput" class="btn btn-primary px-4">Tra cứu</button>
                        </div>
                    </div>

                    {{-- 2. GIAO DIỆN MOBILE (Nút bật camera) --}}
                    <div class="d-md-none text-center mb-3">
                        <div id="mobile-guide" class="mb-3">
                            <p class="text-muted small mb-2">Sử dụng Camera điện thoại</p>
                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg shadow" id="btn-start-camera-mobile"
                                    onclick="startCamera()">
                                    <i class="fa-solid fa-camera me-2"></i> BẬT CAMERA
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- 3. KHUNG CAMERA (Ẩn mặc định) --}}
                    <div id="camera-container" style="display:none;" class="mb-4">
                        <div class="position-relative border rounded overflow-hidden shadow-sm bg-black">
                            <div id="reader" style="width: 100%;"></div>
                            <button onclick="stopCamera()"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                <i class="fa-solid fa-xmark"></i> Đóng
                            </button>
                        </div>
                        <div class="text-center mt-2 small text-muted">Đang tìm mã QR...</div>
                    </div>

                    <hr>

                    {{-- 4. HIỂN THỊ KẾT QUẢ --}}
                    @if ($message)
                        <div class="alert alert-{{ $scanStatus == 'success' ? 'success' : ($scanStatus == 'warning' ? 'warning' : 'danger') }} text-center shadow-sm"
                            role="alert">
                            <h4 class="alert-heading fw-bold fs-5 mb-1">
                                @if ($scanStatus == 'success')
                                    <i class="fa-solid fa-circle-check"></i>
                                @elseif($scanStatus == 'warning')
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark"></i>
                                @endif
                                {{ $message }}
                            </h4>
                        </div>
                    @endif

                    @if (!empty($itemInfo))
                        <div class="table-responsive  rounded p-2 border position-relative">
                            <button wire:click="resetScan"
                                class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted small" width="30%">Mã Vải:</td>
                                    <td class="fw-bold fs-5 text-primary">
                                        {{ $itemInfo['MA_VAI'] ?? 'N/A' }}
                                        @if (!empty($selectedModelId))
                                            <i class="fa-solid fa-pen-to-square text-warning small ms-1"
                                                title="Đã cập nhật theo cài đặt"></i>
                                        @endif
                                    </td>
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
                                    <td>
                                        {{ $itemInfo['PO'] ?? '' }}
                                        @if (!empty($selectedOrderId))
                                            <i class="fa-solid fa-pen-to-square text-warning small ms-1"
                                                title="Đã cập nhật theo cài đặt"></i>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endif

                    {{-- Placeholder khi chưa quét --}}
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
    {{-- 1. Sửa lại đường dẫn thư viện HTML5-QRCode dùng bản Online (CDN) để đảm bảo chạy được ngay --}}
    <script src="js/html5-qrcode.min.js" type="text/javascript"></script>

    {{-- 2. Thư viện SweetAlert2 --}}
    <script src="js/sweetalert2@11.js" type="text/javascript"></script>

    <script>
        // 3. Sửa lại đường dẫn âm thanh dùng link Online của Google (để không phải tải file về)
        const audioSuccess = new Audio('audio/cartoon_boing.ogg');
        const audioError = new Audio('audio/beep_short.ogg');

        let html5QrcodeScanner = null;
        let isCameraRunning = false;

        document.addEventListener('livewire:initialized', () => {
            // Lắng nghe sự kiện âm thanh
            Livewire.on('play-success-sound', () => {
                audioSuccess.play().catch(e => console.log(e));
            });
            Livewire.on('play-error-sound', () => {
                audioError.play().catch(e => console.log(e));
            });
            Livewire.on('play-warning-sound', () => {
                audioError.play().catch(e => console.log(e));
            });

            // Focus vào ô input trên PC
            Livewire.on('focus-input', () => {
                if (!isCameraRunning) {
                    const input = document.getElementById('scannerInput');
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                }
            });

            // Tự động focus khi vào trang (nếu là màn hình lớn)
            if (window.innerWidth >= 768) {
                const input = document.getElementById('scannerInput');
                if (input) input.focus();
            }

            // Hiển thị Toast (Popup đẹp)
            Livewire.on('show-toast', (data) => {
                const payload = data[0] || data;
                Swal.fire({
                    icon: payload.type,
                    title: payload.title,
                    text: payload.text,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#fff',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            });
        });

        // Hàm bật/tắt camera trên PC
        function toggleCameraDesktop() {
            if (isCameraRunning) {
                stopCamera();
            } else {
                startCamera();
            }
        }

        // Hàm khởi động Camera
        function startCamera() {
            document.getElementById('mobile-guide').style.display = 'none';
            document.getElementById('camera-container').style.display = 'block';
            document.getElementById('placeholder-icon').style.display = 'none';

            const btnPc = document.getElementById('btn-toggle-cam-pc');
            if (btnPc) btnPc.innerHTML = '<i class="fa-solid fa-xmark me-1"></i> Tắt Camera';

            html5QrcodeScanner = new Html5Qrcode("reader");
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                },
                aspectRatio: 1.0
            };

            html5QrcodeScanner.start({
                    facingMode: "environment"
                }, config,
                (decodedText) => {
                    // Gọi hàm PHP xử lý
                    @this.handleScan(decodedText);
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

        // Hàm tắt Camera
        function stopCamera() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner.clear();
                    isCameraRunning = false;
                    resetUI();
                }).catch(err => {
                    resetUI();
                });
            } else {
                resetUI();
            }
        }

        // Hàm reset giao diện về ban đầu
        function resetUI() {
            document.getElementById('camera-container').style.display = 'none';
            document.getElementById('placeholder-icon').style.display = 'block';
            document.getElementById('mobile-guide').style.display = 'block';

            const btnPc = document.getElementById('btn-toggle-cam-pc');
            if (btnPc) btnPc.innerHTML = '<i class="fa-solid fa-camera me-1"></i> Dùng Camera';

            if (window.innerWidth >= 768) {
                const input = document.getElementById('scannerInput');
                if (input) input.focus();
            }
        }
    </script>
</div>
