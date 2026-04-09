<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING -->
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
        style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status">
        </div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary"><i class="fa-solid fa-weight-scale me-2"></i>Quản Lý Trạm Cân</h4>
        <button wire:click="openCreate" class="btn btn-primary">
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
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Tên Trạm Cân</th>
                            <th>Mã (Code)</th>
                            <th>Token Xác Thực</th>
                            <th>Ghi Chú</th>
                            <th>Trạng Thái</th>
                            <th width="130" class="text-center">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stations as $station)
                            <tr>
                                <td>{{ $station->id }}</td>
                                <td class="fw-semibold">{{ $station->name }}</td>
                                <td><code>{{ $station->code }}</code></td>
                                <td>
                                    <span class="badge bg-secondary font-monospace"
                                        title="{{ $station->station_token }}"
                                        style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle;">
                                        {{ Str::limit($station->station_token, 20) }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $station->notes ?? '—' }}</td>
                                <td>
                                    @if ($station->status)
                                        <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fa-solid fa-circle-pause me-1"></i>Ngừng</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button wire:click="edit({{ $station->id }})"
                                        class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button
                                        wire:confirm="Xóa trạm cân '{{ $station->name }}'? Thao tác không thể hoàn tác."
                                        wire:click="delete({{ $station->id }})" class="btn btn-sm btn-outline-danger"
                                        title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-weight-scale fa-2x mb-2 d-block opacity-25"></i>
                                    Chưa có trạm cân nào. Nhấn <strong>Thêm Mới</strong> để bắt đầu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $stations->links() }}</div>
        </div>

        <!-- Modal Form -->
        <div x-data="{ show: @entangle('showModal') }" x-show="show" style="display: none;">
            <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.45);">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="fa-solid fa-weight-scale me-2 text-primary"></i>
                                {{ $stationId ? 'Cập Nhật Trạm Cân' : 'Thêm Mới Trạm Cân' }}
                            </h5>
                            <button type="button" class="btn-close" @click="show = false"
                                wire:click="closeModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="scaleName">
                                        Tên Trạm Cân <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="scaleName" placeholder="VD: Trạm Cân Kho A" wire:model="name">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="scaleCode">
                                        Mã Trạm Cân <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                        id="scaleCode" placeholder="VD: SCALE_01" wire:model="code">
                                    <small class="text-muted">Mã dùng để định danh trạm trong hệ thống.</small>
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Token -->
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="scaleToken">
                                    Token Xác Thực <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control font-monospace @error('station_token') is-invalid @enderror"
                                        id="scaleToken" wire:model="station_token"
                                        placeholder="Token sẽ được tự động tạo">
                                    <button type="button" class="btn btn-outline-secondary"
                                        wire:click="regenerateToken" title="Tạo token mới">
                                        <i class="fa-solid fa-rotate me-1"></i> Tạo Mới
                                    </button>
                                </div>
                                <small class="text-muted">
                                    Token dùng cho C# client xác thực kết nối. Tự động sinh khi tạo mới, có thể chỉnh sửa hoặc tạo lại.
                                </small>
                                @error('station_token')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Ghi chú -->
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="scaleNotes">Ghi Chú</label>
                                <textarea class="form-control" id="scaleNotes" rows="2"
                                    placeholder="Mô tả vị trí, công dụng của trạm cân..."
                                    wire:model="notes"></textarea>
                            </div>

                            <!-- Trạng thái -->
                            <div class="mb-2 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="scaleStatus" wire:model="status">
                                <label class="form-check-label fw-bold" for="scaleStatus">Trạng Thái Hoạt Động</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="show = false"
                                wire:click="closeModal">Hủy</button>
                            <button type="button" class="btn btn-primary" wire:click="store"
                                wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>
                                    {{ $stationId ? 'Cập Nhật' : 'Lưu' }}
                                </span>
                                <span wire:loading wire:target="store">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
