<div>
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="fw-bold text-primary"><i class="fa-solid fa-boxes-packing me-2"></i>Quản lý Lệnh Sản Xuất</h4>
        </div>
        <div class="col-md-6 text-end">
            <div class="d-flex justify-content-end gap-2">
                <select wire:model.live="statusFilter" class="form-select w-auto">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model.live="search" class="form-control w-auto" placeholder="Tìm theo mã LSX...">
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã LSX</th>
                            <th>Trạng thái</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Số lượng Đơn hàng</th>
                            <th>Ghi chú</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productionOrders as $po)
                            <tr>
                                <td><span class="fw-bold">{{ $po->code }}</span></td>
                                <td>
                                    <span class="badge {{ $po->status->badge() }}">{{ $po->status->label() }}</span>
                                </td>
                                <td>{{ $po->start_date ? $po->start_date->format('d/m/Y') : '-' }}</td>
                                <td>{{ $po->end_date ? $po->end_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill">{{ $po->orders_count }}</span>
                                </td>
                                <td>{{ Str::limit($po->notes, 30) }}</td>
                                <td class="text-end">
                                    <button wire:click="viewOrders({{ $po->id }})" class="btn btn-sm btn-info text-white" title="Xem danh sách đơn hàng">
                                        <i class="fa-solid fa-list"></i>
                                    </button>
                                    <button wire:click="editMode({{ $po->id }})" class="btn btn-sm btn-warning" title="Sửa thông tin">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy Lệnh Sản Xuất nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($productionOrders->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $productionOrders->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Edit -->
    @if ($isEditModalOpen)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen me-2"></i>Cập nhật Lệnh Sản Xuất</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select wire:model="status" class="form-select">
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngày bắt đầu</label>
                            <input type="date" wire:model="start_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngày dự kiến kết thúc</label>
                            <input type="date" wire:model="end_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ghi chú</label>
                            <textarea wire:model="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">Hủy</button>
                        <button type="button" class="btn btn-primary" wire:click="save">Lưu thay đổi</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal View Orders -->
    @if ($isViewModalOpen)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fa-solid fa-list me-2"></i>Đơn hàng trong LSX: {{ $selectedOrder->code }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Trạng thái</th>
                                        <th>Tổng SL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($viewingOrders as $order)
                                        <tr>
                                            <td class="fw-bold">{{ $order->code }}</td>
                                            <td><span class="badge {{ $order->status->badge() }}">{{ $order->status->label() }}</span></td>
                                            <td>{{ $order->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
