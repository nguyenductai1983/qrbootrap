@props([
    'inputModel' => 'scannedCodeInput',
    'onEnter' => 'handleKeyInput',
    'onScan' => 'handleScan',
    'placeholder' => 'Quét hoặc nhập mã...',
    'buttonText' => 'Tra cứu',
])

<div class="scanner-wrapper">
    {{-- 1. GIAO DIỆN PC / SÚNG QUÉT BÀN PHÍM --}}
    <div class="d-none d-md-block mb-4">
        <div class="alert alert-info py-2 small d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-keyboard me-1"></i> Chế độ nhập liệu / Máy quét rời</span>
            <button type="button" class="btn btn-sm btn-outline-primary bg-white btn-toggle-cam-pc"
                onclick="toggleCameraDesktop(this, '{{ $onScan }}')">
                <i class="fa-solid fa-camera me-1"></i> Dùng Camera
            </button>
        </div>
        <div class="input-group input-group-lg shadow-sm">
            <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
            <input type="text" id="scannerInput" wire:model="{{ $inputModel }}"
                wire:keydown.enter="{{ $onEnter }}" class="form-control fw-bold text-primary"
                placeholder="{{ $placeholder }}" autocomplete="off" autofocus>
            <button type="button" wire:click="{{ $onEnter }}"
                class="btn btn-primary px-4">{{ $buttonText }}</button>
        </div>
    </div>

    {{-- 2. GIAO DIỆN MOBILE (Nút bật camera) --}}
    <div class="d-md-none text-center mb-3">
        <div class="mobile-guide mb-3">
            <p class="text-muted small mb-2">Sử dụng Camera điện thoại</p>
            <div class="d-grid">
                <button type="button" class="btn btn-primary btn-lg shadow btn-start-camera-mobile"
                    onclick="startCamera('{{ $onScan }}')">
                    <i class="fa-solid fa-camera me-2"></i> BẬT CAMERA
                </button>
            </div>

            <div class="position-relative mt-4 mb-3">
                <hr class="text-secondary opacity-25">
                <span class="position-absolute top-50 start-50 translate-middle px-2 small text-muted fw-bold">
                    NHẬP MÃ THỦ CÔNG</span>
            </div>

            <div class="input-group input-group-lg shadow-sm">
                <input type="text" wire:model="{{ $inputModel }}" wire:keydown.enter="{{ $onEnter }}"
                    class="form-control fw-bold text-primary" placeholder="{{ $placeholder }}" autocomplete="off">
                <button type="button" wire:click="{{ $onEnter }}" class="btn btn-primary px-3"><i
                        class="fa-solid fa-play"></i></button>
            </div>
        </div>
    </div>

    {{-- 3. KHUNG CAMERA (Ẩn mặc định) --}}
    <div id="camera-container" class="camera-container mb-4" style="display:none;" wire:ignore>
        <div class="position-relative border rounded overflow-hidden shadow-sm bg-black">
            <div id="reader" style="width: 100%;"></div>
            <button type="button" onclick="stopCamera()"
                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" style="z-index: 10;">
                <i class="fa-solid fa-xmark"></i> Đóng
            </button>
        </div>
        <div class="text-center mt-2 small text-muted placeholder-icon">Đang tìm mã QR...</div>
    </div>
</div>

@once
    <script src="/js/html5-qrcode.min.js" type="text/javascript"></script>
    <script>
        let html5QrcodeScanner = null;
        let isCameraRunning = false;
        let currentScanMethod = '';

        function toggleCameraDesktop(btnElement, scanMethod) {
            if (isCameraRunning) {
                stopCamera();
            } else {
                startCamera(scanMethod, btnElement);
            }
        }

        function startCamera(scanMethod, btnPc = null) {
            currentScanMethod = scanMethod;

            document.querySelectorAll('.mobile-guide').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.camera-container').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.placeholder-icon').forEach(el => el.style.display = 'none');

            const pcBtns = document.querySelectorAll('.btn-toggle-cam-pc');
            pcBtns.forEach(btn => {
                btn.innerHTML = '<i class="fa-solid fa-xmark me-1"></i> Tắt Camera';
            });

            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }

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
                },
                config,
                (decodedText) => {
                    let componentId = document.getElementById('camera-container').closest('[wire\\:id]').getAttribute(
                        'wire:id');
                    // Gửi event tới Livewire component componentId với action
                    Livewire.find(componentId).call(currentScanMethod, decodedText);
                },
                (errorMessage) => {
                    // Ignore errors during scan
                }
            ).then(() => {
                isCameraRunning = true;
            }).catch(err => {
                console.warn(err);
                if (err.message && err.message.includes('Permission')) {
                    alert("Không thể mở Camera. Vui lòng cấp quyền truy cập.");
                } else {
                    alert("Lỗi phần cứng hoặc không tìm thấy Camera.");
                }
                stopCamera();
            });
        }

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

        function resetUI() {
            document.querySelectorAll('.camera-container').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.mobile-guide').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.placeholder-icon').forEach(el => el.style.display = 'block');

            const pcBtns = document.querySelectorAll('.btn-toggle-cam-pc');
            pcBtns.forEach(btn => {
                btn.innerHTML = '<i class="fa-solid fa-camera me-1"></i> Dùng Camera';
            });

            if (window.innerWidth >= 768) {
                const input = document.getElementById('scannerInput');
                if (input) input.focus();
            }
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('resume-camera', () => {
                if (isCameraRunning) {
                    // Ẩn UX icon khi camera restart
                    setTimeout(() => {
                        document.querySelectorAll('.placeholder-icon').forEach(el => el.style
                            .display = 'none');
                        document.querySelectorAll('.mobile-guide').forEach(el => el.style.display =
                            'none');
                    }, 50);
                } else {
                    const input = document.getElementById('scannerInput');
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                }
            });

            Livewire.on('focus-input', () => {
                if (!isCameraRunning) {
                    const input = document.getElementById('scannerInput');
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                }
            });
        });
    </script>
@endonce
