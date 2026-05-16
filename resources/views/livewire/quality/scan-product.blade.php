<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-12">

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-qrcode me-2"></i>QUÉT SẢN PHẨM</h5>
                </div>

                <div class="card-body">
                    <x-scanner inputModel="scannedCodeInput" onEnter="handleKeyInput" onScan="handleScan"
                        placeholder="Quét hoặc nhập mã tem..." buttonText="Xác nhận và Tra cứu" />

                    <hr>

                    {{-- 4. HIỂN THỊ KẾT QUẢ --}}
                    @if ($message)
                        <div class="alert alert-{{ $scanStatus == 'success' ? 'success' : ($scanStatus == 'warning' ? 'warning' : 'danger') }} text-center shadow-sm"
                            role="alert">
                            <h6 class="alert-heading fw-bold fs-6">
                                @if ($scanStatus == 'success')
                                    <i class="fa-solid fa-circle-check"></i>
                                @elseif($scanStatus == 'warning')
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark"></i>
                                @endif
                                {{ $message }}
                            </h6>
                            {{-- MỚI THÊM: Nút Quét lại hiển thị khi có lỗi hoặc cảnh báo --}}
                            @if ($scanStatus == 'error' || $scanStatus == 'warning')
                                <button wire:click="resetScan" class="btn btn-sm btn-outline-dark">
                                    <i class="fa-solid fa-rotate-right me-1"></i> Bấm để quét lại
                                </button>
                            @endif
                        </div>
                    @endif

                    @if (!empty($itemInfo))
                        <div class="rounded p-2 border">
                            <div class="input-group mb-3">
                                @if ($scannedItemId)
                                    <button wire:click="reprintItems([{{ $scannedItemId }}])"
                                        class="form-control btn btn-sm btn-outline-info" title="In lại tem này">
                                        <i class="fa-solid fa-print"></i> In Lại
                                    </button>
                                @endif

                                <button wire:click="resetScan" class="form-control btn btn-sm btn-outline-secondary"
                                    title="Quét lại từ đầu">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                            </div>
                            <div>
                                {{-- Mã Vải & Màu (1 Row) --}}
                                <div class="row g-2 mb-2">
                                    <div class="col-3">
                                        <div class="input-group input-group-sm">
                                            <span class="form-control fw-bold text-primary text-truncate bg-white">
                                                {{ $itemInfo->product->name ?? 'N/A' }}
                                                @if (!empty($selectedModelId))
                                                    <i class="fa-solid fa-pen-to-square text-warning small ms-1"
                                                        title="Đã cập nhật theo cài đặt"></i>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-group input-group-sm">
                                            <span class="form-control fw-bold text-dark text-truncate bg-white">
                                                {{ $itemInfo->color->code ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted">PO:</span>
                                            <span class="form-control fw-bold bg-white">
                                                {{ $itemInfo->order->code ?? '-' }}
                                                @if (!empty($selectedOrderId))
                                                    <i class="fa-solid fa-pen-to-square text-warning small ms-1"
                                                        title="Đã cập nhật theo cài đặt"></i>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Số GSM thực (Mobile Optimized) --}}
                                <div class="p-2 bg-light rounded border border-warning">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small fw-bold">GSM:</span>
                                        <div>
                                            <span class="text-muted small me-1">Gốc:</span>
                                            <span class="badge bg-secondary fs-6">{{ $itemInfo->gsm ?? 0 }}</span>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-lg">
                                        <input type="number" inputmode="decimal" step="0.1" min="0"
                                            wire:model="editGsm" wire:keydown.enter="updateGsm"
                                            class="form-control border-warning fw-bold text-center fs-3 text-primary"
                                            placeholder="0.0" style="height: 55px;">
                                        <span class="input-group-text bg-warning text-dark fw-bold px-3">GSM</span>
                                    </div>
                                </div>
                            </div>

                            {{-- FORM CẬP NHẬT GHI CHÚ VÀ LƯU --}}
                            <div class="border-top pt-2 mt-1">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-12 col-lg-9">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1editNotes">
                                                <i class="fa-solid fa-comment me-1"></i>Ghi chú
                                            </span>
                                            <input type="text" wire:model="editNotes" id="editNotes"
                                                class="form-control form-control-sm"
                                                placeholder="Lý do thay đổi...">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-3">
                                        <div class="input-group mb-3">
                                            <button wire:click="updateGsm" wire:loading.attr="disabled"
                                                class="form-control btn btn-warning fw-bold">
                                                <span wire:loading.remove wire:target="updateGsm">
                                                    <i class="fa-solid fa-floppy-disk me-1"></i>Lưu thông tin
                                                </span>
                                                <span wire:loading wire:target="updateGsm">
                                                    <i class="fa-solid fa-spinner fa-spin me-1"></i>Đang lưu...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===== KHU VỰC CHỤP ẢNH PHIẾU VẢI ===== --}}
                            <div class="border rounded p-2 mt-2 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark small">
                                        <i class="fa-solid fa-camera me-1 text-primary"></i>
                                        Ảnh phiếu vải
                                    </span>
                                    @if ($currentPhoto)
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-circle-check me-1"></i>Đã có ảnh
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Chưa có ảnh</span>
                                    @endif
                                </div>

                                @if ($currentPhoto)
                                    {{-- Hiển thị ảnh đã chụp --}}
                                    <div class="text-center position-relative mb-2">
                                        <img src="{{ $currentPhoto['url'] }}"
                                            alt="Ảnh phiếu vải"
                                            class="img-fluid rounded border shadow-sm"
                                            style="max-height: 280px; object-fit: contain; cursor: zoom-in;"
                                            id="qcPhotoPreview"
                                            onclick="document.getElementById('qcPhotoModal').style.display='flex'">
                                        <div class="text-muted small mt-1">
                                            <i class="fa-solid fa-clock me-1"></i>
                                            Chụp lúc: {{ $currentPhoto['created_at'] }}
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        {{-- Nút chụp lại (label trigger file input) --}}
                                        <label for="qcCameraInput" class="btn btn-outline-primary btn-sm flex-grow-1" style="cursor:pointer;">
                                            <i class="fa-solid fa-camera-rotate me-1"></i>
                                            Chụp lại
                                        </label>
                                        {{-- Nút xóa ảnh --}}
                                        <button wire:click="deletePhoto" wire:confirm="Bạn chắc chắn muốn xóa ảnh này?"
                                            class="btn btn-outline-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                @else
                                    {{-- Chưa có ảnh, hiện nút chụp lớn --}}
                                    <label for="qcCameraInput"
                                        class="btn btn-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-1"
                                        style="cursor:pointer; border-radius: 10px; border: 2px dashed rgba(255,255,255,0.5);">
                                        <i class="fa-solid fa-camera fa-2x"></i>
                                        <span class="fw-bold">Chụp ảnh phiếu vải</span>
                                        <small class="opacity-75">Tap để mở camera</small>
                                    </label>
                                @endif

                                {{-- Input file ẩn (trigger camera trên mobile) --}}
                                <input type="file"
                                    id="qcCameraInput"
                                    accept="image/*"
                                    capture="environment"
                                    class="d-none"
                                    onchange="handleQcPhotoCapture(this)">

                                {{-- Loading indicator khi đang upload --}}
                                <div id="qcPhotoLoading" class="text-center py-2 d-none">
                                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                    <span class="small text-muted">Đang xử lý và lưu ảnh...</span>
                                </div>
                            </div>
                            {{-- ===== KẾT THÚC CHỤP ẢNH ===== --}}

                        </div>
                    @endif

                    {{-- Placeholder --}}
                </div>
            </div>
        </div>
        {{-- SCRIPTS --}}
        {{-- 2. Thư viện SweetAlert2 --}}
        <script src="/js/sweetalert2@11.js" type="text/javascript"></script>

        <script>
            // Âm thanh
            const audioSuccess = new Audio('/audio/cartoon_boing.ogg');
            const audioError = new Audio('/audio/beep_short.ogg');

            document.addEventListener('livewire:initialized', () => {
                // Lắng nghe sự kiện âm thanh
                Livewire.on('play-success-sound', () => {
                    audioError.pause();
                    audioSuccess.pause();
                    audioSuccess.currentTime = 0;
                    audioSuccess.play().catch(e => console.log(e));
                });
                Livewire.on('play-error-sound', () => {
                    audioSuccess.pause();
                    audioError.pause();
                    audioError.currentTime = 0;
                    audioError.play().catch(e => console.log(e));
                });
                Livewire.on('play-warning-sound', () => {
                    audioSuccess.pause();
                    audioError.pause();
                    audioError.currentTime = 0;
                    audioError.play().catch(e => console.log(e));
                });

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

            /**
             * Xử lý khi QC chọn/chụp ảnh từ input file
             * - Resize ảnh về max 1200px (để nhẹ)
             * - Chuyển sang base64
             * - Gửi lên Livewire
             */
            function handleQcPhotoCapture(input) {
                if (!input.files || !input.files[0]) return;

                const file = input.files[0];
                const loading = document.getElementById('qcPhotoLoading');
                if (loading) loading.classList.remove('d-none');

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        // Resize về max 1200px
                        const MAX_SIZE = 1200;
                        let w = img.width;
                        let h = img.height;

                        if (w > MAX_SIZE || h > MAX_SIZE) {
                            if (w > h) {
                                h = Math.round((h * MAX_SIZE) / w);
                                w = MAX_SIZE;
                            } else {
                                w = Math.round((w * MAX_SIZE) / h);
                                h = MAX_SIZE;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = w;
                        canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, w, h);

                        // Xuất ra JPEG chất lượng 85%
                        const base64 = canvas.toDataURL('image/jpeg', 0.85);

                        // Gửi lên Livewire
                        @this.savePhoto(base64).then(() => {
                            if (loading) loading.classList.add('d-none');
                            // Reset input để có thể chụp lại cùng file
                            input.value = '';
                        });
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        </script>

        {{-- Modal xem ảnh to --}}
        <div id="qcPhotoModal"
            onclick="this.style.display='none'"
            style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh;
                   background:rgba(0,0,0,0.85); z-index:9999;
                   align-items:center; justify-content:center; cursor:zoom-out;">
            @if ($currentPhoto)
                <img src="{{ $currentPhoto['url'] }}"
                    style="max-width:95vw; max-height:95vh; border-radius:8px; object-fit:contain;"
                    onclick="event.stopPropagation()">
            @endif
        </div>

    </div>
