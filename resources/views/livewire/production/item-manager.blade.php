<div wire:poll.10s>
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-auto me-auto text-center text-md-start">
                        <h5 class="mb-0"><i class="fa-solid fa-tags me-2"></i>Quản lý Kho Tem (Items)</h5>
                    </div>
                    <div class="col-6 col-md-auto">
                        {{-- <div class="d-flex flex-column flex-sm-row align-items-stretch gap-2"> --}}
                        <div class="input-group input-group-sm">
                            <span class="input-group-textfw-bold border-0">Từ ngày</span>
                            <input type="date" wire:model.live="fromDate" class="form-control border-0">
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="input-group input-group-sm">
                            <span class="input-group-textfw-bold border-0">Đến ngày</span>
                            <input type="date" wire:model.live="toDate" class="form-control border-0">
                        </div>

                        {{-- </div> --}}
                    </div>
                    <div class="col-12 col-md-auto">
                        <div class="d-flex flex-column flex-sm-row align-items-stretch gap-2">
                            <button wire:click="exportExcel"
                                class="btn btn-sm text-primary bg-warning fw-bold shadow-sm text-nowrap"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="exportExcel">
                                    <i class="fa-solid fa-file-excel me-1 text-success"></i> Xuất Excel (kèm nguồn gốc)
                                </span>
                                <span wire:loading wire:target="exportExcel">
                                    <i class="fa-solid fa-spinner fa-spin me-1"></i> Đang xuất...
                                </span>
                            </button>
                        </div>
                    </div>
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

                                {{-- Giao diện Loading Spinner --}}
                                <div wire:loading wire:target="searchCode"
                                    class="position-absolute top-50 translate-middle-y"
                                    style="right: 40px; z-index: 10;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>

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
                    <div class="col-md-3 col-12">
                        <label class="form-label small fw-bold" for="filterOrderId">Lọc theo Đơn hàng:</label>
                        <select wire:model.live="filterOrderId" class="form-select" id="filterOrderId">
                            <option value="">-- Tất cả đơn hàng --</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}">{{ $order->code }} - {{ $order->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-12">
                        <label class="form-label small fw-bold" for="filterDepartmentId">Lọc theo Xưởng:</label>
                        <select wire:model.live="filterDepartmentId" class="form-select" id="filterDepartmentId">
                            <option value="">-- Tất cả xưởng --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->code }} - {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-12">
                        <label class="form-label small fw-bold" for="filterProductId">Lọc theo Sản phẩm:</label>
                        <select wire:model.live="filterProductId" class="form-select" id="filterProductId">
                            <option value="">-- Tất cả sản phẩm --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-12">
                        <label class="form-label small fw-bold" for="filterColorId">Lọc theo Màu:</label>
                        <select wire:model.live="filterColorId" class="form-select" id="filterColorId">
                            <option value="">-- Tất cả màu --</option>
                            @foreach ($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->code }} - {{ $color->name }}
                                </option>
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
                <div class="mt-3 table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0 table-card">
                        <thead class="sticky-top shadow-sm text-center" style="z-index: 10;">
                            <tr>
                                <th>#</th>
                                <th>Mã Tem</th>
                                <th>Đơn hàng</th>
                                <th>Sản phẩm</th>
                                <th>Màu</th>
                                <th>Trạng thái</th>
                                <th>Chiều dài (Gốc/Còn)</th>
                                <th>Nguồn gốc (Cây Cha)</th>
                                <th>Chi tiết (Properties)</th>
                                <th>Vị trí hiện tại</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-primary text-break" data-label="Mã Tem">
                                        {{ $item->code }}</td>
                                    <td class="text-center" data-label="Đơn hàng">{{ $item->order->code ?? '-' }}
                                    </td>
                                    <td data-label="Sản phẩm">{{ $item->product->name ?? '-' }} -
                                        {{ $item->department->name ?? '-' }}
                                        <br>
                                        <span
                                            class="badge bg-secondary rounded-pill">{{ $item->creator->name ?? '-' }}</span>
                                    </td>
                                    <td data-label="Màu">{{ $item->color->name ?? '-' }}</td>
                                    <td class="text-center" data-label="Trạng thái">
                                        <span class="badge {{ $item->status?->badge() ?? '' }}">
                                            {{ $item->status?->label() ?? '' }}
                                        </span>
                                        <span class="badge bg-info">
                                            {{ $item->warehouse_code }}
                                        </span>
                                    </td>
                                    {{-- CỘT CHIỀU DÀI --}}
                                    <td class="text-center text-wrap" data-label="Chiều dài">
                                        @if ($item->original_length || $item->length)
                                            <div class="small">Gốc: <span
                                                    class="fw-bold">{{ (float) $item->original_length }}m</span></div>
                                            <div class="small">Còn: <span
                                                    class="fw-bold text-danger">{{ (float) $item->length }}m</span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- CỘT NGUỒN GỐC (PHẢ HỆ) --}}
                                    <td data-label="Nguồn gốc">
                                        @if ($item->parents->isNotEmpty())
                                            <ul class="mb-0 ps-3 text-wrap text-break" style="font-size: 0.85rem;">
                                                @foreach ($item->parents as $parent)
                                                    <li>
                                                        <span
                                                            class="fw-bold text-primary">{{ $parent->code }}</span><br>
                                                        <span class="text-muted fst-italic">(Lấy
                                                            {{ (float) $parent->pivot->used_length }}m)</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="badge text-secondary border">Nguyên bản</span>
                                        @endif
                                    </td>
                                    <td data-label="Chi tiết">
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
                                    <td class="text-center text-muted" data-label="Vị trí">
                                        {{-- Sắp tới: {{ $item->currentLocation->name ?? 'Chưa định vị' }} --}}
                                        <i class="fa-solid fa-location-dot me-1"></i> N/A
                                    </td>
                                    <td class="text-center" data-label="Hành động">
                                        <button wire:click="reprintItems([{{ $item->id }}])"
                                            class="btn btn-sm btn-outline-info me-1" title="In lại tem">
                                            <i class="fa-solid fa-print"></i>
                                        </button>
                                        <button wire:click="edit({{ $item->id }})"
                                            class="btn btn-sm btn-outline-primary me-1" title="Sửa chi tiết">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button wire:click="viewHistory({{ $item->id }})"
                                            class="btn btn-sm btn-outline-warning me-1" title="Xem lịch sử">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                        </button>
                                        <a href="{{ route('items.genealogy', $item->id) }}"
                                            class="btn btn-sm btn-outline-secondary me-1"
                                            title="Truy xuất nguồn gốc phả hệ">
                                            <i class="fa-solid fa-code-merge"></i>
                                        </a>
                                        <button wire:click="delete({{ $item->id }})"
                                            wire:confirm="⚠️ Bạn có chắc muốn xóa tem [{{ $item->code }}]? Hành động này không thể hoàn tác!"
                                            class="btn btn-sm btn-outline-danger" title="Xóa tem">
                                            <i class="fa-solid fa-trash"></i>
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
                    <h6 class="fw-bold mb-2">Thông số thực tế</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-3">
                            <label class="form-label small text-muted mb-1" for="editOriginalLength">Dài gốc (m)</label>
                            <input type="number" step="0.01" wire:model="editOriginalLength"
                                class="form-control form-control-sm" id="editOriginalLength">
                        </div>
                        <div class="col-3">
                            <label class="form-label small text-muted mb-1" for="editLength">Chiều dài (m)</label>
                            <input type="number" step="0.01" wire:model="editLength"
                                class="form-control form-control-sm" id="editLength">
                        </div>
                        <div class="col-3">
                            <label class="form-label small text-muted mb-1" for="editGsm">GSM</label>
                            <input type="number" step="0.01" wire:model="editGsm"
                                class="form-control form-control-sm" id="editGsm">
                        </div>
                        <div class="col-3">
                            <label class="form-label small text-muted mb-1" for="editWeight">Trọng lượng (kg)</label>
                            <input type="number" step="0.01" wire:model="editWeight"
                                class="form-control form-control-sm" id="editWeight">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label small text-muted mb-1" for="editShift">Ca sản xuất</label>
                            <input type="text" wire:model="editShift" class="form-control form-control-sm"
                                id="editShift" placeholder="VD: Ca 1, A...">
                        </div>
                        <div class="col-8">
                            <label class="form-label small text-muted mb-1" for="editNotes">Ghi chú</label>
                            <input type="text" wire:model="editNotes" class="form-control form-control-sm"
                                id="editNotes" placeholder="Ghi chú thêm...">
                        </div>
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

    {{-- MODAL LỊCH SỬ THAY ĐỔI --}}
    <div wire:ignore.self class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-warning"><i
                            class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử thay đổi:
                        {{ $historyItemCode }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($itemHistories && count($itemHistories) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Người sửa</th>
                                        <th>Trường</th>
                                        <th>Giá trị cũ</th>
                                        <th>Giá trị mới</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itemHistories as $history)
                                        <tr>
                                            <td>{{ $history->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td>{{ $history->user->name ?? 'Hệ thống' }}</td>
                                            <td>
                                                @if ($history->field_name === 'original_length')
                                                    Chiều dài gốc
                                                @elseif($history->field_name === 'length')
                                                    Chiều dài
                                                @elseif($history->field_name === 'gsm')
                                                    GSM
                                                @elseif($history->field_name === 'weight')
                                                    Trọng lượng
                                                @elseif($history->field_name === 'shift')
                                                    Ca sản xuất
                                                @elseif($history->field_name === 'notes')
                                                    Ghi chú
                                                @else
                                                    {{ $history->field_name }}
                                                @endif
                                            </td>
                                            <td class="text-danger fw-bold">{{ $history->old_value }}</td>
                                            <td class="text-success fw-bold">{{ $history->new_value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">Chưa có lịch sử thay đổi nào.</div>
                    @endif
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

            // Modal lịch sử
            let historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
            Livewire.on('open-history-modal', () => {
                historyModal.show();
            });
        });
    </script>
</div>
