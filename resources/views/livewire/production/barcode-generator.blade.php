<div>
    <div class="container py-4">

        <div class="card shadow-sm mb-4 d-print-none">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa-solid fa-barcode me-2"></i>Phát hành Tem & Barcode</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">

                    {{-- CỘT TRÁI: CẤU HÌNH IN --}}
                    <div class="col-md-4 border-end">

                        {{-- 1. Chọn Phân Xưởng --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Phân Xưởng</label>
                            <select wire:model.live="selectedDeptCode" class="form-select">
                                @if (count($departments) > 0)
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->code }}">
                                            {{ $dept->name }} ({{ $dept->code }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Bạn chưa được phân quyền bộ phận nào</option>
                                @endif
                            </select>
                        </div>

                        {{-- 2. Chọn Loại Tem --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Loại Tem</label>
                            <select wire:model.live="type" class="form-select">
                                <option value="RM">Nguyên liệu (RM)</option>
                                <option value="WI">Bán thành phẩm (WI)</option>
                                <option value="FG">Thành phẩm (FG)</option>
                            </select>
                        </div>

                        {{-- 3. Số lượng --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">3. Số lượng tem</label>
                            <input wire:model="quantity" type="number" class="form-control" min="1" max="100">
                        </div>

                        {{-- 4. Tùy chọn Định dạng In (MỚI) --}}
                        <div class="mb-3 bg-light p-3 rounded border">
                            <label class="form-label fw-bold small text-uppercase text-muted mb-2">Định dạng mã in</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="printFormat" value="QR" id="fmtQR">
                                    <label class="form-check-label fw-bold cursor-pointer" for="fmtQR">
                                        <i class="fa-solid fa-qrcode text-primary me-1"></i> QR Code
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="printFormat" value="BARCODE" id="fmtBar">
                                    <label class="form-check-label fw-bold cursor-pointer" for="fmtBar">
                                        <i class="fa-solid fa-barcode text-dark me-1"></i> Barcode 1D
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- CỘT PHẢI: NHẬP THÔNG TIN CHI TIẾT --}}
                    <div class="col-md-8">
                        <h6 class="text-primary fw-bold mb-3">Thông tin lô hàng</h6>
                        <div class="row g-2">

                            {{-- Chọn Đơn Hàng --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Chọn Đơn Hàng (PO) <span class="text-danger">*</span></label>
                                <select wire:model.live="itemData.ORDER_ID" class="form-select @error('itemData.ORDER_ID') is-invalid @enderror">
                                    <option value="">-- Chọn Đơn Hàng --</option>
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}">{{ $order->code }} - {{ $order->customer_name }}</option>
                                    @endforeach
                                </select>
                                @error('itemData.ORDER_ID')
                                    <span class="text-danger small fst-italic">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Chọn Model --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Chọn Model/Mã Hàng <span class="text-danger">*</span></label>
                                <select wire:model.live="itemData.PRODUCT_MODEL_ID" class="form-select @error('itemData.PRODUCT_MODEL_ID') is-invalid @enderror">
                                    <option value="">-- Chọn Model ({{ count($availableModels) }}) --</option>
                                    @foreach ($availableModels as $model)
                                        <option value="{{ $model->id }}">{{ $model->code }} - {{ $model->name }}</option>
                                    @endforeach
                                </select>
                                @error('itemData.PRODUCT_MODEL_ID')
                                    <span class="text-danger small fst-italic">{{ $message }}</span>
                                @enderror
                                @if (empty($availableModels) && $selectedDeptCode)
                                    <small class="text-warning">⚠️ Xưởng này chưa có Model nào.</small>
                                @endif
                            </div>

                            {{-- Mã Vải (Tự động từ Model) --}}
                            <div class="col-md-4">
                                <label class="form-label small">Mã Vải (Auto)</label>
                                <input wire:model="itemData.MA_VAI" type="text" class="form-control text-uppercase bg-light" readonly>
                            </div>
                        </div>

                        {{-- Các trường nhập liệu chi tiết --}}
                        <div class="row g-2 mt-1">
                            <div class="col-md-4">
                                <label class="form-label small">Mã Vải (Gốc) <span class="text-danger">*</span></label>
                                <input wire:model="itemData.MA_VAI" type="text" class="form-control text-uppercase @error('itemData.MA_VAI') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Mã Cây/Lô</label>
                                <input wire:model="itemData.MA_CAY_VAI" type="text" class="form-control text-uppercase">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">PO Text</label>
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
                            <div class="col-md-6">
                                <label class="form-label small">Ghi chú</label>
                                <input wire:model="itemData.GHI_CHU" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button wire:click="generate" class="btn btn-success px-4 fw-bold">
                                <i class="fa-solid fa-plus me-1"></i> Tạo Mới & In Ngay
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
                    <button wire:click="reprintSelected" class="btn btn-sm btn-dark shadow-sm">
                        <i class="fa-solid fa-print me-1"></i>
                        In lại {{ count($selectedHistoryIds) }} tem đã chọn
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
                                    <input type="checkbox" wire:model.live="selectedHistoryIds" value="{{ $item->id }}" class="form-check-input" style="cursor: pointer;">
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
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            @endphp

            <div class="print-area">
                <div class="row g-2">
                    @foreach ($generatedItems as $item)
                        <div class="col-md-4 col-6 mb-3">
                            <div class="label-item">
                                {{-- Header Tem --}}
                                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                    <span class="fw-bold text-uppercase small">{{ $item['info']['MA_VAI'] ?? '' }}</span>
                                    <span class="fw-bold small">{{ $item['info']['MAU'] ?? '' }}</span>
                                </div>

                                {{-- Code Area (QR hoặc Barcode) --}}
                                <div class="barcode-wrapper text-center d-flex justify-content-center flex-column align-items-center" style="min-height: 70px;">
                                    @if($printFormat == 'QR')
                                        {{-- Render QR Code --}}
                                        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(60)->generate($item['code']) !!}
                                    @else
                                        {{-- Render Barcode 1D --}}
                                        {!! $generator->getBarcode($item['code'], $generator::TYPE_CODE_128, 2, 45) !!}
                                    @endif

                                    <div class="code-text fw-bold mt-1" style="font-size: 14px; letter-spacing: 1px;">{{ $item['code'] }}</div>
                                </div>

                                {{-- Footer Tem --}}
                                <div class="info-grid mt-2 small text-start border-top pt-1">
                                    <div class="row g-0">
                                        <div class="col-6"><strong>PO:</strong> {{ $item['info']['PO'] ?? '' }}</div>
                                        <div class="col-6 text-end"><strong>Kho:</strong> {{ $item['info']['KHO'] ?? '' }}</div>
                                        <div class="col-12 text-truncate"><strong>Note:</strong> {{ $item['info']['GHI_CHU'] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- SCRIPT: TỰ ĐỘNG BẬT CỬA SỔ IN --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('trigger-print', () => {
                setTimeout(() => {
                    window.print();
                }, 500); // Đợi 0.5s để ảnh QR render xong mới in
            });
        });
    </script>

    {{-- CSS: ĐỊNH DẠNG TEM VÀ CHẾ ĐỘ IN --}}
    <style>
        /* Giao diện trên màn hình */
        .label-item {
            border: 1px dashed #333;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
        }
        .barcode-wrapper svg {
            max-width: 100%;
            height: auto;
        }
        .cursor-pointer {
            cursor: pointer;
        }

        /* Giao diện khi bấm In (Ctrl + P) */
        @media print {
            /* Ẩn tất cả mọi thứ trên trang */
            body * {
                visibility: hidden;
            }

            /* Chỉ hiện khu vực print-area */
            .print-area,
            .print-area * {
                visibility: visible;
            }

            /* Định vị khu vực in lên góc trên cùng */
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0;
                margin: 0;
            }

            /* Định dạng tem khi in ra */
            .label-item {
                border: 1px solid #000 !important; /* Viền đen đậm */
                border-radius: 0;
                page-break-inside: avoid; /* Không bị cắt đôi khi hết trang */
                margin-bottom: 2mm;
                padding: 2mm !important;
            }

            /* Chia cột in (2 tem 1 hàng hoặc tùy chỉnh) */
            .col-6 {
                width: 50% !important;
                float: left;
            }
        }
    </style>
</div>
