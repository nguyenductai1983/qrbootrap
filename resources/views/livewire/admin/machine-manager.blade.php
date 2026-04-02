<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
         style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary"><i class="fa-solid fa-gears me-2"></i>Quản Lý Máy Móc</h4>
        <button wire:click="resetInput" data-bs-toggle="modal" data-bs-target="#machineModal" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm Máy Mới
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
            <input type="text" wire:model.live="searchTerm" class="form-control mb-3" id="machineSearch"
                placeholder="Tìm kiếm theo mã hoặc tên máy...">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã Máy</th>
                            <th>Tên Máy</th>
                            <th>Phân Xưởng</th>
                            <th>Trạng Thái</th>
                            <th class="text-end">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($machines as $machine)
                            <tr>
                                <td class="fw-bold font-monospace">{{ $machine->code }}</td>
                                <td>{{ $machine->name }}</td>
                                <td>
                                    @if ($machine->department)
                                        <span class="badge">{{ $machine->department->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($machine->status)
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button wire:click="edit({{ $machine->id }})"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button wire:confirm="Xóa máy này? Thao tác không thể hoàn tác."
                                        wire:click="delete({{ $machine->id }})" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có máy nào được đăng ký.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $machines->links() }}
        </div>
    </div>

    {{-- Modal Thêm / Sửa --}}
    <div wire:ignore.self class="modal fade" id="machineModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEditMode ? 'Cập Nhật Máy' : 'Thêm Máy Mới' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                        <div class="mb-3">
                            <label class="form-label" for="machineCode">Mã Máy <span
                                    class="text-danger">*</span></label>
                            <input type="text" wire:model="code" id="machineCode" class="form-control text-uppercase"
                                placeholder="VD: MD-01">
                            @error('code')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="machineName">Tên Máy <span
                                    class="text-danger">*</span></label>
                            <input type="text" wire:model="name" id="machineName" class="form-control"
                                placeholder="VD: Máy Dệt 01">
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="machineDept">Phân Xưởng <span
                                    class="text-danger">*</span></label>
                            <select wire:model="department_id" id="machineDept" class="form-select">
                                <option value="">-- Chọn phân xưởng --</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model="status" id="machineStatus">
                                <label class="form-check-label" for="machineStatus">Đang hoạt động</label>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">
                                {{ $isEditMode ? 'Lưu Thay Đổi' : 'Tạo Mới' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', () => {
                var el = document.getElementById('machineModal');
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            });
            Livewire.on('open-modal', () => {
                var el = document.getElementById('machineModal');
                var modal = new bootstrap.Modal(el);
                modal.show();
            });
        });
    </script>
</div>
