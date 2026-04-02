<div>
    <div class="container-fluid py-4 position-relative">
        <!-- OVERLAY LOADING -->
        <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
             style="background: transparent;">
            <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
            <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary"><i class="fa-solid fa-layer-group me-2"></i>Quản lý Danh mục
            </h4>
            <button wire:click="resetForm" class="btn btn-primary shadow-sm" data-bs-toggle="modal"
                data-bs-target="#categoryModal">
                <i class="fa-solid fa-plus me-1"></i> Thêm mới
            </button>
        </div>
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header border-bottom-0 pt-3 pb-0">
                {{-- DANH SÁCH TAB --}}
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button wire:click="switchTab('color')"
                            class="nav-link fw-semibold {{ $activeTab === 'color' ? 'active text-primary' : 'text-secondary' }}">
                            Màu sắc
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="switchTab('specification')"
                            class="nav-link fw-semibold {{ $activeTab === 'specification' ? 'active text-primary' : 'text-secondary' }}">
                            Quy cách
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="switchTab('width')"
                            class="nav-link fw-semibold {{ $activeTab === 'width' ? 'active text-primary' : 'text-secondary' }}">
                            Khổ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="switchTab('plastic_type')"
                            class="nav-link fw-semibold {{ $activeTab === 'plastic_type' ? 'active text-primary' : 'text-secondary' }}">
                            Loại nhựa
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                {{-- BẢNG DỮ LIỆU --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table">
                            <tr>
                                <th class="ps-4">Mã (Code)</th>
                                <th>Tên hiển thị</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataList as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="form-check form-switch cursor-pointer">
                                            <input wire:click="toggleActive({{ $item->id }})"
                                                class="form-check-input" type="checkbox" role="switch"
                                                {{ $item->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                            <label
                                                class="form-check-label {{ $item->is_active ? 'text-success' : 'text-muted' }} small">
                                                {{ $item->is_active ? 'Đang dùng' : 'Tạm ẩn' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button wire:click="edit({{ $item->id }})"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-pen-to-square"></i> Sửa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu trong danh
                                        mục này.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL THÊM/SỬA --}}
    <div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">{{ $itemId ? 'Cập nhật' : 'Thêm mới' }} dữ liệu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã (Code) <span class="text-danger">*</span></label>
                            <input wire:model="code" type="text" class="form-control text-uppercase"
                                placeholder="VD: RED, 5KG, ABS..." required>
                            @error('code')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên hiển thị <span
                                    class="text-danger">*</span></label>
                            <input wire:model="name" type="text" class="form-control"
                                placeholder="VD: Màu Đỏ, Cuộn 5 Kg..." required>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input wire:model="is_active" class="form-check-input" type="checkbox" role="switch"
                                id="activeSwitch">
                            <label class="form-check-label" for="activeSwitch">Cho phép sử dụng (Active)</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save"><i class="fa-solid fa-floppy-disk me-1"></i> Lưu lại</span>
                            <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script đóng/mở Modal --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            let myModal = new bootstrap.Modal(document.getElementById('categoryModal'));
            Livewire.on('show-modal', () => myModal.show());
            Livewire.on('hide-modal', () => myModal.hide());
        });
    </script>
</div>
