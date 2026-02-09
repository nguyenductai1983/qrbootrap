<div> {{-- 1. THẺ GỐC DUY NHẤT BAO BỌC TẤT CẢ --}}

    <div class="container py-4">
        <div class="card shadow-sm mb-4 d-print-none">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa-solid fa-print me-2"></i>Tạo & In Tem Mã Vạch</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Loại Tem</label>
                        <select wire:model.live="type" class="form-select">
                            <option value="RM">Nguyên liệu (RM-)</option>
                            <option value="WIP">Bán thành phẩm (WIP-)</option>
                            <option value="FG">Thành phẩm (FG-)</option>
                            <option value="LOC">Vị trí (LOC-)</option>
                        </select>
                    </div>

                    @if ($type === 'LOC')
                        <div class="col-md-6">
                            <label class="form-label">Mã vị trí (Nhập tay phần đuôi)</label>
                            <div class="input-group">
                                <span class="input-group-text">LOC-</span>
                                <input wire:model="manualCode" type="text" class="form-control"
                                    placeholder="VD: KHO-01">
                            </div>
                        </div>
                    @else
                        <div class="col-md-3">
                            <label class="form-label">Số lượng tem</label>
                            <input wire:model="quantity" type="number" class="form-control" min="1"
                                max="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Số bắt đầu (STT)</label>
                            <input wire:model="startIndex" type="number" class="form-control">
                        </div>
                    @endif

                    <div class="col-md-12 text-end">
                        <button wire:click="generate" class="btn btn-success">
                            <i class="fa-solid fa-rotate me-1"></i> Tạo Mã
                        </button>
                        @if (count($generatedCodes) > 0)
                            <button onclick="window.print()" class="btn btn-secondary ms-2">
                                <i class="fa-solid fa-print me-1"></i> In Ngay
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (count($generatedCodes) > 0)
            <div class="print-area">
                <div class="row g-3">
                    @foreach ($generatedCodes as $code)
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card border-dark h-100 text-center p-2 label-item">
                                <div class="d-flex justify-content-center mb-2">
                                    {!! QrCode::size(100)->generate($code) !!}
                                </div>
                                <div class="fw-bold fs-5">{{ $code }}</div>
                                <div class="small text-muted">
                                    {{ $type == 'RM' ? 'NGUYÊN LIỆU' : ($type == 'LOC' ? 'VỊ TRÍ' : 'SẢN PHẨM') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="alert alert-info d-print-none">Chưa có mã nào được tạo. Vui lòng chọn thông số và bấm "Tạo Mã".
            </div>
        @endif
    </div>

    <style>
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
                page-break-inside: avoid;
                margin-bottom: 10px;
            }

            .col-6 {
                width: 50% !important;
                float: left;
            }
        }
    </style>

</div> {{-- KẾT THÚC THẺ GỐC --}}
