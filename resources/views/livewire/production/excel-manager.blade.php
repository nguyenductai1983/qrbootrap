<div class="container py-4">
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
                            @foreach($orders as $o) <option value="{{ $o->id }}">{{ $o->code }}</option> @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn Model Vải</label>
                        <select wire:model="selectedModelId" class="form-select">
                            <option value="">-- Chọn Model --</option>
                            @foreach($models as $m) <option value="{{ $m->id }}">{{ $m->code }} - {{ $m->name }}</option> @endforeach
                        </select>
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
                        @error('fileUpload') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div wire:loading wire:target="import" class="text-primary mb-2">
                        <div class="spinner-border spinner-border-sm"></div> Đang xử lý import...
                    </div>

                    <button wire:click="import" class="btn btn-primary w-100" {{ !$fileUpload ? 'disabled' : '' }}>
                        <i class="fa-solid fa-upload me-2"></i> Cập nhật vào hệ thống
                    </button>

                    @if (session()->has('message'))
                        <div class="alert alert-success mt-3"><i class="fa-solid fa-check"></i> {{ session('message') }}</div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
