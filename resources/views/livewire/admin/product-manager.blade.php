<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
         style="background: transparent;">
        <div class="spinner-border text-success" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-success">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-success"><i class="fa-solid fa-layer-group me-2"></i>Quản Lý Sản Phẩm</h4>
        <button wire:click="resetInput" data-bs-toggle="modal" data-bs-target="#productModal" class="btn btn-success">
            <i class="fa-solid fa-plus"></i> Thêm Sản Phẩm Mới
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
            {{-- Đã sửa wire:product.live thành wire:model.live --}}
            <input type="text" wire:model.live="searchTerm" class="form-control mb-3" id="searchTerm"
                placeholder="Tìm kiếm mã sản phẩm hoặc tên vải...">

            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Mã Sản Phẩm</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Phân Xưởng Áp Dụng</th>
                        <th class="text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td class="fw-bold">{{ $product->code }}</td>
                            <td>
                                <div>{{ $product->name }}</div>
                                <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                            </td>
                            <td>
                                @foreach ($product->departments as $dept)
                                    <span class="badge bg-info text-dark mb-1">{{ $dept->code }}</span>
                                @endforeach
                            </td>
                            <td class="text-end">
                                <button wire:click="edit({{ $product->id }})"
                                    class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                    data-bs-target="#productModal">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:confirm="Xóa sản phẩm này sẽ ảnh hưởng đến các tem đã in. Bạn chắc chứ?"
                                    wire:click="delete({{ $product->id }})" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>

    {{-- Đã sửa id="product" thành id="productModal" --}}
    <div wire:ignore.self class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Cập Nhật Sản Phẩm' : 'Thêm Sản Phẩm Mới' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="code">Mã Sản Phẩm (Code) <span
                                        class="text-danger">*</span></label>
                                {{-- Đã sửa wire:product thành wire:model --}}
                                <input type="text" wire:model="code" id="code"
                                    class="form-control text-uppercase" placeholder="VD: VAI-CVC-40S">
                                @error('code')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="namesp">Tên Sản Phẩm <span
                                        class="text-danger">*</span></label>
                                {{-- Đã sửa wire:product thành wire:model --}}
                                <input type="text" wire:model="name" class="form-control" id="namesp"
                                    placeholder="VD: Vải Thun CVC 40s">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="description">Quy cách / Mô tả</label>
                                {{-- Đã sửa wire:product thành wire:model --}}
                                <textarea wire:model="description" id="description" class="form-control" rows="2"
                                    placeholder="Ghi chú về thành phần, định lượng..."></textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <span class="form-label fw-bold text-primary">
                                    Áp dụng cho Phân Xưởng nào?
                                    <span class="text-danger">*</span></span>
                                <div class="card p-3">
                                    <div class="row">
                                        @foreach ($departments as $dept)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    {{-- Đã sửa wire:product thành wire:model --}}
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model="selectedDepartments" value="{{ $dept->id }}"
                                                        id="dept_{{ $dept->id }}">
                                                    <label class="form-check-label" for="dept_{{ $dept->id }}">
                                                        {{ $dept->name }} ({{ $dept->code }})
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('selectedDepartments')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="store update">
                                <span wire:loading.remove wire:target="store update">{{ $isEditMode ? 'Lưu Thay Đổi' : 'Tạo Mới' }}</span>
                                <span wire:loading wire:target="store update"><span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...</span>
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
                var myModalEl = document.getElementById('productModal');
                var modal = bootstrap.Modal.getInstance(myModalEl);
                if (modal) modal.hide();
            });
        });
    </script>
</div>
