@props([
    'id' => 'scanner_' . uniqid(),
    'inputModel' => 'scannedCodeInput',
    'onEnter' => 'handleKeyInput',
    'onScan' => 'handleScan',
    'placeholder' => 'Quét hoặc nhập mã...',
    'buttonText' => 'Tra cứu',
])

<div class="scanner-wrapper" id="{{ $id }}_wrapper">
    {{-- 1. GIAO DIỆN PC / SÚNG QUÉT BÀN PHÍM --}}
    <div class="d-none d-md-block mb-4">
        <div class="alert alert-info small d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-keyboard me-1"></i> Chế độ nhập liệu / Máy quét rời</span>
            <button type="button" class="btn btn-sm btn-outline-primary bg-white btn-toggle-cam-pc"
                onclick="toggleCameraDesktop(this, '{{ $onScan }}', '{{ $id }}')">
                <i class="fa-solid fa-camera me-1"></i> Dùng Camera
            </button>
        </div>
        <div class="input-group input-group-lg shadow-sm">
            <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
            <input type="text" id="scannerInput_{{ $id }}" wire:model="{{ $inputModel }}"
                wire:keydown.enter="{{ $onEnter }}" class="form-control fw-bold text-primary"
                placeholder="{{ $placeholder }}" autocomplete="off" autofocus>
            <button type="button" wire:click="{{ $onEnter }}"
                class="btn btn-primary px-4">{{ $buttonText }}</button>
        </div>
    </div>

    {{-- 2. GIAO DIỆN MOBILE (Nút bật camera) --}}
    <div class="d-md-none text-center mb-3">
        <div class="mobile-guide_{{ $id }} mb-3">
            <p class="text-muted small mb-2">Sử dụng Camera điện thoại</p>
            <div class="d-grid">
                <button type="button" class="btn btn-primary btn-lg shadow btn-start-camera-mobile"
                    onclick="startCamera('{{ $onScan }}', '{{ $id }}')">
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
    <div id="camera-container-{{ $id }}" class="camera-container mb-4" style="display:none;" wire:ignore>
        <div class="position-relative border rounded overflow-hidden shadow-sm bg-black">
            <div id="reader-{{ $id }}" style="width: 100%;"></div>
            <button type="button" onclick="stopCamera('{{ $id }}')"
                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" style="z-index: 10;">
                <i class="fa-solid fa-xmark"></i> Đóng
            </button>
        </div>
        <div class="text-center mt-2 small text-muted placeholder-icon_{{ $id }}">Đang tìm mã QR...</div>
    </div>
</div>

