<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
         style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary"><i class="fa-solid fa-print me-2"></i>Quản Lý Trạm In</h4>
        <button wire:click="resetForm" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Thêm Mới
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Tên Trạm</th>
                        <th>Mã (Code)</th>
                        <th>Trạng Thái</th>
                        <th width="150" class="text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stations as $station)
                        <tr>
                            <td>{{ $station->id }}</td>
                            <td>{{ $station->name }}</td>
                            <td><span class="badge badge-info">{{ $station->code }}</span></td>
                            <td>
                                @if($station->status)
                                    <span class="badge badge-success">Đang hoạt động</span>
                                @else
                                    <span class="badge badge-danger">Ngừng hoạt động</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button wire:click="edit({{ $station->id }})" class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button 
                                    wire:confirm="Xóa trạm in '{{ $station->name }}'? Thao tác không thể hoàn tác."
                                    wire:click="delete({{ $station->id }})" 
                                    class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $stations->links() }}</div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.45);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $stationId ? 'Cập Nhật Trạm In' : 'Thêm Mới Trạm In' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="stationName">Tên Trạm In <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="stationName" placeholder="VD: Trạm In Máy Tráng 1" wire:model="name">
                        @error('name') <span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="stationCode">Mã (Code) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="stationCode" placeholder="VD: station_01" wire:model="code">
                        <small class="text-muted">Mã này bắt buộc phải trùng khớp với mã cấu hình trên trình duyệt Kiosk.</small>
                        @error('code') <span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="stationStatus" wire:model="status">
                        <label class="form-check-label fw-bold" for="stationStatus">Trạng Thái Hoạt Động</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Hủy</button>
                    <button type="button" class="btn btn-primary" wire:click="store" wire:loading.attr="disabled" wire:target="store">
                        <span wire:loading.remove wire:target="store"><i class="fa-solid fa-floppy-disk me-1"></i> {{ $stationId ? 'Cập Nhật' : 'Lưu' }}</span>
                        <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
