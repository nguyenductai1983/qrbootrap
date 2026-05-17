<div>
    <div class="card shadow-sm mb-4 d-print-none">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa-solid fa-barcode me-2"></i>Phát hành Tem & Barcode Cây Vải</h5>
        </div>
        <div class="card-body position-relative"> {{-- Thêm position-relative vào đây --}}
            {{-- 🌟 LỚP MÀN MỜ BẢO VỆ CHỐNG CLICK NHANH 🌟 --}}
            <div wire:loading class="position-absolute w-100 h-100 top-0 start-0"
                style="opacity: 0.6; z-index: 10; cursor: not-allowed;">
                {{-- Có thể thêm icon xoay xoay ở giữa cho sinh động (Tùy chọn) --}}
                <div class="position-absolute top-50 start-50 translate-middle">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="row g-4">
                {{-- CỘT TRÁI: CẤU HÌNH IN --}}
                <div class="col-md-4 border-end">

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
                        @if (empty($availableProducts))
                            <small class="text-warning">⚠️ Xưởng này chưa có Mã Hàng nào.</small>
                        @endif
                    </div>
                    {{-- 2. Chọn Loại Tem --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="type">Loại Tem <span
                                class="text-danger">*</span></label>
                        <select wire:model="type" class="form-select text-primary fw-bold" id="type">
                            @if (count($itemTypes) > 0)
                                @foreach ($itemTypes as $t)
                                    <option value={{ $t->id }}>{{ $t->code }} - {{ $t->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">-- Chưa có loại tem nào --</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('production.barcode-generator') }}" class="btn btn-primary">
                            <i class="fa-solid fa-hand me-1"></i> <i class="fa-solid fa-arrow-right me-1"></i>
                            <i class="fa-solid fa-print me-1"></i>
                            Tạo Tem Thủ
                            Công</a>
                    </div>
                </div>
                {{-- CỘT PHẢI: NHẬP THÔNG TIN CHI TIẾT --}}
                <div class="col-md-8">
                    <div class="row g-1">
                        <div class="col-12 mb-3 p-2 rounded border border-info">
                            <label class="form-label fw-bold text-primary mb-1"><i
                                    class="fa-solid fa-sliders me-1"></i>Ý nghĩa của Cột đầu tiên (Cột 1) trong Excel là
                                gì?</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="col0Mode"
                                        value="quantity" id="modeQty">
                                    <label class="form-check-label cursor-pointer" for="modeQty">
                                        <strong>Số lượng tem</strong> <small class="text-muted">(Sẽ tạo N tem liên
                                            tiếp)</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="col0Mode"
                                        value="sequence" id="modeSeq">
                                    <label class="form-check-label cursor-pointer" for="modeSeq">
                                        <strong>Số thứ tự tem</strong> <small class="text-muted">(Sẽ tạo chính xác tem
                                            có số thứ tự này)</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold" for="excelData">
                                <i class="fa-solid fa-file-excel text-success me-1"></i>Dán Dữ Liệu Từ Excel
                            </label>
                            <p class="small text-muted mb-2">
                                Copy từ các cột Excel theo thứ tự: <strong>Cột 1 (Số lượng hoặc Số TT), Mã Đơn Hàng,
                                    Màu, Loại,
                                    Khổ, Loại Nhựa, GSM, Chiều dài, Số máy</strong>
                            </p>
                            <textarea wire:model="excelData" id="excelData" class="form-control" rows="5"
                                placeholder="Ví dụ: 15&#9;H212NDS&#9;we&#9;D8&#9;1780&#9;PP&#9;150&#9;1000&#9;02-17"></textarea>
                            @error('excelData')
                                <span class="text-danger small fst-italic">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row g-1 mt-3 rounded border">

                        <div class="col-12 col-md-5 col-offset-1">
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
                                        <i class="fa-solid fa-barcode text-primary me-1"></i> Barcode
                                    </label>
                                </div>
                            </div>

                        </div>
                        {{-- 5. Số tem / Hàng (Khổ giấy in) --}}
                        <div class="col-4 col-md-2">
                            <label class="form-label fw-bold" for="printColumns">Số tem 1 dòng</label>
                            <input wire:model="printColumns" type="number" class="form-control" min="1"
                                id="printColumns">
                            <small class="text-muted">Số tem trên 1 hàng</small>
                        </div>
                        {{-- 6. Số hàng giấy in --}}
                        <div class="col-4 col-md-2">
                            <label class="form-label fw-bold" for="rowsPerPage">Số hàng giấy in</label>
                            <input wire:model="rowsPerPage" type="number" class="form-control" min="1"
                                id="rowsPerPage">
                            <small class="text-muted">Số hàng trên giấy</small>
                        </div>
                        {{-- 7. Cỡ chữ --}}
                        <div class="col-4 col-md-2">
                            <label class="form-label fw-bold" for="fontSize">Cỡ chữ Code</label>
                            <div class="mb-3">
                                <input wire:model="fontSize" type="number" class="form-control" min="3"
                                    id="fontSize">
                                <small class="text-muted">Cỡ chữ cho Code</small>
                            </div>
                        </div>
                    </div>
                </div>
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
    {{-- Hiển thị thông báo lỗi thuần Laravel/Livewire --}}
    @if (session()->has('error'))
        <div class="alert alert-danger mt-3 d-flex align-items-center shadow-sm">
            <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>
            <strong>{{ session('error') }}</strong>
        </div>
    @endif
    <div class="card shadow-sm d-print-none">
        <div class="card-header bg-light py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold">
                    <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Lịch sử tạo tem
                    @if(!empty($search) || !empty($searchDateFrom) || !empty($searchDateTo) || !empty($searchProduct))
                        <span class="badge bg-warning text-dark ms-1">
                            <i class="fa-solid fa-filter fa-xs me-1"></i> Đang lọc
                        </span>
                    @endif
                </h6>

                {{-- Nút in lại chỉ hiện khi có item được chọn --}}
                <button x-show="$wire.selectedHistoryIds.length > 0" style="display: none;"
                    wire:click="reprintSelected" class="btn btn-sm btn-success shadow-sm">
                    <span wire:loading.remove wire:target="reprintSelected">
                        <i class="fa-solid fa-print me-1"></i> In lại <span
                            x-text="$wire.selectedHistoryIds.length"></span> tem
                    </span>
                    <span wire:loading wire:target="reprintSelected">
                        <i class="fa-solid fa-circle-notch fa-spin me-1"></i> Đang nạp tem...
                    </span>
                </button>
            </div>

            {{-- 🔍 THANH TÌM KIẾM TEM CŨ --}}
            <div class="row g-2 mt-2">
                {{-- Tìm theo mã barcode / đơn hàng / màu --}}
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white">
                            <i class="fa-solid fa-magnifying-glass text-primary"></i>
                        </span>
                        <input type="text"
                            wire:model.live.debounce.400ms="search"
                            class="form-control"
                            placeholder="Tìm mã barcode, đơn hàng, màu..."
                            id="search-history">
                        @if(!empty($search))
                            <button wire:click="$set('search', '')" class="btn btn-outline-secondary" type="button" title="Xóa">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Lọc theo sản phẩm --}}
                <div class="col-12 col-md-3">
                    <select wire:model.live="searchProduct" class="form-select form-select-sm" id="search-product">
                        <option value="">-- Tất cả sản phẩm --</option>
                        @foreach ($availableProducts as $product)
                            <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Lọc từ ngày --}}
                <div class="col-6 col-md-2">
                    <input type="date"
                        wire:model.live="searchDateFrom"
                        class="form-control form-control-sm"
                        id="search-date-from"
                        title="Từ ngày">
                </div>

                {{-- Lọc đến ngày --}}
                <div class="col-6 col-md-2">
                    <input type="date"
                        wire:model.live="searchDateTo"
                        class="form-control form-control-sm"
                        id="search-date-to"
                        title="Đến ngày">
                </div>

                {{-- Nút xóa tất cả bộ lọc --}}
                @if(!empty($search) || !empty($searchDateFrom) || !empty($searchDateTo) || !empty($searchProduct))
                <div class="col-12 col-md-1">
                    <button wire:click="clearSearch" class="btn btn-sm btn-outline-danger w-100" title="Xóa tất cả bộ lọc">
                        <i class="fa-solid fa-filter-circle-xmark me-1"></i> Xóa lọc
                    </button>
                </div>
                @endif
            </div>
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
                            <td>{{ $item->color->code ?? '-' }}</td>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td class="small text-muted">{{ $item->created_at->format('d/m H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-top bg-light">
                <small class="text-muted">
                    Trang <strong>{{ $historyItems->currentPage() }}</strong>
                    &nbsp;·&nbsp; {{ $historyItems->count() }} tem
                </small>
                <div class="d-flex gap-2">
                    @if ($historyItems->onFirstPage())
                        <button class="btn btn-sm btn-outline-secondary" disabled>
                            <i class="fa-solid fa-arrow-left me-1"></i> Trang trước
                        </button>
                    @else
                        <button wire:click="previousPage" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Trang trước
                        </button>
                    @endif

                    @if ($historyItems->hasMorePages())
                        <button wire:click="nextPage" class="btn btn-sm btn-outline-primary">
                            Trang tiếp <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    @else
                        <button class="btn btn-sm btn-outline-secondary" disabled>
                            Trang tiếp <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Giao diện in đã được chuyển sang view tập trung (print.labels) --}}

    {{-- SCRIPT: TỰ ĐỘNG BẬT CỬA SỔ IN NẾU CẦN (Hiện tại đã bắt ở app.blade.php) --}}
    <script>
        document.addEventListener('livewire:initialized', () => {

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
