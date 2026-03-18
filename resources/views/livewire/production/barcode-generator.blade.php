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
                        <label class="form-label fw-bold" for="selectedDeptCode">Phân Xưởng</label>
                        <select wire:model.live="selectedDeptCode" class="form-select" id="selectedDeptCode">
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
                    {{-- Chọn Model --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="PRODUCT_ID">Chọn Sản phẩm <span
                                class="text-danger">*</span></label>
                        <select wire:model.live="itemData.PRODUCT_ID"
                            class="form-select @error('itemData.PRODUCT_ID') is-invalid @enderror" id="PRODUCT_ID">
                            <option value="">-- Chọn Sản phẩm ({{ count($availableProducts) }}) --
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
                    {{-- 2. Chọn Loại Tem --}}
                    <div class="mb-3">
                        <select wire:model="type" class="form-select text-primary fw-bold" id="type">
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
                    <div class="row g-1 mt-3 rounded border">
                        <div class="col-12">
                            {{-- 4. Tùy chọn Định dạng In (MỚI) --}}
                            <span class="form-label fw-bold small text-uppercase text-muted ms-1 mb-2">Định dạng mã
                                in</span>
                            <div class="d-flex gap-3 ms-1">
                                <div class="form-check">
                                    {{-- Đã xóa .live --}}
                                    <input class="form-check-input" type="radio" wire:model="printFormat"
                                        value="QR" id="fmtQR">
                                    <label class="form-check-label fw-bold cursor-pointer" for="fmtQR">
                                        <i class="fa-solid fa-qrcode text-primary me-1"></i> QR Code
                                    </label>
                                </div>
                                <div class="form-check">
                                    {{-- Đã xóa .live --}}
                                    <input class="form-check-input" type="radio" wire:model="printFormat"
                                        value="BARCODE" id="fmtBar">
                                    <label class="form-check-label fw-bold cursor-pointer" for="fmtBar">
                                        <i class="fa-solid fa-barcode text-primary me-1"></i> Barcode 1D
                                    </label>
                                </div>
                            </div>

                        </div>
                        {{-- 5. Số tem / Hàng (Khổ giấy in) --}}
                        <div class="col-4">
                            <label class="form-label fw-bold" for="printColumns">Tem 1 dòng</label>
                            <input wire:model="printColumns" type="number" class="form-control" min="1"
                                id="printColumns">

                        </div>
                        {{-- 6. Số hàng giấy in --}}
                        <div class="col-4">
                            <label class="form-label fw-bold" for="rowsPerPage">Số hàng</label>
                            <input wire:model="rowsPerPage" type="number" class="form-control" min="1"
                                id="rowsPerPage">

                        </div>
                        {{-- 7. Cỡ chữ --}}
                        <div class="col-4">
                            <label class="form-label fw-bold" for="fontSize">Cỡ chữ</label>
                            <div class="mb-3">
                                <input wire:model="fontSize" type="number" class="form-control" min="3"
                                    id="fontSize">

                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('production.barcode-generator-excel') }}" class="btn btn-primary"><i
                                class="fa-solid fa-file-excel me-1"></i> <i class="fa-solid fa-arrow-right me-1"></i>
                            <i class="fa-solid fa-print me-1"></i>
                            Tạo Tem
                            từ Excel</a>
                    </div>
                </div>
                {{-- CỘT PHẢI: NHẬP THÔNG TIN CHI TIẾT --}}
                <div class="col-md-8">
                    <div class="row g-1">
                        <div class="col-12">
                            <div class="d-flex gap-2 align-items-stretch">
                                {{-- Nút link sang trang quản lý (Chiếm 7 phần chiều ngang) --}}
                                <a href="{{ route('manager.categories') }}" target="_new" style="flex: 7;"
                                    class="list-group-item list-group-item-action text-primary border rounded px-3 py-2 d-flex justify-content-center align-items-center {{ request()->routeIs('manager.categories') ? 'active' : '' }}">
                                    <span><i class="fa-solid fa-list me-2"></i> Quản lý thuộc tính</span>
                                </a>
                                {{-- Nút bấm Làm mới danh sách (Chiếm 3 phần chiều ngang) --}}
                                <button type="button" wire:click="refreshMasterData" style="flex: 3;"
                                    class="btn btn-outline-success shadow-sm px-3 d-flex justify-content-center align-items-center"
                                    title="Tải lại dữ liệu ngay lập tức">
                                    <span>
                                        <i class="fa-solid fa-arrows-rotate me-1" wire:loading.class="fa-spin"
                                            wire:target="refreshMasterData"></i>
                                        Tải lại thuộc tính
                                    </span>
                                </button>

                            </div>
                        </div>
                    </div>
                    <div class="row g-1 mt-3">
                        {{-- 3. Số lượng --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="quantity"><i
                                    class="fa-solid fa-arrow-up-9-1"></i>Số
                                lượng tem</label>
                            <input wire:model="quantity" type="number" class="form-control" min="1"
                                id="quantity" max="100">
                        </div>
                        {{-- Chọn Đơn Hàng --}}
                        <div class="col-md-4">
                            <label class="form-label small fw-bold" for="ORDER_CODE"><i
                                    class="fa-solid fa-file-invoice me-1"></i>Mã Đơn Hàng</label>
                            <input type="text" class="form-control" placeholder="Nhập mã đơn hoặc ..."
                                wire:model="itemData.ORDER_CODE" list="orderList" autocomplete="off"
                                id="ORDER_CODE">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold" for="selectedColor"><i
                                    class="fa-solid fa-palette"></i>Màu</label>
                            <select wire:model="selectedColor" class="form-select" id="selectedColor">
                                <option value="">-- Chọn Màu --</option>
                                @foreach ($colors as $color)
                                    <option value="{{ $color->id }}">{{ $color->code }} -
                                        {{ $color->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-1">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" for="selectedSpec"><i
                                        class="fa-solid fa-section"></i>Quy Cách</label>
                                <select wire:model="selectedSpec" class="form-select" id="selectedSpec">
                                    <option value="">-- Chọn Quy Cách --</option>
                                    @foreach ($specifications as $specification)
                                        <option value="{{ $specification->id }}">{{ $specification->code }} -
                                            {{ $specification->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" for="selectedWidth"><i
                                        class="fa-solid fa-text-width"></i>Khổ</label>
                                <select wire:model="selectedWidth" class="form-select" id="selectedWidth">
                                    <option value="">-- Chọn Khổ --</option>
                                    @foreach ($widths as $width)
                                        <option value="{{ $width->id }}">{{ $width->code }} -
                                            {{ $width->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" for="selectedPlastic"><i
                                        class="fa-solid fa-sheet-plastic"></i>Loại
                                    Nhựa</label>
                                <select wire:model="selectedPlastic" class="form-select" id="selectedPlastic">
                                    <option value="">-- Chọn Loại Nhựa --</option>
                                    @foreach ($plasticTypes as $plastic)
                                        <option value="{{ $plastic->id }}">{{ $plastic->code }} -
                                            {{ $plastic->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold" for="length"><i
                                    class="fa-solid fa-ruler-combined"></i>Chiều dài</label>
                            <input wire:model="length" type="number" class="form-control" id="length">
                        </div>
                    </div>
                    {{-- Các trường nhập liệu chi tiết --}}
                    <div class="row g-1 mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2 align-items-stretch">
                                {{-- Nút link sang trang quản lý (Chiếm 7 phần chiều ngang) --}}
                                <a href="{{ route('manager.properties') }}" target="_new" style="flex: 7;"
                                    class="list-group-item list-group-item-action text-primary border rounded px-3 py-2 d-flex justify-content-center align-items-center {{ request()->routeIs('manager.properties') ? 'active' : '' }}">
                                    <span><i class="fa-solid fa-tags me-1"></i> Cài đặt thuộc tính động</span>
                                </a>
                                {{-- Nút bấm Làm mới danh sách (Chiếm 3 phần chiều ngang) --}}
                                <button type="button" wire:click="refreshDynamicProperties" style="flex: 3;"
                                    class="btn btn-outline-success shadow-sm px-3 d-flex justify-content-center align-items-center"
                                    title="Tải lại dữ liệu ngay lập tức">
                                    <span>
                                        <i class="fa-solid fa-arrows-rotate me-1" wire:loading.class="fa-spin"
                                            wire:target="refreshDynamicProperties"></i>
                                        Tải thuộc tính động
                                    </span>
                                </button>

                            </div>
                        </div>
                    </div>
                    <div class="row g-1">
                        {{-- Vòng lặp hiển thị các thuộc tính động --}}
                        @foreach ($dynamicProperties as $prop)
                            <div class="col-6">
                                <label class="form-label mb-1" style="font-size: 0.85rem;"
                                    for="{{ $prop->code }}">
                                    {{ $prop->name }}
                                    @if ($prop->is_code)
                                        <span class="text-primary fw-bold">
                                            @if ($prop->code_usage)
                                                {{ $prop->code }}
                                            @endif
                                            DATA {{ $prop->unit }}
                                            @if ($prop->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </span>
                                    @endif
                                </label>

                                @if ($prop->type === 'select' && is_array($prop->options))
                                    <select wire:model="itemData.{{ $prop->code }}"
                                        class="form-select form-select-sm" id="{{ $prop->code }}">
                                        <option value="">-- Chọn --</option>
                                        @foreach ($prop->options as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $prop->type === 'number' ? 'number' : 'text' }}"
                                        wire:model="itemData.{{ $prop->code }}" id="{{ $prop->code }}"
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
                    <div class="mt-4">
                        <div class="input-group mb-3">
                            <button wire:click="generate" class="form-control btn btn-success px-4 fw-bold">
                                <i class="fa-solid fa-plus me-1"></i> Tạo Mới & In Ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm d-print-none">
                {{-- Tách card-header và d-flex ra bằng 1 dấu khoảng trắng --}}
                {{-- <div class="card-header d-flex justify-content-between align-items-center"> --}}
                <div class="input-group">
                    <h6 class="mb-0 fw-bold form-control me-3">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử tạo tem
                    </h6>

                    {{-- Nút in lại chỉ hiện khi có item được chọn --}}
                    {{-- Dùng x-show của Alpine để tự động ẩn/hiện phía Client (Trình duyệt) mà không cần gọi Server --}}
                    <button x-show="$wire.selectedHistoryIds.length > 0" style="display: none; min-width: 160px;"
                        wire:click="reprintSelected" class="btn btn-sm btn-success shadow-sm form-control">

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
                {{-- </div> --}}

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
    {{-- kết thúc vùng không in  --}}
    {{-- KHU VỰC IN TEM (ẨN TRÊN MÀN HÌNH, CHỈ HIỆN KHI IN) --}}
    @if (count($generatedItems) > 0)
        <div class="print-area">
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                // 🌟 TÍNH TOÁN TỔNG SỐ TEM TRÊN 1 TỜ GIẤY
                $cols = $printColumns > 0 ? $printColumns : 2;
                $rows = $rowsPerPage > 0 ? $rowsPerPage : 2;
                $itemsPerPage = $cols * $rows;

                // Cắt nhỏ mảng ra thành nhiều trang
                $pages = array_chunk($generatedItems, $itemsPerPage);
            @endphp
            @foreach ($pages as $pageItems)
                {{-- 🌟 MỖI VÒNG LẶP LÀ 1 TỜ GIẤY ĐỘC LẬP --}}
                <div class="print-page" @style(['--print-cols: ' . $cols, '--print-rows: ' . $rows])>
                    <div class="print-grid">
                        @foreach ($pageItems as $item)
                            <div class="label-item">
                                <div class="barcode-wrapper">
                                    @if ($printFormat == 'QR')
                                        <div class="d-flex flex-column align-items-center justify-content-center"
                                            style="height: 100%; width: 100%;">

                                            {{-- 🌟 1. VÙNG CHỨA QR 🌟 --}}
                                            <div class="qr-container">
                                                {{-- Mẹo: Đổi size(200) thành size(80) để kích thước gốc nhỏ đi một chút, CSS sẽ ép nó dễ hơn --}}
                                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($item['code']) !!}
                                            </div>

                                            {{-- 2. DÒNG CHỮ --}}
                                            <div class="code-text fw-bold text-center w-100 mt-1" @style(['font-size: ' . ($fontSize ?? 10) . 'px', 'letter-spacing: 0.5px', 'word-wrap: break-word', 'line-height: 1.2'])>
                                                {{ $item['code'] }}
                                            </div>

                                        </div>
                                    @else
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-center h-100 pt-1">
                                            <div class="w-98 text-center">
                                                {!! $generator->getBarcode($item['code'], $generator::TYPE_CODE_128, 2, 45) !!}
                                            </div>
                                            <div class="code-text fw-bold mt-1 text-center w-100" @style(['font-size: ' . ($fontSize ?? 10) . 'px', 'letter-spacing: 1px', 'word-wrap: break-word'])>
                                                {{ $item['code'] }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/barcode.css') }}">
    @endpush
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
