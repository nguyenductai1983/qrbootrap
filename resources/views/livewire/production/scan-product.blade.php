<div class="container py-3">

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-dark text-white text-center">
            <h5 class="mb-0">Quét Nhập Kho</h5>
        </div>
    </div>
    <div class="mb-3">
        <select wire:model.live="selectedOrderId" class="form-select form-select-lg bg-warning text-dark fw-bold">
            <option value="">-- CHỌN ĐƠN HÀNG (PO) --</option>
            @foreach ($orders as $o)
                <option value="{{ $o->id }}">{{ $o->code }} - {{ $o->customer_name }}</option>
            @endforeach
        </select>
        @if (!$selectedOrderId)
            <div class="text-danger small fw-bold text-center mt-1">⚠️ Vui lòng chọn PO trước khi quét</div>
        @endif
    </div>

    @if ($selectedOrderId)
        <div class="card shadow-sm mb-3" wire:ignore>...</div>
    @endif
    <div class="card shadow-sm mb-3" wire:ignore>
        <div class="card-body p-0 position-relative">
            <div id="reader" style="width: 100%; min-height: 300px; background: #000;"></div>

            <div id="scan-loading" class="position-absolute top-50 start-50 translate-middle text-white d-none">
                <div class="spinner-border" role="status"></div>
                <div class="mt-2 fw-bold">Đang xử lý...</div>
            </div>
        </div>
    </div>

    @if ($scanStatus)
        <div
            class="card shadow border-{{ $scanStatus == 'success' ? 'success' : 'danger' }} mb-3 animate__animated animate__fadeInUp">
            <div class="card-body text-center">

                {{-- Icon trạng thái --}}
                <div class="mb-2">
                    @if ($scanStatus == 'success')
                        <i class="fa-solid fa-circle-check text-success" style="font-size: 3rem;"></i>
                    @elseif($scanStatus == 'warning')
                        <i class="fa-solid fa-triangle-exclamation text-warning" style="font-size: 3rem;"></i>
                    @else
                        <i class="fa-solid fa-circle-xmark text-danger" style="font-size: 3rem;"></i>
                    @endif
                </div>

                <h4 class="fw-bold">{{ $message }}</h4>
                <div class="text-muted fw-bold fs-5">{{ $lastScannedCode }}</div>

                {{-- Thông tin chi tiết vải --}}
                @if (!empty($itemInfo))
                    <div class="alert alert-light text-start mt-3 small">
                        <div><strong>Loại:</strong> {{ $itemInfo['MA_VAI'] ?? '' }}</div>
                        <div><strong>Màu:</strong> {{ $itemInfo['MAU'] ?? '' }}</div>
                        <div><strong>Lô:</strong> {{ $itemInfo['MA_CAY_VAI'] ?? '' }}</div>
                    </div>
                @endif

                {{-- NÚT QUÉT TIẾP --}}
                <div class="mt-4">
                    <button wire:click="resetScan" class="btn btn-lg btn-primary w-100">
                        <i class="fa-solid fa-camera me-2"></i> Quét Cây Tiếp Theo
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const html5QrCode = new Html5Qrcode("reader");
            let isProcessing = false; // Biến Cờ (Flag) quan trọng để chặn quét trùng

            const startCamera = () => {
                const config = {
                    fps: 10,
                    qrbox: {
                        width: 550,
                        height: 150
                    }
                };

                html5QrCode.start({
                        facingMode: "environment"
                    }, config,
                    (decodedText) => {
                        // --- KHI QUÉT TRÚNG ---

                        // 1. Nếu đang xử lý rồi thì chặn ngay, không làm gì cả
                        if (isProcessing) return;

                        // 2. Đánh dấu đang bận
                        isProcessing = true;

                        console.log("Đã bắt được mã: " + decodedText);

                        // 3. Dừng hình Camera lại ngay lập tức (Pause)
                        html5QrCode.pause();

                        // 4. Hiện loading
                        document.getElementById('scan-loading').classList.remove('d-none');

                        // 5. Gọi Livewire xử lý
                        @this.handleScan(decodedText);
                    },
                    (errorMessage) => {
                        /* Bỏ qua lỗi quét trượt */ }
                ).catch(err => console.log("Lỗi cam:", err));
            };

            // Khởi động cam lần đầu
            startCamera();

            // --- LẮNG NGHE SỰ KIỆN TỪ SERVER ---

            // Khi Livewire xử lý xong (dù lỗi hay thành công)
            Livewire.on('scan-finished', () => {
                document.getElementById('scan-loading').classList.add('d-none');
                // Lưu ý: Ta KHÔNG resume camera ở đây.
                // Ta đợi người dùng bấm nút "Quét tiếp theo" mới resume.
            });

            // Khi người dùng bấm nút "Quét tiếp"
            Livewire.on('resume-camera', () => {
                isProcessing = false; // Mở khóa
                // Resume camera
                html5QrCode.resume();
            });

            // Âm thanh
            Livewire.on('play-success-sound', () => {
                // beep.play(); // Nếu có file âm thanh
            });

            Livewire.on('play-error-sound', () => {
                // error.play();
            });
        });
    </script>
</div>
