<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-info"><i class="fa-solid fa-tags me-2"></i>Quản Lý Thuộc Tính Động</h4>
        <button wire:click="resetInput" data-bs-toggle="modal" data-bs-target="#propertyModal"
            class="btn btn-info text-white">
            <i class="fa-solid fa-plus"></i> Thêm Thuộc Tính Mới
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
            <div class="alert alert-secondary small">
                <i class="fa-solid fa-circle-info me-1"></i> Đây là các thuộc tính (Màu, Size, Khổ, Số Mét...) sẽ tự
                động hiển thị trong form <strong>In Tem</strong> và xuất ra <strong>Excel</strong>.
            </div>

            <input type="text" wire:model.live="searchTerm" class="form-control mb-3"
                placeholder="Tìm kiếm mã hoặc tên thuộc tính...">

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Thứ tự</th>
                        <th>Mã (Code)</th>
                        <th>Tên Hiển Thị</th>
                        <th>Kiểu Dữ Liệu</th>
                        <th class="text-center">Bắt buộc</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($properties as $prop)
                        <tr>
                            <td class="text-center fw-bold">{{ $prop->sort_order }}</td>
                            <td class="fw-bold text-primary">{{ $prop->code }}</td>
                            <td>{{ $prop->name }}</td>
                            <td>
                                @if ($prop->type == 'text')
                                    <span class="badge bg-secondary">Văn bản</span>
                                @elseif($prop->type == 'number')
                                    <span class="badge bg-primary">Số</span>
                                @else
                                    <span class="badge bg-success">Dropdown (Chọn)</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {!! $prop->is_required ? '<i class="fa-solid fa-check text-danger"></i>' : '-' !!}
                            </td>
                            <td class="text-center">
                                {!! $prop->is_active
                                    ? '<span class="badge bg-success">Hoạt động</span>'
                                    : '<span class="badge bg-danger">Đang tắt</span>' !!}
                            </td>
                            <td class="text-end">
                                <button wire:click="edit({{ $prop->id }})"
                                    class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                    data-bs-target="#propertyModal">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:confirm="Xóa thuộc tính sẽ làm mất cấu hình hiển thị. Bạn chắc chứ?"
                                    wire:click="delete({{ $prop->id }})" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $properties->links() }}
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="propertyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Cập Nhật Thuộc Tính' : 'Tạo Thuộc Tính Mới' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mã Thuộc Tính (Code) <span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model="code" class="form-control text-uppercase"
                                    placeholder="VD: SO_MET, MAU_SAC" {{ $isEditMode ? 'readonly' : '' }}>
                                @error('code')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tên Hiển Thị <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control"
                                    placeholder="VD: Số Mét, Màu Sắc">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kiểu Dữ Liệu <span class="text-danger">*</span></label>
                                <select wire:model.live="type" class="form-select">
                                    <option value="text">Văn bản (Nhập chữ tự do)</option>
                                    <option value="number">Kiểu Số (Chỉ nhập số)</option>
                                    <option value="select">Dropdown (Chọn từ danh sách)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Thứ tự xuất hiện</label>
                                <input type="number" wire:model="sort_order" class="form-control">
                            </div>
                        </div>

                        {{-- Chỉ hiện ô nhập Tùy chọn nếu Type là Select --}}
                        @if ($type === 'select')
                            <div class="mb-3 p-3 bg-light border rounded">
                                <label class="form-label fw-bold text-success">Danh sách Tùy chọn (Ngăn cách bằng dấu
                                    phẩy)</label>
                                <textarea wire:model="options" class="form-control" rows="2" placeholder="VD: Đỏ, Xanh Lá, Vàng, Đen"></textarea>
                                <small class="text-muted">Nhân viên sẽ chọn 1 trong các giá trị này khi in tem.</small>
                            </div>
                        @endif

                        <div class="row mb-4 mt-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_required"
                                        id="is_required">
                                    <label class="form-check-label fw-bold text-danger" for="is_required">Bắt buộc
                                        phải nhập?</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active"
                                        id="is_active">
                                    <label class="form-check-label fw-bold text-success" for="is_active">Đang hoạt
                                        động?</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 border-top pt-3">
                            <div class="col-12 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model.live="is_global"
                                        id="is_global">
                                    <label class="form-check-label fw-bold text-primary" for="is_global">Áp dụng cho
                                        TẤT CẢ mã hàng?</label>
                                </div>
                                <small class="text-muted">Nếu tắt, bạn sẽ cần chọn những mã hàng (Model) cụ thể được
                                    phép sử dụng thuộc tính này.</small>
                            </div>

                            {{-- Chỉ hiện danh sách sản phẩm khi TẮT tính năng áp dụng tất cả --}}
                            @if (!$is_global)
                                <div class="col-12 mt-2">
                                    <label class="form-label fw-bold text-warning">Chọn các mã hàng (Model) áp
                                        dụng:</label>
                                    <div class="card p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                        <div class="row">
                                            @foreach ($allProducts as $product)
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            wire:model="selectedProducts" value="{{ $product->id }}"
                                                            id="prod_{{ $product->id }}">
                                                        <label class="form-check-label"
                                                            for="prod_{{ $product->id }}">
                                                            {{ $product->code }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn btn-info text-white">{{ $isEditMode ? 'Lưu Thay Đổi' : 'Tạo Mới' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', () => {
                var myModalEl = document.getElementById('propertyModal');
                var modal = bootstrap.Modal.getInstance(myModalEl);
                if (modal) modal.hide();
            });
        });
    </script>
</div>
