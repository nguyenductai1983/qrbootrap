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
                    </div>
                </div>
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-qrcode me-2"></i>QUÉT SẢN PHẨM</h5>
                    </div>

                    <div class="card-body">
                        <x-scanner inputModel="scannedCodeInput" onEnter="handleKeyInput" onScan="handleScan"
                            placeholder="Quét hoặc nhập mã tem..." buttonText="Xác nhận và Tra cứu" />
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
                                    <button wire:click="reprintItems([{{ $scannedItemId }}])"
                                        class="form-control btn btn-sm btn-outline-info" title="In lại tem này">
                                        <i class="fa-solid fa-print"></i> In Lại
                                    </button>

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

                                    {{-- Số mét thực (Mobile Optimized) --}}
                                    <div class="p-2 bg-light rounded border border-warning">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted small fw-bold">Số mét thực:</span>
                                            <div>
                                                <span class="text-muted small me-1">Gốc:</span>
                                                <span
                                                    class="badge bg-secondary fs-6">{{ $itemInfo->original_length ?? 0 }}
                                                    m</span>
                                            </div>
                                        </div>
                                        <div class="input-group input-group-lg">
                                            <input type="number" inputmode="decimal" step="0.1" min="0"
                                                wire:model="editLength" wire:keydown.enter="updateLength"
                                                class="form-control border-warning fw-bold text-center fs-3 text-primary"
                                                placeholder="0.0" style="height: 55px;">
                                            <span class="input-group-text bg-warning text-dark fw-bold px-3">m</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- FORM CẬP NHẬT SỐ MÉT --}}
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
                                                <button wire:click="updateLength" wire:loading.attr="disabled"
                                                    class="form-control btn btn-warning fw-bold">
                                                    <span wire:loading.remove wire:target="updateLength">
                                                        <i class="fa-solid fa-floppy-disk me-1"></i>Lưu thông tin
                                                    </span>
                                                    <span wire:loading wire:target="updateLength">
                                                        <i class="fa-solid fa-spinner fa-spin me-1"></i>Đang lưu...
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
