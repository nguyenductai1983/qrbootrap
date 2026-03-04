<div>
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
                        <label class="form-label fw-bold">Loại Sản phẩm (Thành Phẩm , Nguyên Vật Liệu, Bán thành
                            phẩm)</label>
                        <select wire:model="type" class="form-select text-primary fw-bold">
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
                        <input wire:model="quantity" type="number" class="form-control" min="1" max="100">
                    </div>

                    {{-- 4. Tùy chọn Định dạng In (MỚI) --}}
                    <div class="mb-3 p-3 rounded border">
                        <label class="form-label fw-bold small text-uppercase text-muted mb-2">Định dạng mã in</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                {{-- Đã xóa .live --}}
                                <input class="form-check-input" type="radio" wire:model="printFormat" value="QR"
                                    id="fmtQR">
                                <label class="form-check-label fw-bold cursor-pointer" for="fmtQR">
                                    <i class="fa-solid fa-qrcode text-primary me-1"></i> QR Code
                                </label>
                            </div>
                            <div class="form-check">
                                {{-- Đã xóa .live --}}
                                <input class="form-check-input" type="radio" wire:model="printFormat" value="BARCODE"
                                    id="fmtBar">
                                <label class="form-check-label fw-bold cursor-pointer" for="fmtBar">
                                    <i class="fa-solid fa-barcode text-dark me-1"></i> Barcode 1D
                                </label>
                            </div>
                        </div>
                    </div>
                    {{-- 5. Số tem / Hàng (Khổ giấy in) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Khổ giấy in (Số tem ngang)</label>
                        <select wire:model="printColumns" class="form-select border-primary">
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
                                    <i class="fa-solid fa-plus me-1"></i> Tạo nhanh PO
                                </button>
                            </div>

                            {{-- Thêm wire:key="select-po-{{ count($orders) }}" vào thẳng thẻ select --}}
                            <select wire:key="select-po-{{ count($orders) }}" wire:model="itemData.ORDER_ID"
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
                        <div wire:ignore.self class="modal fade" id="quickOrderModal" tabindex="-1" aria-hidden="true">
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
                                                <input wire:model="newOrderTotal" type="number" class="form-control"
                                                    min="1">
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
                            {{-- Sẽ tải thuộc tính tương ứng với Mã Hàng đã chọn. Nếu bạn không thấy Mã Hàng nào, hãy kiểm tra lại Phân Xưởng hoặc liên hệ quản lý để được hỗ trợ thêm. --}}
                            @error('itemData.PRODUCT_ID')
                                <span class="text-danger small fst-italic">{{ $message }}</span>
                            @enderror
                            @if (empty($availableProducts) && $selectedDeptCode)
                                <small class="text-warning">⚠️ Xưởng này chưa có Mã Hàng nào.</small>
                            @endif
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Khổ</label>
                            <select wire:model="selectedWidth" class="form-select">
                                <option value="">-- Chọn Khổ --</option>
                                @foreach ($widths as $width)
                                    <option value="{{ $width->id }}">{{ $width->code }} -
                                        {{ $width->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        {{-- Các trường nhập liệu chi tiết --}}
                        {{-- Thay thế toàn bộ khối nhập "Thông tin chi tiết" cứng của bạn bằng khối này --}}
                        <div class="col-12">
                            <div class="row g-2 mt-2 border-top pt-2">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center ">
                                        {{-- Tiêu đề bên trái --}}
                                        <label class="small text-muted fw-bold mb-0">Thông tin chi tiết (Thuộc tính
                                            động):</label>

                                        {{-- Nhóm Link và Nút bấm bên phải --}}
                                        <div class="d-flex align-items-center gap-3">
                                            <a href="{{ route('manager.properties') }}" target="_new"
                                                class="small text-decoration-none">
                                                <i class="fa-solid fa-gear me-1"></i> Quản lý thuộc tính
                                            </a>

                                            <button type="button" wire:click="refreshDynamicProperties"
                                                class="btn btn-sm btn-outline-success shadow-sm px-3 d-flex justify-content-center align-items-center"
                                                title="Tải lại dữ liệu ngay lập tức">
                                                <span>
                                                    <i class="fa-solid fa-arrows-rotate me-1"
                                                        wire:loading.class="fa-spin"
                                                        wire:target="refreshDynamicProperties"></i>
                                                    Tải thuộc tính
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {{-- Vòng lặp hiển thị các thuộc tính động --}}
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
                                                    <option value="{{ $opt }}">{{ $opt }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="{{ $prop->type === 'number' ? 'number' : 'text' }}"
                                                wire:model="itemData.{{ $prop->code }}"
                                                class="form-control form-control-sm"
                                                placeholder="Nhập {{ strtolower($prop->name) }}" @required($prop->is_required)>
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
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử tạo tem
                    </h6>

                    {{-- Nút in lại chỉ hiện khi có item được chọn --}}
                    {{-- Dùng x-show của Alpine để tự động ẩn/hiện phía Client (Trình duyệt) mà không cần gọi Server --}}
                    <button x-show="$wire.selectedHistoryIds.length > 0" style="display: none; min-width: 160px;"
                        wire:click="reprintSelected" class="btn btn-sm btn-dark shadow-sm">

                        {{-- Trạng thái bình thường --}}
                        <span wire:loading.remove wire:target="reprintSelected">
                            <i class="fa-solid fa-print me-1"></i> In lại <span
                                x-text="$wire.selectedHistoryIds.length"></span> tem
                        </span>

                        {{-- Trạng thái đang tải (Quay vòng vòng) --}}
                        <span wire:loading wire:target="reprintSelected">
                            <i class="fa-solid fa-circle-notch fa-spin me-1"></i> Đang nạp tem...
                        </span>
                    </button>
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

                                    {{-- CỘT 1: Ô CHECKBOX --}}
                                    <td class="text-center">
                                        {{-- Thêm id="check-{{ $item->id }}" vào đây để tạo ID độc nhất cho mỗi dòng --}}
                                        <input type="checkbox" id="check-{{ $item->id }}"
                                            wire:model="selectedHistoryIds" value="{{ $item->id }}"
                                            class="form-check-input" style="cursor: pointer;">
                                    </td>

                                    {{-- CỘT 2: MÃ BARCODE (Chữ có thể bấm được) --}}
                                    <td class="fw-bold text-primary">
                                        {{-- Bọc chữ bằng thẻ label và chỉ định for="" khớp với ID của checkbox ở trên --}}
                                        <label for="check-{{ $item->id }}"
                                            style="cursor: pointer; margin: 0; width: 100%;">
                                            {{ $item->code }}
                                        </label>
                                    </td>

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
                                <div class="d-flex flex-column align-items-start justify-content-center h-100 pt-1">
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

    {{-- CSS: ĐỊNH DẠNG TEM VÀ CHẾ ĐỘ IN MỚI (CHỐNG LỖI MENU) --}}
    <style>
        /* --- GIAO DIỆN TRÊN MÀN HÌNH MÁY TÍNH --- */
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

        .print-area {
            display: none;
            /* Mặc định ẩn trên màn hình */
        }


        /* 🌟 --- CẤU TRÚC KHI BẤM IN (CTRL + P) --- 🌟 */
        @media print {

            /* 1. TÀNG HÌNH MENU BẰNG BỘ QUÉT TỰ ĐỘNG (WILDCARD) */
            /* Quét sạch mọi thẻ div có chữ "sidebar", "menu", "nav" trong tên class */
            aside,
            nav,
            header,
            footer,
            [class*="sidebar"],
            [id*="sidebar"],
            [class*="menu"],
            [id*="menu"],
            [class*="nav"],
            [id*="nav"] {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }

            /* 2. Đảm bảo form cấu hình và lịch sử biến mất */
            .d-print-none {
                display: none !important;
            }

            /* 3. TẠO TỜ GIẤY TRẮNG ĐÈ LÊN MỌI THỨ CÒN SÓT LẠI */
            .print-area {
                display: block !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                min-height: 100vh !important;
                /* Ép chiều cao tối thiểu bằng 1 trang giấy */
                background-color: #ffffff !important;
                /* ĐỔ NỀN TRẮNG ĐỂ CHE MỌI THỨ BÊN DƯỚI */
                z-index: 999999 !important;
                /* Nổi lên trên cùng */
            }

            /* Lưới in tự động chia cột theo tùy chọn */
            .print-grid {
                display: grid;
                grid-template-columns: repeat(var(--print-cols), 1fr);
                gap: 2mm;
                width: 100%;
                background-color: #ffffff !important;
                /* Đảm bảo nền lưới cũng trắng */
            }

            .label-item {
                border: 1px solid #000 !important;
                /* Viền đen đậm khi in */
                border-radius: 0;
                page-break-inside: avoid;
                /* Không để tem bị đứt đôi giữa 2 trang giấy */
                padding: 2mm !important;
                margin-bottom: 0;
                background-color: #ffffff !important;
            }
        }
    </style>

    {{-- SCRIPT: TỰ ĐỘNG BẬT CỬA SỔ IN --}}
    <script>
        document.addEventListener('livewire:initialized', () => {

            Livewire.on('trigger-print', () => {
                // Tăng thời gian chờ lên 800ms để đảm bảo các mã QR/Barcode (SVG) đã được trình duyệt vẽ xong 100%
                setTimeout(() => {
                    window.print();
                }, 800);
            });

            // Script đóng Modal Tạo nhanh
            Livewire.on('close-quick-order-modal', () => {
                let modalEl = document.getElementById('quickOrderModal');
                let modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    let newModal = new bootstrap.Modal(modalEl);
                    newModal.hide();
                }
            });
        });
    </script>
</div>
