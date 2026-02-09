<div>
    <div class="container py-4">

        <div class="card shadow-sm mb-4 d-print-none">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa-solid fa-barcode me-2"></i>Phát hành Tem & Barcode</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4 border-end">
                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Phân Xưởng</label>
                            <select wire:model="selectedDeptCode" class="form-select">
                                @if (count($departments) > 0)
                                    @foreach ($departments as $dept)
                                        {{-- Lưu ý: $dept bây giờ là Object Model, không phải mảng --}}
                                        <option value="{{ $dept->code }}">
                                            {{ $dept->name }} ({{ $dept->code }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Bạn chưa được phân quyền bộ phận nào</option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Loại Tem</label>
                            <select wire:model.live="type" class="form-select">
                                <option value="RM">Nguyên liệu (RM)</option>
                                <option value="WI">Bán thành phẩm (WI)</option>
                                <option value="FG">Thành phẩm (FG)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">3. Số lượng</label>
                            <input wire:model="quantity" type="number" class="form-control" min="1"
                                max="100">
                        </div>
                    </div>

                    <div class="col-md-8">
                        <h6 class="text-primary fw-bold mb-3">Thông tin lô hàng</h6>
                        <div class="row g-2">

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Chọn Đơn Hàng (PO) <span
                                        class="text-danger">*</span></label>
                                <select wire:model.live="itemData.ORDER_ID" class="form-select">
                                    <option value="">-- Chọn Đơn Hàng --</option>
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}">{{ $order->code }} -
                                            {{ $order->customer_name }}</option>
                                    @endforeach
                                </select>
                                {{-- ĐÂY LÀ DÒNG QUAN TRỌNG ĐỂ HIỆN CHỮ ĐỎ --}}
                                @error('itemData.ORDER_ID')
                                    <span class="text-danger small fst-italic">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Chọn Model/Mã Hàng <span
                                        class="text-danger">*</span></label>
                                <select wire:model.live="itemData.PRODUCT_MODEL_ID" class="form-select">
                                    <option value="">-- Chọn Model ({{ count($availableModels) }}) --</option>
                                    @foreach ($availableModels as $model)
                                        <option value="{{ $model->id }}">{{ $model->code }} - {{ $model->name }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- ĐÂY LÀ DÒNG QUAN TRỌNG ĐỂ HIỆN CHỮ ĐỎ --}}
                                @error('itemData.PRODUCT_MODEL_ID')
                                    <span class="text-danger small fst-italic">{{ $message }}</span>
                                @enderror
                                @if (empty($availableModels))
                                    <small class="text-danger">Xưởng này chưa được gán Model nào.</small>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Mã Vải (Tự động)</label>
                                <input wire:model="itemData.MA_VAI" type="text"
                                    class="form-control text-uppercase bg-light" readonly>
                            </div>

                        </div>
                        <div class="row g-2">

                            <div class="col-md-4">
                                <label class="form-label small">Mã Vải <span class="text-danger">*</span></label>
                                <input wire:model="itemData.MA_VAI" type="text" class="form-control text-uppercase">
                                @error('itemData.MA_VAI')
                                    <span class="text-danger small">Bắt buộc</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Mã Cây/Lô</label>
                                <input wire:model="itemData.MA_CAY_VAI" type="text"
                                    class="form-control text-uppercase">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">PO</label>
                                <input wire:model="itemData.PO" type="text" class="form-control text-uppercase">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Màu</label>
                                <input wire:model="itemData.MAU" type="text" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Khổ</label>
                                <input wire:model="itemData.KHO" type="text" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Ghi chú</label>
                                <input wire:model="itemData.GHI_CHU" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button wire:click="generate" class="btn btn-success px-4">
                                <i class="fa-solid fa-plus me-1"></i> Tạo Mới & In
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm d-print-none mt-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử tạo tem</h6>

                {{-- Nút in lại chỉ hiện khi có item được chọn --}}
                @if (count($selectedHistoryIds) > 0)
                    <button wire:click="reprintSelected" class="btn btn-sm btn-dark">
                        <i class="fa-solid fa-print me-1"></i>
                        In {{ count($selectedHistoryIds) }} tem đã chọn
                    </button>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="40" class="text-center">#</th>
                            <th>Mã Barcode</th>
                            <th>Mã Vải</th>
                            <th>Màu</th>
                            <th>Lô/Batch</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historyItems as $item)
                            <tr class="{{ in_array($item->id, $selectedHistoryIds) ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    {{-- CHECKBOX CHỌN IN --}}
                                    <input type="checkbox" wire:model.live="selectedHistoryIds"
                                        value="{{ $item->id }}" class="form-check-input"
                                        style="cursor: pointer;">
                                </td>
                                <td class="fw-bold text-primary">{{ $item->code }}</td>
                                <td>{{ $item->properties['MA_VAI'] ?? '-' }}</td>
                                <td>{{ $item->properties['MAU'] ?? '-' }}</td>
                                <td>{{ $item->properties['MA_CAY_VAI'] ?? 'N/A' }}</td>
                                <td class="small text-muted">{{ $item->created_at->format('d/m H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-2">
                    {{ $historyItems->links() }}
                </div>
            </div>
        </div>

        @if (count($generatedItems) > 0)
            @php $generator = new \Picqer\Barcode\BarcodeGeneratorSVG(); @endphp

            <div class="print-area">
                <div class="row g-2">
                    @foreach ($generatedItems as $item)
                        <div class="col-md-4 col-6 mb-3">
                            <div class="label-item">
                                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                    <span
                                        class="fw-bold text-uppercase small">{{ $item['info']['MA_VAI'] ?? '' }}</span>
                                    <span class="fw-bold small">{{ $item['info']['MAU'] ?? '' }}</span>
                                </div>
                                <div class="barcode-wrapper text-center">
                                    {!! $generator->getBarcode($item['code'], $generator::TYPE_CODE_128, 2, 45) !!}
                                    <div class="code-text fw-bold mt-1">{{ $item['code'] }}</div>
                                </div>
                                <div class="info-grid mt-2 small text-start">
                                    <div class="row g-0">
                                        <div class="col-6"><strong>PO:</strong> {{ $item['info']['PO'] ?? '' }}</div>
                                        <div class="col-6 text-end"><strong>Kho:</strong>
                                            {{ $item['info']['KHO'] ?? '' }}</div>
                                        <div class="col-12 text-truncate"><strong>Note:</strong>
                                            {{ $item['info']['GHI_CHU'] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('trigger-print', () => {
                setTimeout(() => {
                    window.print();
                }, 500);
            });
        });
    </script>

    <style>
        .label-item {
            border: 1px dashed #333;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
        }

        .barcode-wrapper svg {
            width: 100%;
            max-width: 220px;
            height: 45px;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
                visibility: visible;
            }

            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .label-item {
                border: 1px solid #000 !important;
                border-radius: 0;
                page-break-inside: avoid;
                margin-bottom: 2mm;
                padding: 2mm !important;
            }

            .col-6 {
                width: 50% !important;
                float: left;
            }
        }
    </style>
</div>
