<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-12">

            {{-- HEADER: CẤU HÌNH LỌC & GÁN DỮ LIỆU --}}
            <div class="card shadow-sm mb-3 border-primary">
                <div class="card-header bg-primary text-white py-1">
                    <small class="fw-bold"><i class="fa-solid fa-gears me-1"></i> Thiết lập quét (Để trống = Giữ
                        nguyên)</small>
                </div>
                <div class="card-body py-2 ">
                    <div class="row g-2">
                        <div class="col-12 col-lg-4">
                            <label class="small text-muted fw-bold" for="selectedMachineId">
                                <i class="fa-solid fa-gears me-1"></i>Chọn Máy Thực Hiện
                            </label>
                            <select wire:model="selectedMachineId" id="selectedMachineId"
                                class="form-select form-select-sm border-primary fw-bold text-primary mb-2">
                                <option value="">-- Không chọn / Không rõ --</option>
                                @foreach ($machines as $machine)
                                    <option value="{{ $machine->id }}">
                                        [{{ $machine->code }}] {{ $machine->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        {{-- 1. Chọn Đơn Hàng --}}
                        <div class="col-6 col-lg-4">
                            <label class="small text-muted fw-bold" for="selectedOrderId">Gán Đơn Hàng (PO)</label>
                            <select wire:model.live="selectedOrderId" id="selectedOrderId"
                                class="form-select form-select-sm border-secondary fw-bold text-primary">
                                <option value="">-- Mặc định --</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}">{{ $order->code }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Chọn Model --}}
                        <div class="col-6 col-lg-4">
                            <label class="small text-muted fw-bold" for="selectedProductId">Gán Model</label>
                            <select wire:model.live="selectedProductId" id="selectedProductId"
                                class="form-select form-select-sm border-secondary fw-bold text-success">
                                <option value="">-- Mặc định --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}
                                    </option>
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
                        <x-scanner inputModel="scannedCodeInput" onEnter="handleKeyInput" onScan="handleScan"
                            placeholder="Quét hoặc nhập mã tem..." buttonText="Xác nhận và Tra cứu" />

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
                                {{-- MỚI THÊM: Nút Quét lại hiển thị khi có lỗi hoặc cảnh báo --}}
                                @if ($scanStatus == 'error' || $scanStatus == 'warning')
                                    <button wire:click="resetScan" class="btn btn-sm btn-outline-dark mt-3">
                                        <i class="fa-solid fa-rotate-right me-1"></i> Bấm để quét lại
                                    </button>
                                @endif
                            </div>
                        @endif

                        @if (!empty($itemInfo))
                            <div class="table-responsive  rounded p-2 border position-relative">
                                <div class="position-absolute top-0 end-0 m-2 d-flex gap-2">
                                    @if ($scannedItemId)
                                        <button wire:click="reprintItems([{{ $scannedItemId }}])"
                                            class="btn btn-sm btn-outline-info" title="In lại tem này">
                                            <i class="fa-solid fa-print"></i> In Lại
                                        </button>
                                    @endif
                                    <button wire:click="resetScan" class="btn btn-sm btn-outline-secondary"
                                        title="Quét lại từ đầu">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </button>
                                </div>
                                <table class="table table-borderless mb-0 mt-3">
                                    <tr>
                                        <td class="text-muted small" width="30%">Mã Vải:</td>
                                        <td class="fw-bold fs-5 text-primary">
                                            {{ $itemInfo->product->name ?? 'N/A' }}
                                            @if (!empty($selectedModelId))
                                                <i class="fa-solid fa-pen-to-square text-warning small ms-1"
                                                    title="Đã cập nhật theo cài đặt"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small">Màu:</td>
                                        <td class="fw-bold">{{ $itemInfo->color->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small">Thông số:</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $itemInfo->original_length ?? 0 }}
                                                m</span>
                                            <span class="badge bg-info text-dark">{{ $itemInfo->length ?? 0 }}
                                                m</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small">PO:</td>
                                        <td>
                                            {{ $itemInfo->order->code ?? '' }}
                                            @if (!empty($selectedOrderId))
                                                <i class="fa-solid fa-pen-to-square text-warning small ms-1"
                                                    title="Đã cập nhật theo cài đặt"></i>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        @endif

                        {{-- Placeholder --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- SCRIPTS --}}
        {{-- 2. Thư viện SweetAlert2 --}}
        <script src="/js/sweetalert2@11.js" type="text/javascript"></script>

        <script>
            // 3. Sửa lại đường dẫn âm thanh dùng link Online của Google (để không phải tải file về)
            const audioSuccess = new Audio('/audio/cartoon_boing.ogg');
            const audioError = new Audio('/audio/beep_short.ogg');

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
        </script>
    </div>