@assets
    <script src="/js/html5-qrcode.min.js" type="text/javascript"></script>
    <script>
        if (typeof window.scannerInitialized === 'undefined') {
            window.scannerInitialized = true;
            window.html5QrcodeScanner = null;
            window.isCameraRunning = false;
            window.currentScanMethod = '';
            window.currentScannerId = '';

            // ===== DỌN DẸP CAMERA KHI LIVEWIRE CHUYỂN TRANG/RENDER =====
            document.addEventListener('livewire:initialized', () => {
                // Sử dụng Livewire Hook để bắt khi một thành phần bị xóa hoặc cập nhật DOM
                Livewire.hook('morph.removing', ({ el }) => {
                    // Tự động tắt camera nếu container của nó sắp bị xóa khỏi DOM
                    if (el.id && el.id.includes('wrapper') && window.isCameraRunning && window.html5QrcodeScanner) {
                        window.forceStopCamera();
                    }
                });

                // Tắt camera khi người dùng rời khỏi trang
                window.addEventListener('beforeunload', () => {
                    window.forceStopCamera();
                });

                Livewire.on('resume-camera', () => {
                    if (window.isCameraRunning) {
                        setTimeout(() => {
                            document.querySelectorAll('.placeholder-icon_' + window.currentScannerId).forEach(el => el.style.display = 'none');
                            document.querySelectorAll('.mobile-guide_' + window.currentScannerId).forEach(el => el.style.display = 'none');
                        }, 50);
                    } else if (window.currentScannerId) {
                        const input = document.getElementById('scannerInput_' + window.currentScannerId);
                        if (input) {
                            input.value = '';
                            input.focus();
                        }
                    }
                });

                Livewire.on('focus-input', () => {
                    if (!window.isCameraRunning) {
                        // Try to find the visible scanner input
                        const inputs = document.querySelectorAll('input[id^="scannerInput_"]');
                        for (const input of inputs) {
                            if (input.offsetParent !== null) { // is visible
                                input.value = '';
                                input.focus();
                                break;
                            }
                        }
                    }
                });
            });

            window.forceStopCamera = function() {
                if (window.isCameraRunning && window.html5QrcodeScanner) {
                    try {
                        window.html5QrcodeScanner.stop().catch(() => {}).finally(() => {
                            try { window.html5QrcodeScanner.clear(); } catch(e) {}
                            window.html5QrcodeScanner = null;
                            window.isCameraRunning = false;
                        });
                    } catch(e) {}
                }
            };

            window.toggleCameraDesktop = function(btnElement, scanMethod, scannerId) {
                if (window.isCameraRunning) {
                    window.stopCamera(scannerId);
                } else {
                    window.startCamera(scanMethod, scannerId, btnElement);
                }
            };

            window.startCamera = function(scanMethod, scannerId, btnPc = null) {
                window.currentScanMethod = scanMethod;
                window.currentScannerId = scannerId;

                const readerId = "reader-" + scannerId;
                const readerEl = document.getElementById(readerId);
                if (!readerEl) {
                    alert('Không tìm thấy khung camera. Vui lòng thử lại.');
                    return;
                }

                // Nếu đang chạy ở một scanner khác, hãy tắt nó trước
                if (window.isCameraRunning && window.html5QrcodeScanner) {
                    window.html5QrcodeScanner.stop().catch(() => {}).finally(() => {
                        window.isCameraRunning = false;
                        try { window.html5QrcodeScanner.clear(); } catch(e) {}
                        window.html5QrcodeScanner = null;
                        window.initNewHtml5Qrcode(readerId, scannerId);
                    });
                } else {
                    window.initNewHtml5Qrcode(readerId, scannerId);
                }
            };

            window.initNewHtml5Qrcode = function(readerId, scannerId) {
                // Update UI
                document.querySelectorAll('.mobile-guide_' + scannerId).forEach(el => el.style.display = 'none');
                document.getElementById('camera-container-' + scannerId).style.display = 'block';
                document.querySelectorAll('.placeholder-icon_' + scannerId).forEach(el => el.style.display = 'none');

                document.querySelectorAll('.btn-toggle-cam-pc').forEach(btn => {
                    btn.innerHTML = '<i class="fa-solid fa-xmark me-1"></i> Tắt Camera';
                });

                // Re-create instance
                window.html5QrcodeScanner = new Html5Qrcode(readerId);
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                };

                window.html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        // Thêm Timeout và Try/Catch để ngăn lỗi Livewire làm crash đứng khung hình Camera
                        setTimeout(() => {
                            try {
                                const audioSuccess = new Audio('/audio/cartoon_boing.ogg');
                                audioSuccess.play().catch(() => {});

                                const cameraContainer = document.getElementById('camera-container-' + window.currentScannerId);
                                if (!cameraContainer) return;
                                
                                let handled = false;
                                const wireComponent = cameraContainer.closest('[wire\\:id]');
                                if (wireComponent && typeof window.Livewire !== 'undefined' && typeof window.Livewire.find === 'function') {
                                    const componentId = wireComponent.getAttribute('wire:id');
                                    const lwInstance = window.Livewire.find(componentId);
                                    if (lwInstance) {
                                        // Livewire 3: lwInstance refers directly to $wire object 
                                        if (typeof lwInstance[window.currentScanMethod] === 'function') {
                                            lwInstance[window.currentScanMethod](decodedText);
                                            handled = true;
                                        } 
                                        // Livewire 2
                                        else if (typeof lwInstance.call === 'function') {
                                            lwInstance.call(window.currentScanMethod, decodedText);
                                            handled = true;
                                        }
                                    }
                                } 
                                
                                if (!handled) {
                                    // Fallback cứu hộ thông qua DOM (Bền vững nhất)
                                    console.warn("Using fallback to dispatch input event.");
                                    const inputEl = document.getElementById('scannerInput_' + window.currentScannerId);
                                    if(inputEl) {
                                        inputEl.value = decodedText;
                                        inputEl.dispatchEvent(new Event('input', {bubbles:true}));
                                        setTimeout(() => {
                                            if(inputEl.nextElementSibling) inputEl.nextElementSibling.click();
                                        }, 100);
                                    }
                                }
                            } catch (e) {
                                console.error('[Camera Error] Lỗi khi xử lý mã:', e);
                                alert('Mã QR: ' + decodedText + '.\nLỗi kĩ thuật JS: ' + e.message);
                            }
                        }, 10);
                    },
                    (errorMessage) => {
                        // Ignore background scan errors
                    }
                ).then(() => {
                    window.isCameraRunning = true;
                }).catch(err => {
                    console.warn('[Camera Error]', err);
                    window.isCameraRunning = false;
                    window.html5QrcodeScanner = null;
                    window.resetUI(scannerId);

                    const msg = err && err.message ? err.message.toLowerCase() : '';
                    if (msg.includes('permission') || msg.includes('denied') || msg.includes('notallowed')) {
                        alert('❌ Trình duyệt bị chặn quyền Camera.\n\nVui lòng:\n1. Nhấn vào biểu tượng khoá/camera trên thanh địa chỉ\n2. Cho phép truy cập Camera\n3. Tải lại trang');
                    } else if (msg.includes('notfound') || msg.includes('devicenotfound')) {
                        alert('❌ Không tìm thấy Camera trên thiết bị này.');
                    } else if (msg.includes('https') || location.protocol !== 'https:' && location.hostname !== 'localhost') {
                        alert('❌ Camera chỉ hoạt động trên HTTPS hoặc localhost.\n\nTrang web đang dùng: ' + location.protocol + '//' + location.hostname);
                    } else {
                        alert('❌ Không thể mở Camera: ' + (err.message || err));
                    }
                });
            };

            window.stopCamera = function(scannerId) {
                if (window.html5QrcodeScanner) {
                    window.html5QrcodeScanner.stop().then(() => {
                        try { window.html5QrcodeScanner.clear(); } catch (e) {}
                        window.html5QrcodeScanner = null;
                        window.isCameraRunning = false;
                        window.resetUI(scannerId);
                    }).catch(err => {
                        try { window.html5QrcodeScanner.clear(); } catch (e) {}
                        window.html5QrcodeScanner = null;
                        window.isCameraRunning = false;
                        window.resetUI(scannerId);
                    });
                } else {
                    window.isCameraRunning = false;
                    window.resetUI(scannerId);
                }
            };

            window.resetUI = function(scannerId) {
                if (!scannerId) return;
                const container = document.getElementById('camera-container-' + scannerId);
                if(container) container.style.display = 'none';
                
                document.querySelectorAll('.mobile-guide_' + scannerId).forEach(el => el.style.display = 'block');
                document.querySelectorAll('.placeholder-icon_' + scannerId).forEach(el => el.style.display = 'block');

                document.querySelectorAll('.btn-toggle-cam-pc').forEach(btn => {
                    btn.innerHTML = '<i class="fa-solid fa-camera me-1"></i> Dùng Camera';
                });

                if (window.innerWidth >= 768) {
                    const input = document.getElementById('scannerInput_' + scannerId);
                    if (input) input.focus();
                }
            };
        }
    </script>
@endassets
