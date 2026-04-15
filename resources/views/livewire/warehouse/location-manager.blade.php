<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
        style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status">
        </div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="row align-items-center mb-3 g-3">
        <div class="col-12 col-md-6">
            <h4 class="fw-bold text-primary mb-0"><i class="fa-solid fa-map-location-dot me-2"></i>Quản Lý Vị Trí Kho
            </h4>
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

    {{-- Cấu hình In ấn --}}
    <div class="accordion mb-3 shadow-sm border-info" id="printSettingsAccordion">
        <div class="accordion-item border-info">
            <h2 class="accordion-header">
                <button
                    class="accordion-button py-2 {{ count($selectedLocations) > 0 ? '' : 'collapsed' }} bg-info bg-opacity-10 text-info fw-bold"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseSettings">
                    <i class="fa-solid fa-print me-2"></i> Cấu hình In ấn (Áp dụng cho nút in tại từng dòng & in hàng
                    loạt)
                    @if (count($selectedLocations) > 0)
                        <span class="badge bg-danger ms-2">Đã chọn {{ count($selectedLocations) }} dòng</span>
                    @endif
                </button>
            </h2>
            <div id="collapseSettings"
                class="accordion-collapse collapse {{ count($selectedLocations) > 0 ? 'show' : '' }}">
                <div class="accordion-body p-3">
                    @if (count($selectedLocations) > 0)
                        <div class="mb-3 text-end">
                            <button wire:click="clearSelection" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-xmark me-1"></i>Bỏ chọn hàng loạt
                            </button>
                        </div>
                    @endif
                    <div class="row gy-3">
                        <!-- Cấu hình QR/Barcode -->
                        <div class="col-12 col-xl-12 pt-0 pb-3 pb-xl-0 d-flex flex-column">
                            <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-qrcode me-1"></i>Cấu hình
                                QR/Barcode</h6>
                            <div class="row g-2 align-items-end flex-grow-1">
                                <div class="col-12 col-md-5">
                                    <div class="d-flex gap-2">
                                        <div class="form-check border rounded px-2 py-1 bg-white mb-0 flex-grow-1">
                                            <input class="form-check-input mt-1 ms-0 me-1" type="radio"
                                                wire:model.live="printFormat" value="QR" id="locFmtQR">
                                            <label class="form-check-label fw-bold cursor-pointer small"
                                                for="locFmtQR">QR</label>
                                        </div>
                                        <div class="form-check border rounded px-2 py-1 bg-white mb-0 flex-grow-1">
                                            <input class="form-check-input mt-1 ms-0 me-1" type="radio"
                                                wire:model.live="printFormat" value="BARCODE" id="locFmtBar">
                                            <label class="form-check-label fw-bold cursor-pointer small"
                                                for="locFmtBar">Barcode</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-2">
                                    <label class="form-label fw-bold small text-muted mb-1" for="locPrintCols">Số
                                        cột</label>
                                    <input wire:model.live.blur="printColumns" type="number"
                                        class="form-control form-control-sm" min="1" max="8"
                                        id="locPrintCols">
                                </div>
                                <div class="col-4 col-md-2">
                                    <label class="form-label fw-bold small text-muted mb-1"
                                        for="locPrintRows">Hàng/trang</label>
                                    <input wire:model.live.blur="rowsPerPage" type="number"
                                        class="form-control form-control-sm" min="1" id="locPrintRows">
                                </div>
                                <div class="col-4 col-md-3">
                                    <label class="form-label fw-bold small text-muted mb-1" for="locFont">Cỡ
                                        chữ(px)</label>
                                    <input wire:model.live.blur="fontSize" type="number"
                                        class="form-control form-control-sm" min="3" id="locFont">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button wire:click="printQR" class="btn btn-sm btn-warning fw-bold w-100"
                                    @if (count($selectedLocations) == 0) disabled @endif>
                                    <i class="fa-solid fa-qrcode"></i> In QR/Bar Hàng Loạt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



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
                                    <button wire:click="printQRSingle({{ $location->id }})"
                                        class="btn btn-sm btn-outline-warning me-1 mb-1 mb-md-0"
                                        title="In QR/Barcode">
                                        <i class="fa-solid fa-qrcode"></i> <span class="d-inline d-md-none ms-1">In
                                            QR</span>
                                    </button>
                                    <button wire:click="printCodeSingle({{ $location->id }})"
                                        class="btn btn-sm btn-outline-success me-1 mb-1 mb-md-0" title="In Text Code">
                                        <i class="fa-solid fa-tag"></i> <span class="d-inline d-md-none ms-1">In
                                            Code</span>
                                    </button>
                                    <button wire:click="edit({{ $location->id }})"
                                        class="btn btn-sm btn-outline-primary me-1 mb-1 mb-md-0" title="Sửa">
                                        <i class="fa-solid fa-pen"></i> <span
                                            class="d-inline d-md-none ms-1">Sửa</span>
                                    </button>
                                    <button
                                        wire:confirm="Xóa vị trí '{{ $location->code }}'? Thao tác không thể hoàn tác."
                                        wire:click="delete({{ $location->id }})"
                                        class="btn btn-sm btn-outline-danger mb-1 mb-md-0" title="Xóa">
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
