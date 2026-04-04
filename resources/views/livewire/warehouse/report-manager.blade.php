<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
        style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status">
        </div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="row g-4 justify-content-center">

        <div class="col-md-8">
            <div class="card h-100 shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-file-excel me-2"></i>Xuất báo cáo kho Kho Bán Thành Phẩm
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Chọn các bộ lọc bên dưới để tải file số liệu báo cáo xuất Excel chính xác
                        nhất.</p>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn Dòng Sản Phẩm (Bỏ trống để xuất tất cả)</label>
                        <select wire:model="selectedModelId" class="form-select">
                            <option value="">-- Tất cả Dòng Sản Phẩm --</option>
                            @foreach ($models as $m)
                                <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Từ Ngày (Ngày nhập kho)</label>
                            <input type="date" wire:model="fromDate" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Đến Ngày (Ngày nhập kho)</label>
                            <input type="date" wire:model="toDate" class="form-control">
                        </div>
                    </div>

                    <hr class="my-4">

                    <button wire:click="export" class="btn btn-success w-100 py-2 fs-5 fw-bold">
                        <i class="fa-solid fa-download me-2"></i> Tải Ngay File Dữ Liệu Excel
                    </button>

                </div>
            </div>
        </div>

    </div>
</div>
