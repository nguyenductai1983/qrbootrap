<div>
    {{-- CSS: ĐỊNH DẠNG TEM VÀ CHẾ ĐỘ IN --}}
    <style>
        /* 1. Giao diện trên màn hình (Luôn chia 2 hoặc 3 cột cho dễ nhìn) */
        .print-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        @media (min-width: 992px) {
            .print-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .label-item {
            border: 1px dashed #333;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
            width: 100%;
        }

        .barcode-wrapper svg {
            max-width: 100%;
            height: auto;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* 2. Giao diện khi bấm In (Ctrl + P) - BIẾN HÌNH THEO LỰA CHỌN CỦA USER */
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
                padding: 0;
                margin: 0;
            }

            /* LƯỚI IN THÔNG MINH */
            .print-grid {
                display: grid;
                /* Lấy đúng số cột mà user chọn trên giao diện */
                grid-template-columns: repeat(var(--print-cols), 1fr);
                gap: 2mm;
                /* Khoảng cách giữa các tem */
            }

            .label-item {
                border: 1px solid #000 !important;
                border-radius: 0;
                page-break-inside: avoid;
                padding: 2mm !important;
                margin-bottom: 0;
                /* Đã có gap lo khoảng cách */
            }
        }
    </style>
    <div class="container py-4">

        <div class="card shadow-sm mb-4 d-print-none">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa-solid fa-barcode me-2"></i>Phát hành Tem & Barcode</h5>
            </div>
            <div class="card-body position-relative"> {{-- Thêm position-relative vào đây --}}

                {{-- 🌟 LỚP MÀN MỜ BẢO VỆ CHỐNG CLICK NHANH 🌟 --}}
                <div wire:loading class="position-absolute w-100 h-100 top-0 start-0 bg-white"
                    style="opacity: 0.6; z-index: 10; cursor: not-allowed;">
                    {{-- Có thể thêm icon xoay xoay ở giữa cho sinh động (Tùy chọn) --}}
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
                <div class="row g-4">

                    {{-- CỘT TRÁI: CẤU HÌNH IN --}}
                    <div class="col-md-4 border-end">

                        {{-- 1. Chọn Phân Xưởng --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phân Xưởng</label>
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
                            <label class="form-label fw-bold">Loại Tem</label>
                            <select wire:model.live="type" class="form-select text-primary fw-bold">
                                @if (count($itemTypes) > 0)
                                    @foreach ($itemTypes as $t)
                                        <option value="{{ $t->code }}">{{ $t->code }} - {{ $t->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">-- Chưa có loại tem nào --</option>
                                @endif
                            </select>
                        </div>
                        {{-- 3. Số lượng --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số lượng tem</label>
                            <input wire:model="quantity" type="number" class="form-control" min="1"
                                max="100">
                        </div>

                        {{-- 4. Tùy chọn Định dạng In (MỚI) --}}
                        <div class="mb-3 p-3 rounded border">
                            <label class="form-label fw-bold small text-uppercase text-muted mb-2">Định dạng mã
                                in</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="printFormat"
                                        value="QR" id="fmtQR">
                                    <label class="form-check-label fw-bold cursor-pointer" for="fmtQR">
                                        <i class="fa-solid fa-qrcode text-primary me-1"></i> QR Code
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="printFormat"
                                        value="BARCODE" id="fmtBar">
                                    <label class="form-check-label fw-bold cursor-pointer" for="fmtBar">
                                        <i class="fa-solid fa-barcode text-dark me-1"></i> Barcode 1D
                                    </label>
                                </div>
                            </div>
                        </div>
                        {{-- 5. Số tem / Hàng (Khổ giấy in) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Khổ giấy in (Số tem ngang)</label>
                            <select wire:model.live="printColumns" class="form-select border-primary">
                                <option value="1">Máy in nhiệt cuộn (1 tem/dòng)</option>
                                <option value="2">Giấy A4 Decal (2 tem/dòng)</option>
                            </select>
                            <small class="text-muted">Hệ thống sẽ tự động canh lề khớp với giấy in.</small>
                        </div>
                    </div>

                    {{-- CỘT PHẢI: NHẬP THÔNG TIN CHI TIẾT --}}
                    <div class="col-md-8">
                        <h6 class="text-primary fw-bold mb-3">Thông tin lô hàng</h6>
                        <div class="row g-2">

                            {{-- Chọn Đơn Hàng --}}
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <label class="form-label small fw-bold mb-0">Chọn Đơn Hàng (PO) <span
                                            class="text-danger">*</span></label>
                                    {{-- Nút gọi Modal tạo nhanh --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0"
                                        data-bs-toggle="modal" data-bs-target="#quickOrderModal">
                                        <i class="fa-solid fa-plus me-1"></i> Tạo nhanh
                                    </button>
                                </div>

                                {{-- Thêm wire:key="select-po-{{ count($orders) }}" vào thẳng thẻ select --}}
                                <select wire:key="select-po-{{ count($orders) }}" wire:model.live="itemData.ORDER_ID"
                                    class="form-select @error('itemData.ORDER_ID') is-invalid @enderror">
                                    <option value="">-- Chọn Đơn Hàng ({{ count($orders) }}) --</option>
                                    @foreach ($orders as $order)
                                        <option value="{{ $order->id }}" wire:key="opt-po-{{ $order->id }}">
                                            {{ $order->code }} - {{ $order->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('itemData.ORDER_ID')
                                    <span class="text-danger small fst-italic">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- MODAL TẠO NHANH ĐƠN HÀNG --}}
                            <div wire:ignore.self class="modal fade" id="quickOrderModal" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold text-primary">Tạo Nhanh Đơn Hàng</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form wire:submit="quickCreateOrder">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Loại Đơn Hàng <span
                                                            class="text-danger">*</span></label>
                                                    <select wire:model="newOrderType" class="form-select" required>
                                                        {{-- Lấy từ Enum hoặc viết cứng tạm thời --}}
                                                        <option value="C">Đơn hàng loại C</option>
                                                        <option value="F">Đơn hàng loại F</option>
                                                        <option value="H">Đơn hàng loại H</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Số lượng sản phẩm <span
                                                            class="text-danger">*</span></label>
                                                    <input wire:model="newOrderTotal" type="number"
                                                        class="form-control" min="1">
                                                    @error('newOrderTotal')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Tên Khách Hàng / Đối tác
                                                        <span class="text-danger">*</span></label>
                                                    <input wire:model="newOrderCustomer" type="text"
                                                        class="form-control" placeholder="Nhập tên khách..." required>
                                                    @error('newOrderCustomer')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                {{-- Giải thích quy tắc cho người dùng hiểu --}}
                                                <div class="alert alert-info py-2 small mb-0">
                                                    <i class="fa-solid fa-circle-info me-1"></i> Mã PO sẽ tạo tự động:
                                                    <strong>[Loại] + [STT] + [Tháng] + [Năm]</strong>.
                                                    <br>Ví dụ: <strong>C001{{ date('my') }}</strong>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa-solid fa-check me-1"></i> Tạo & Chọn Ngay
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- Chọn Model --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Chọn Mã Hàng <span
                                        class="text-danger">*</span></label>
                                <select wire:model.live="itemData.PRODUCT_ID"
                                    class="form-select @error('itemData.PRODUCT_ID') is-invalid @enderror">
                                    <option value="">-- Chọn Mã Hàng ({{ count($availableProducts) }}) --
                                    </option>
                                    @foreach ($availableProducts as $product)
                                        <option value="{{ $product->id }}">{{ $product->code }} -
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('itemData.PRODUCT_ID')
                                    <span class="text-danger small fst-italic">{{ $message }}</span>
                                @enderror
                                @if (empty($availableProducts) && $selectedDeptCode)
                                    <small class="text-warning">⚠️ Xưởng này chưa có Mã Hàng nào.</small>
                                @endif
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Loại Nhựa</label>
                                <select wire:model="selectedPlastic" class="form-select">
                                    <option value="">-- Chọn Loại Nhựa --</option>
                                    @foreach ($plasticTypes as $plastic)
                                        <option value="{{ $plastic->id }}">{{ $plastic->code }} -
                                            {{ $plastic->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Quy Cách</label>
                                <select wire:model="selectedSpec" class="form-select">
                                    <option value="">-- Chọn Quy Cách --</option>
                                    @foreach ($specifications as $specification)
                                        <option value="{{ $specification->id }}">{{ $specification->code }} -
                                            {{ $specification->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Màu</label>
                                <select wire:model="selectedColor" class="form-select">
                                    <option value="">-- Chọn Màu --</option>
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}">{{ $color->code }} -
                                            {{ $color->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-2 align-items-stretch">

                                    {{-- Nút link sang trang quản lý (Chiếm 7 phần chiều ngang) --}}
                                    <a href="{{ route('manager.categories') }}" target="_new" style="flex: 7;"
                                        class="list-group-item list-group-item-action text-primary border rounded px-3 py-2 d-flex justify-content-center align-items-center {{ request()->routeIs('manager.categories') ? 'active' : '' }}">
                                        <span><i class="fa-solid fa-tags me-1"></i> Tạo mới: Nhựa - Quy Cách -
                                            Màu</span>
                                    </a>

                                    {{-- Nút bấm Làm mới danh sách (Chiếm 3 phần chiều ngang) --}}
                                    <button type="button" wire:click="refreshMasterData" style="flex: 3;"
                                        class="btn btn-outline-success shadow-sm px-3 d-flex justify-content-center align-items-center"
                                        title="Tải lại dữ liệu ngay lập tức">
                                        <span>
                                            <i class="fa-solid fa-arrows-rotate me-1" wire:loading.class="fa-spin"
                                                wire:target="refreshMasterData"></i>
                                            Tải danh mục
                                        </span>
                                    </button>

                                </div>
                            </div>
                        </div>
                        {{-- Các trường nhập liệu chi tiết --}}
                        {{-- Thay thế toàn bộ khối nhập "Thông tin chi tiết" cứng của bạn bằng khối này --}}
                        <div class="row g-2 mt-2 border-top pt-2">
                            <label class="small text-muted fw-bold w-100 mb-1">Thông tin chi tiết (Thuộc tính
                                động):</label>

                            @foreach ($dynamicProperties as $prop)
                                <div class="col-6">
                                    <label class="form-label mb-1" style="font-size: 0.85rem;">
                                        {{ $prop->name }}
                                        @if ($prop->is_required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if ($prop->type === 'select' && is_array($prop->options))
                                        <select wire:model="itemData.{{ $prop->code }}"
                                            class="form-select form-select-sm">
                                            <option value="">-- Chọn --</option>
                                            @foreach ($prop->options as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $prop->type === 'number' ? 'number' : 'text' }}"
                                            wire:model="itemData.{{ $prop->code }}"
                                            class="form-control form-control-sm"
                                            placeholder="Nhập {{ strtolower($prop->name) }}">
                                    @endif

                                    @error('itemData.' . $prop->code)
                                        <span class="text-danger" style="font-size: 0.75rem;">Vui lòng nhập
                                            {{ $prop->name }}</span>
                                    @enderror
                                </div>
                            @endforeach
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
            <div class="card-headerd-flex justify-content-between align-items-center">
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
                    <thead>
                        <tr>
                            <th width="40" class="text-center">#</th>
                            <th>Mã Barcode</th>
                            <th>Đơn hàng</th>
                            <th>Màu</th>
                            <th>Sản phẩm</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historyItems as $item)
                            <tr class="{{ in_array($item->id, $selectedHistoryIds) ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    <input type="checkbox" wire:model.live="selectedHistoryIds"
                                        value="{{ $item->id }}" class="form-check-input"
                                        style="cursor: pointer;">
                                </td>
                                <td class="fw-bold text-primary">{{ $item->code }}</td>
                                <td>{{ $item->order->code ?? '-' }}</td>
                                <td>{{ $item->properties['MAU'] ?? '-' }}</td>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
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

        {{-- KHU VỰC IN TEM (ẨN TRÊN MÀN HÌNH, CHỈ HIỆN KHI IN) --}}
        @if (count($generatedItems) > 0)
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            @endphp

            <div class="print-area">
                {{-- Chuyền biến $printColumns từ PHP sang CSS thông qua thẻ style nội tuyến --}}
                <div class="print-grid" style="--print-cols: {{ $printColumns }};">
                    @foreach ($generatedItems as $item)
                        {{-- BỎ class col-6 col-md-4 ở đây đi --}}
                        <div class="label-item">
                            {{-- Header Tem --}}
                            <div class="d-flex justify-content-between border-bottom pb-1 mb-1 w-100">
                                {{-- Nhóm 1: Tên Sản Phẩm (Nằm bên trái) --}}
                                <div class="text-truncate pe-2">
                                    <strong class="small text-muted">SP:</strong>
                                    <span
                                        class="fw-bold text-uppercase small">{{ $item['info']['PRODUCT_NAME'] ?? '' }}</span>
                                </div>

                                {{-- Nhóm 2: Màu (Nằm bên phải) --}}
                                <div class="text-end flex-shrink-0">
                                    <strong class="small text-muted">MÀU:</strong>
                                    <span class="fw-bold small">{{ $item['info']['MAU'] ?? '' }}</span>
                                </div>
                            </div>

                            {{-- Code Area (QR hoặc Barcode) - ĐÃ CẬP NHẬT GIAO DIỆN BIẾN HÌNH --}}
                            <div class="barcode-wrapper" style="min-height: 70px;">

                                @if ($printFormat == 'QR')
                                    {{-- 1. LAYOUT CHO QR CODE: QR bên trái, Chữ bên phải --}}
                                    <div class="d-flex align-items-center justify-content-start h-100">
                                        {{-- Thêm class flex-shrink-0 vào đây để QR không bao giờ bị bóp nhỏ --}}
                                        <div class="me-2 flex-shrink-0">
                                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(60)->generate($item['code']) !!}
                                        </div>
                                        {{-- Thêm flex-grow-1 để khối chữ chiếm toàn bộ không gian còn lại --}}
                                        <div class="code-text fw-bold text-start flex-grow-1"
                                            style="font-size: 13px; letter-spacing: 0.5px; word-break: break-all; line-height: 1.2;">
                                            {{ $item['code'] }}
                                        </div>
                                    </div>
                                @else
                                    {{-- 2. LAYOUT CHO BARCODE 1D: Barcode ở trên (Canh giữa), Chữ ở dưới (Canh trái, tự ngắt dòng) --}}
                                    <div
                                        class="d-flex flex-column align-items-start justify-content-center h-100 pt-1">
                                        <div class="w-100 text-center"> {{-- Thẻ bọc này giúp mã vạch luôn nằm giữa --}}
                                            {!! $generator->getBarcode($item['code'], $generator::TYPE_CODE_128, 2, 45) !!}
                                        </div>
                                        <div class="code-text fw-bold mt-1 text-start w-100"
                                            style="font-size: 14px; letter-spacing: 1px; word-break: break-all;">
                                            {{ $item['code'] }}
                                        </div>
                                    </div>
                                @endif

                            </div>

                            {{-- Footer Tem --}}
                            <div class="info-grid mt-2 small text-start border-top pt-1">
                                <div class="row g-0">
                                    <div class="col-6"><strong class="small text-muted">PO:</strong>
                                        {{ $item['info']['PO'] ?? '' }}</div>
                                    <div class="col-6 text-end"><strong class="small text-muted">TYPE:</strong>
                                        {{ $item['info']['type'] ?? '' }}</div>
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
            // 1. Script in tem của bạn
            Livewire.on('trigger-print', () => {
                setTimeout(() => {
                    window.print();
                }, 500);
            });

            // 2. SCRIPT ĐÓNG MODAL TẠO NHANH ĐƠN HÀNG (SỬA LẠI Ở ĐÂY)
            Livewire.on('close-quick-order-modal', () => {
                // Lấy Modal hiện tại đang mở thay vì tạo mới
                let modalEl = document.getElementById('quickOrderModal');
                let modalInstance = bootstrap.Modal.getInstance(modalEl);

                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    // Phòng hờ nếu chưa có instance
                    let newModal = new bootstrap.Modal(modalEl);
                    newModal.hide();
                }
            });
        });
    </script>
</div>
