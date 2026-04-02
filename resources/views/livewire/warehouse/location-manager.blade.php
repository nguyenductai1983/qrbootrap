<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
         style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="row align-items-center mb-3 g-3">
        <div class="col-12 col-md-6">
            <h4 class="fw-bold text-primary mb-0"><i class="fa-solid fa-map-location-dot me-2"></i>Quản Lý Vị Trí Kho</h4>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex gap-2 justify-content-md-end">
                <button wire:click="exportInventory" class="btn btn-success flex-grow-1 flex-md-grow-0">
                    <i class="fa-solid fa-file-excel me-1"></i> Xuất Tồn Kho
                </button>
                <button wire:click="create" class="btn btn-primary flex-grow-1 flex-md-grow-0">
                    <i class="fa-solid fa-plus"></i> Thêm Mới
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Toolbar In QR + In Code --}}
    @if (count($selectedLocations) > 0)
        <div class="card border-info shadow-sm mb-3">
            <div class="card-header py-2 bg-info bg-opacity-10">
                <span class="fw-bold text-info">
                    <i class="fa-solid fa-print me-1"></i>Đã chọn {{ count($selectedLocations) }} vị trí — Cấu hình in:
                </span>
                <button wire:click="clearSelection" class="btn btn-sm btn-outline-secondary float-end">
                    <i class="fa-solid fa-xmark me-1"></i>Bỏ chọn
                </button>
            </div>
            <div class="card-body py-2">
                <div class="row g-2 align-items-end">
                    {{-- Định dạng QR / Barcode --}}
                    <div class="col-12 col-md-auto">
                        <span class="form-label fw-bold small text-uppercase text-muted mb-1 d-block">Định dạng</span>
                        <div class="d-flex gap-3">
                            <div class="form-check border rounded px-3 py-1 bg-white">
                                <input class="form-check-input mt-1" type="radio" wire:model="printFormat"
                                    value="QR" id="locFmtQR">
                                <label class="form-check-label fw-bold cursor-pointer w-100" for="locFmtQR">
                                    <i class="fa-solid fa-qrcode text-primary me-1"></i>QR Code
                                </label>
                            </div>
                            <div class="form-check border rounded px-3 py-1 bg-white">
                                <input class="form-check-input mt-1" type="radio" wire:model="printFormat"
                                    value="BARCODE" id="locFmtBar">
                                <label class="form-check-label fw-bold cursor-pointer w-100" for="locFmtBar">
                                    <i class="fa-solid fa-barcode text-primary me-1"></i>Barcode
                                </label>
                            </div>
                        </div>
                    </div>
                    {{-- Số cột --}}
                    <div class="col-4 col-md-1">
                        <label class="form-label fw-bold small" for="locPrintCols">Số cột</label>
                        <input wire:model="printColumns" type="number" class="form-control form-control-sm"
                            min="1" max="8" id="locPrintCols">
                        <small class="text-muted d-none d-md-inline">Cột/trang</small>
                    </div>
                    {{-- Số hàng --}}
                    <div class="col-4 col-md-1">
                        <label class="form-label fw-bold small" for="locPrintRows">Số hàng</label>
                        <input wire:model="rowsPerPage" type="number" class="form-control form-control-sm"
                            min="1" id="locPrintRows">
                        <small class="text-muted d-none d-md-inline">Hàng/trang</small>
                    </div>
                    {{-- Cỡ chữ --}}
                    <div class="col-4 col-md-1">
                        <label class="form-label fw-bold small" for="locFont">Cỡ chữ</label>
                        <input wire:model="fontSize" type="number" class="form-control form-control-sm" min="3"
                            id="locFont">
                        <small class="text-muted d-none d-md-inline">px</small>
                    </div>
                    {{-- Các nút in --}}
                    <div class="col-12 col-md-auto d-flex gap-2 align-items-center pb-0 pb-md-4 mt-3 mt-md-0">
                        <button wire:click="printQR" class="btn btn-sm btn-warning fw-bold flex-grow-1 flex-md-grow-0">
                            <i class="fa-solid fa-qrcode me-1"></i>In QR/Bar
                        </button>
                        <button wire:click="printCode"
                            class="btn btn-sm btn-success fw-bold flex-grow-1 flex-md-grow-0">
                            <i class="fa-solid fa-tag me-1"></i>In Code Text
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif



    <div class="card shadow-sm">
        <div class="card-body">
            <input type="text" wire:model.live="search" class="form-control mb-3"
                placeholder="Tìm theo mã hoặc tên vị trí...">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-card">
                    <thead>
                        <tr>
                            <th style="width:40px">
                                <input type="checkbox" class="form-check-input"
                                    wire:click="toggleSelectAll({{ $locations->pluck('id')->toJson() }})"
                                    @checked($locations->count() > 0 && empty(array_diff($locations->pluck('id')->toArray(), $selectedLocations)))>
                            </th>
                            <th>Mã Vị Trí</th>
                            <th>Tên Vị Trí</th>
                            <th>Loại</th>
                            <th class="text-center">Số Cây đang lưu</th>
                            <th class="text-end">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($locations as $location)
                            <tr wire:key="loc-{{ $location->id }}">
                                <td data-label="Bôi chọn in">
                                    <input type="checkbox" class="form-check-input cursor-pointer"
                                        style="transform: scale(1.2)" wire:model.live="selectedLocations"
                                        value="{{ $location->id }}">
                                </td>
                                <td data-label="Mã Vị Trí">
                                    <span class="badge bg-dark font-monospace fs-6">{{ $location->code }}</span>
                                </td>
                                <td data-label="Tên Vị Trí" class="fw-semibold">{{ $location->name }}</td>
                                <td data-label="Loại">
                                    @if ($location->type === 'warehouse')
                                        <span class="badge bg-primary">Kho Bán Thành Phẩm</span>
                                    @elseif ($location->type === 'production')
                                        <span class="badge bg-warning text-dark">Khu Sản Xuất</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $location->type }}</span>
                                    @endif
                                </td>
                                <td data-label="Số Cây đang lưu" class="text-md-center">
                                    <span
                                        class="badge {{ $location->items_count > 0 ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                        {{ $location->items_count ?? 0 }}
                                    </span>
                                </td>
                                <td data-label="Hành động" class="text-end border-0">
                                    <button wire:click="edit({{ $location->id }})"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fa-solid fa-pen"></i> <span
                                            class="d-inline d-md-none ms-1">Sửa</span>
                                    </button>
                                    <button
                                        wire:confirm="Xóa vị trí '{{ $location->code }}'? Thao tác không thể hoàn tác."
                                        wire:click="delete({{ $location->id }})"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i> <span
                                            class="d-inline d-md-none ms-1">Xóa</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>
                                    Chưa có vị trí nào trong kho.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $locations->links() }}</div>
        </div>
    </div>

    {{-- Modal Thêm / Sửa --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.45)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-map-pin me-2"></i>
                            {{ $isEditMode ? 'Cập Nhật Vị Trí' : 'Thêm Vị Trí Mới' }}
                        </h5>
                        <button wire:click="closeModal" type="button" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="store">
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="locationCode">
                                    Mã Vị Trí <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model="code" id="locationCode"
                                    class="form-control text-uppercase font-monospace" placeholder="VD: K1-A-01-01"
                                    @if ($isEditMode) readonly @endif>
                                <small class="text-muted">Mã này sẽ được in lên QR dán tại kệ hàng. Không thể đổi sau
                                    khi tạo.</small>
                                @error('code')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" for="locationName">
                                    Tên Vị Trí <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model="name" id="locationName" class="form-control"
                                    placeholder="VD: Kho 1 - Dãy A - Kệ 01 - Tầng 1">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" for="locationType">Loại Vị Trí</label>
                                <select wire:model="type" id="locationType" class="form-select">
                                    <option value="warehouse">Kho Bán Thành Phẩm</option>
                                    <option value="production">Khu Sản Xuất</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>

                            <div class="text-end mt-3 d-flex gap-2 justify-content-end">
                                <button wire:click="closeModal" type="button" class="btn btn-secondary">Hủy</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>
                                    {{ $isEditMode ? 'Lưu Thay Đổi' : 'Tạo Mới' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


</div>
