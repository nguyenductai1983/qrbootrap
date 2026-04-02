<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
         style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="row g-4">

        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-file-export me-2"></i>1. Xuất danh sách nhập liệu</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn Đơn Hàng (PO)</label>
                        <select wire:model="selectedOrderId" class="form-select">
                            <option value="">-- Chọn PO --</option>
                            @foreach ($orders as $o)
                                <option value="{{ $o->id }}">{{ $o->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn Sản Phẩm <span class="text-danger">*</span></label>
                        <select wire:model="selectedProductId"
                            class="form-select @error('selectedProductId') is-invalid @enderror">
                            <option value="">-- Chọn Sản Phẩm --</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedProductId')
                            <span class="text-danger small"><i
                                    class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" wire:model="fromDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" wire:model="toDate" class="form-control">
                    </div>
                    <button wire:click="export" class="btn btn-outline-success w-100">
                        <i class="fa-solid fa-download me-2"></i> Tải file Excel mẫu
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-file-import me-2"></i>2. Import thông tin sản xuất</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn file Excel đã điền số liệu</label>
                        <input type="file" wire:model="fileUpload" class="form-control">
                        @error('fileUpload')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>



                    <button wire:click="import" class="btn btn-primary w-100" {{ !$fileUpload ? 'disabled' : '' }}>
                        <i class="fa-solid fa-upload me-2"></i> Cập nhật vào hệ thống
                    </button>

                    @if (session()->has('message'))
                        <div class="alert alert-success mt-3"><i class="fa-solid fa-check"></i> {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
