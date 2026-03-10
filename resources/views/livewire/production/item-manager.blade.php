<div>
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-tags me-2"></i>Quản lý Kho Tem (Items)</h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light text-dark fw-bold border-0">Từ ngày</span>
                        <input type="date" wire:model.live="fromDate" class="form-control border-0">
                    </div>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light text-dark fw-bold border-0">Đến ngày</span>
                        <input type="date" wire:model.live="toDate" class="form-control border-0">
                    </div>
                    <button wire:click="exportExcel" class="btn btn-sm btn-light text-primary fw-bold shadow-sm text-nowrap" style="min-width: 120px;" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="exportExcel">
                            <i class="fa-solid fa-file-excel me-1 text-success"></i> Xuất Excel
                        </span>
                        <span wire:loading wire:target="exportExcel">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Đang xuất...
                        </span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                {{-- KHU VỰC BỘ LỌC TÌM KIẾM --}}
                <div class="row g-3">
                    <div class="col-md-12">
                        {{-- Dùng d-flex để ép Label và Input nằm ngang --}}
                        <div class="d-flex align-items-center">

                            {{-- Thêm mb-0 (xóa lề dưới), me-2 (cách lề phải) và text-nowrap (chống rớt dòng) --}}
                            <label class="form-label small fw-bold mb-0 me-2 text-nowrap" for="searchCode">Tìm mã
                                tem:</label>

                            {{-- 🌟 THẺ BỌC POSITION-RELATIVE 🌟 (Bắt buộc phải có để chứa nút X và Gợi ý) --}}
                            <div class="position-relative flex-grow-1">

                                {{-- Thêm pe-4 (padding-right) để chữ gõ vào không bị đè lên nút X --}}
                                <input type="text" wire:model.live.debounce.500ms="searchCode"
                                    class="form-control pe-4" placeholder="Nhập mã tem hoặc quét barcode..."
                                    id="searchCode">

                                {{-- Nút Reset (X) --}}
                                @if (strlen($searchCode) > 0)
                                    <span wire:click="clearSearch"
                                        class="position-absolute top-50 translate-middle-y text-secondary"
                                        style="right: 12px; cursor: pointer; z-index: 10;" title="Xóa tìm kiếm">
                                        <i class="fas fa-times"></i>
                                    </span>
                                @endif

                                {{-- Khối hiển thị danh sách gợi ý --}}
                                @if ($showSuggestions && !empty($searchCode) && $suggestions->isNotEmpty())
                                    <ul class="list-group position-absolute w-100 shadow-sm"
                                        style="z-index: 1050; max-height: 250px; overflow-y: auto; margin-top: 2px;">
                                        @foreach ($suggestions as $suggestion)
                                            <li wire:click="selectSuggestion('{{ $suggestion->code }}')"
                                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                                style="cursor: pointer;">
                                                <span>{{ $suggestion->code }}</span>
                                                <span class="badge bg-secondary rounded-pill">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                            </div> {{-- Kết thúc thẻ bọc --}}

                        </div>
                    </div>
                </div>
                <div class="row g-3 ">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" for="filterOrderId">Lọc theo Đơn hàng:</label>
                        <select wire:model.live="filterOrderId" class="form-select" id="filterOrderId">
                            <option value="">-- Tất cả đơn hàng --</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}">{{ $order->code }} - {{ $order->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" for="filterProductId">Lọc theo Sản phẩm:</label>
                        <select wire:model.live="filterProductId" class="form-select" id="filterProductId">
                            <option value="">-- Tất cả sản phẩm --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" for="filterColorId">Lọc theo Màu:</label>
                        <select wire:model.live="filterColorId" class="form-select" id="filterColorId">
                            <option value="">-- Tất cả màu --</option>
                            @foreach ($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->code }} - {{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- THÔNG BÁO --}}
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- BẢNG DỮ LIỆU --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table text-center">
                            <tr>
                                <th>Mã Tem</th>
                                <th>Đơn hàng</th>
                                <th>Sản phẩm</th>
                                <th>Màu</th>
                                <th>Trạng thái</th>
                                <th>Chi tiết (Properties)</th>
                                <th>Vị trí hiện tại</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $item->code }}</td>
                                    <td class="text-center">{{ $item->order->code ?? '-' }}</td>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td>{{ $item->color->name ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->status->badge() }}">
                                            {{ $item->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        {{-- Hiển thị tóm tắt properties ra ngoài --}}
                                        @if (is_array($item->properties))
                                            <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
                                                @foreach ($item->properties as $key => $val)
                                                    @if (!empty($val) && !in_array($key, ['ORDER_ID', 'PRODUCT_ID', 'PRODUCT', 'PRODUCT_NAME']))
                                                        <li><strong>{{ $key }}:</strong> {{ $val }}
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted">
                                        {{-- Sắp tới: {{ $item->currentLocation->name ?? 'Chưa định vị' }} --}}
                                        <i class="fa-solid fa-location-dot me-1"></i> N/A
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="edit({{ $item->id }})"
                                            class="btn btn-sm btn-outline-primary" title="Sửa chi tiết">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Không tìm thấy tem nào phù
                                        hợp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CHỈNH SỬA PROPERTIES --}}
    <div wire:ignore.self class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header ">
                    <h5 class="modal-title fw-bold text-primary">Cập nhật Tem: {{ $editCode }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- Dọn sẵn khu vực Định Vị --}}
                    <div class="mb-4 border-bottom pb-3">
                        <label class="form-label fw-bold text-success" for="posvt"><i
                                class="fa-solid fa-map-location-dot me-1"></i>Vị trí hiện tại (Sắp ra mắt)</label>
                        <select class="form-select " disabled id="posvt">
                            <option>-- Đang phát triển tính năng --</option>
                            {{-- Chỗ này sau này bạn sẽ dùng: wire:model="current_location_id" --}}
                        </select>
                    </div>

                    <h6 class="fw-bold mb-3">Thông tin chi tiết (Properties)</h6>
                    <div class="row g-2">
                        {{-- Duyệt mảng properties tự động để sinh ra form chỉnh sửa --}}
                        @if (!empty($editProperties))
                            @foreach ($editProperties as $key => $value)
                                {{-- Ẩn các trường kỹ thuật không cho sửa trực tiếp --}}
                                @if (!in_array($key, ['ORDER_ID', 'PRODUCT_ID', 'PRODUCT', 'PRODUCT_NAME']))
                                    {{-- 🌟 THÊM wire:key VÀO ĐÂY ĐỂ ĐỊNH DANH ĐỘC LẬP TỪNG Ô 🌟 --}}
                                    <div class="col-6" wire:key="prop-{{ $key }}">

                                        <label class="form-label small text-muted mb-1"
                                            for="editProperties">{{ $key }}</label>
                                        <input type="text" wire:model="editProperties.{{ $key }}"
                                            class="form-control form-control-sm" id="editProperties">
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="col-12 text-muted fst-italic">Tem này không có thuộc tính phụ.</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button wire:click="update" type="button" class="btn btn-primary px-4"><i
                            class="fa-solid fa-save me-1"></i> Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Script bật/tắt Modal --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            let myModal = new bootstrap.Modal(document.getElementById('itemModal'));

            Livewire.on('open-modal', () => {
                myModal.show();
            });

            Livewire.on('close-modal', () => {
                myModal.hide();
            });
        });
    </script>
</div>
