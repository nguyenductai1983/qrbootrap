<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary"><i class="fa-solid fa-file-invoice me-2"></i>Quản Lý Đơn Hàng (PO)</h4>
        <button wire:click="resetInput" data-bs-toggle="modal" data-bs-target="#orderModal" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm Mới
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
                placeholder="Tìm kiếm mã PO hoặc khách hàng...">

            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Mã PO</th>
                        <th>Khách Hàng</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Tạo</th>
                        <th class="text-end">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="fw-bold">{{ $order->code }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>
                                {{-- Laravel tự động hiểu $order->status là 1 Enum object --}}
                                <span class="badge {{ $order->status->badge() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <button wire:click="edit({{ $order->id }})"
                                    class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                    data-bs-target="#orderModal">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:confirm="Bạn có chắc muốn xóa?" wire:click="delete({{ $order->id }})"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Cập Nhật Đơn Hàng' : 'Tạo Đơn Hàng Mới' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                        <div class="mb-3">
                            <label class="form-label">Mã Đơn Hàng (PO) <span class="text-danger">*</span></label>
                            <input type="text" wire:model="code" class="form-control text-uppercase"
                                placeholder="VD: PO-2023-001">
                            @error('code')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên Khách Hàng <span class="text-danger">*</span></label>
                            <input type="text" wire:model="customer_name" class="form-control"
                                placeholder="VD: Công ty ABC">
                            @error('customer_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng Thái</label>
                            <select wire:model="status" class="form-select">
                                @foreach (\App\Enums\OrderStatus::cases() as $case)
                                    <option value="{{ $case->value }}">{{ $case->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn btn-primary">{{ $isEditMode ? 'Lưu Thay Đổi' : 'Tạo Mới' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script để đóng modal tự động sau khi lưu
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', () => {
                var myModalEl = document.getElementById('orderModal');
                var modal = bootstrap.Modal.getInstance(myModalEl);
                if (modal) modal.hide();
            });
        });
    </script>
</div>
