<div class="container-fluid py-4 position-relative">
    {{-- OVERLAY LOADING --}}
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="mt-3 fw-bold text-primary">Đang tải...</h5>
    </div>

    {{-- HEADER --}}
    <div class="row align-items-center mb-4 g-2">
        <div class="col">
            <h4 class="fw-bold text-primary mb-0">
                <i class="fa-solid fa-boxes-packing me-2"></i>Danh Sách Nhập Kho
            </h4>
            <small class="text-muted">Tất cả cây vải đã được nhập kho (có người nhập kho)</small>
        </div>
    </div>

    {{-- BỘ LỌC --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                {{-- Tìm mã --}}
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted mb-1">
                        <i class="fa-solid fa-magnifying-glass me-1"></i>Tìm Mã Barcode
                    </label>
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="Nhập mã barcode...">
                </div>

                {{-- Từ ngày --}}
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold small text-uppercase text-muted mb-1">
                        <i class="fa-regular fa-calendar me-1"></i>Từ Ngày
                    </label>
                    <input type="date" wire:model.live="fromDate" class="form-control">
                </div>

                {{-- Đến ngày --}}
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold small text-uppercase text-muted mb-1">
                        <i class="fa-regular fa-calendar-check me-1"></i>Đến Ngày
                    </label>
                    <input type="date" wire:model.live="toDate" class="form-control">
                </div>

                {{-- Dòng sản phẩm --}}
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold small text-uppercase text-muted mb-1">
                        <i class="fa-solid fa-layer-group me-1"></i>Sản Phẩm
                    </label>
                    <select wire:model.live="selectedProductId" class="form-select">
                        <option value="">-- Tất cả --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->code }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Người nhập kho --}}
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold small text-uppercase text-muted mb-1">
                        <i class="fa-solid fa-user me-1"></i>Người Nhập Kho
                    </label>
                    <select wire:model.live="selectedWarehouserId" class="form-select">
                        <option value="">-- Tất cả --</option>
                        @foreach ($warehousers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nút xóa lọc --}}
                <div class="col-12 col-md-1 d-flex align-items-end">
                    <button wire:click="resetFilters" class="btn btn-outline-secondary w-100" title="Xóa bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span class="d-none d-md-inline ms-1">Reset</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- BẢNG DỮ LIỆU --}}

    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold text-dark">
            <i class="fa-solid fa-table-list me-2 text-primary"></i>
            Kết quả: <span class="badge bg-primary rounded-pill">{{ $items->total() }}</span> bản ghi
        </span>
        <small class="text-muted">Trang {{ $items->currentPage() }} / {{ $items->lastPage() }}</small>
    </div>
    <div class="mt-3 table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0 table-card">
            <thead>
                <tr>
                    <th>Mã SP / Mã Kho</th>
                    <th>Màu / Loại / Khổ</th>
                    <th>Số Mét</th>
                    <th>TL (kg)</th>
                    <th>Đơn Hàng</th>
                    <th>Người Nhập Kho</th>
                    <th>Ngày Nhập Kho</th>
                    <th>Vị Trí</th>
                    <th>Bộ Phận</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr wire:key="inbound-{{ $item->id }}">
                        <td data-label="Mã SP / Mã Kho">
                            <div class="d-flex flex-column gap-1 align-items-start">
                                <span class="badge bg-dark font-monospace fs-6">{{ $item->code }}</span>
                                @if ($item->warehouse_code)
                                    <span class="badge bg-success font-monospace border"><i
                                            class="fa-solid fa-qrcode me-1"></i>{{ $item->warehouse_code }}</span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Màu / Loại / Khổ">
                            <div class="d-flex flex-column gap-1 align-items-end align-items-md-start">
                                @if ($item->color)
                                    <span class="badge bg-info text-dark">{{ $item->color->code }}</span>
                                @endif
                                @if ($item->specification)
                                    <span class="badge bg-secondary">{{ $item->specification->code }}</span>
                                @endif
                                @if ($item->width)
                                    <span class="badge bg-light text-dark border">{{ $item->width }} m</span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Số Mét" class="text-center fw-semibold">
                            {{ $item->original_length ?? '-' }}
                        </td>
                        <td data-label="TL (kg)" class="text-center">
                            {{ $item->weight ?? '-' }}
                        </td>
                        <td data-label="Đơn Hàng">
                            @if ($item->order)
                                <span class="text-muted small font-monospace">{{ $item->order->code }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td data-label="Người Nhập Kho">
                            @if ($item->warehouser)
                                <div
                                    class="d-flex align-items-center gap-2 justify-content-end justify-content-md-start">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:30px;height:30px;font-size:0.75rem;">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <span class="small fw-semibold">{{ $item->warehouser->name }}</span>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td data-label="Ngày Nhập Kho">
                            @if ($item->warehoused_at)
                                <div class="small text-end text-md-start">
                                    <div class="fw-semibold">{{ $item->warehoused_at->format('d/m/Y') }}</div>
                                    <div class="text-muted">{{ $item->warehoused_at->format('H:i') }}</div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td data-label="Vị Trí">
                            @if ($item->location)
                                <span class="badge bg-primary font-monospace">{{ $item->location->code }}</span>
                            @else
                                <span class="badge bg-secondary">Chưa có</span>
                            @endif
                        </td>
                        <td data-label="Bộ Phận">
                            @if ($item->department)
                                <span class="badge bg-warning text-dark">{{ $item->department->code }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-inbox fa-3x d-block mb-3 text-muted opacity-50"></i>
                            <p class="fw-semibold">Không tìm thấy dữ liệu nhập kho nào.</p>
                            <small>Thử thay đổi bộ lọc hoặc mở rộng khoảng thời gian.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($items->hasPages())
        <div class="card-footer border-top py-3">
            {{ $items->links() }}
        </div>
    @endif
</div>
