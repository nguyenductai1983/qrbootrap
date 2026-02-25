<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary"><i class="fa-solid fa-tags me-2"></i>Quản Lý Loại Tem</h4>
        <button wire:click="resetInput" data-bs-toggle="modal" data-bs-target="#typeModal" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm Loại Mới
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
            <input type="text" wire:model.live="searchTerm" class="form-control mb-3"
                placeholder="Tìm kiếm mã hoặc tên loại...">

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã Loại (Prefix)</th>
                        <th>Tên Loại</th>
                        <th>Ghi Chú</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($types as $type)
                        <tr>
                            <td class="fw-bold text-primary">{{ $type->code }}</td>
                            <td class="fw-bold">{{ $type->name }}</td>
                            <td>{{ $type->description }}</td>
                            <td>
                                {!! $type->is_active ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-danger">Đang tắt</span>' !!}
                            </td>
                            <td class="text-end">
                                <button wire:click="edit({{ $type->id }})" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#typeModal">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:confirm="Xóa loại tem này. Bạn chắc chứ?" wire:click="delete({{ $type->id }})" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $types->links() }}
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="typeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Cập Nhật Loại Tem' : 'Thêm Loại Tem Mới' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                        <div class="mb-3">
                            <label class="form-label">Mã Loại (Prefix) <span class="text-danger">*</span></label>
                            <input type="text" wire:model="code" class="form-control text-uppercase" placeholder="VD: RM, FG, BTP">
                            <small class="text-muted">Mã này sẽ đứng đầu số Tem (VD: <strong>RM</strong>-KHO-0001)</small>
                            @error('code') <span class="text-danger d-block small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên Loại <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control" placeholder="VD: Nguyên Liệu, Thành Phẩm">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea wire:model="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active">
                            <label class="form-check-label fw-bold text-success" for="is_active">Cho phép sử dụng?</label>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">{{ $isEditMode ? 'Lưu Thay Đổi' : 'Tạo Mới' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', () => {
                var myModalEl = document.getElementById('typeModal');
                var modal = bootstrap.Modal.getInstance(myModalEl);
                if (modal) modal.hide();
            });
        });
    </script>
</div>
