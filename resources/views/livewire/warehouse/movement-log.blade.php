<div>
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Nhật Ký Luân Chuyển Kho
            </h1>
            <p class="text-muted mb-0">Theo dõi lịch sử nhập xuất, điều chuyển và cập nhật cập nhật trọng lượng.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ Lọc Tra Cứu</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Mã cây vải / Ghi chú</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control" wire:model.live.debounce.500ms="searchCode" placeholder="Nhập từ khóa...">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label text-muted small mb-1">Loại thao tác</label>
                    <select class="form-select" wire:model.live="actionType">
                        <option value="">-- Tất cả --</option>
                        @foreach($actionEnum as $action)
                            <option value="{{ $action->value }}">{{ $action->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted small mb-1">Người thực hiện</label>
                    <select class="form-select" wire:model.live="userId">
                        <option value="">-- Tất cả --</option>
                        <option value="system_null">Hệ thống tự động (Cân AI)</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted small mb-1">Từ ngày</label>
                    <input type="date" class="form-control" wire:model.live="dateFrom">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted small mb-1">Đến ngày</label>
                    <input type="date" class="form-control" wire:model.live="dateTo">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" wire:click="resetFilters" title="Xóa bộ lọc">
                        <i class="fa-solid fa-redo"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="15%" class="text-center">Thời gian</th>
                            <th width="15%" class="text-center">Thao tác</th>
                            <th width="15%">Mã cây vải</th>
                            <th width="15%">Vị trí</th>
                            <th width="15%">Người thực hiện</th>
                            <th width="25%">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-center">
                                    <span class="d-block fw-bold">{{ $log->created_at->format('H:i:s') }}</span>
                                    <span class="text-muted small">{{ $log->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $log->action_type->badge() }} px-2 py-1" style="font-size: 0.85rem">
                                        {{ $log->action_type->label() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="fw-bold text-primary text-decoration-none">
                                        {{ $log->item->code ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    @if($log->action_type->value === App\Enums\MovementAction::MOVE->value)
                                        <span class="badge bg-secondary">{{ $log->fromLocation->code ?? 'Kho' }}</span>
                                        <i class="fa-solid fa-arrow-right mx-1 text-muted text-xs"></i>
                                        <span class="badge bg-primary">{{ $log->toLocation->code ?? 'Kho' }}</span>
                                    @elseif($log->to_location_id)
                                        <span class="badge bg-primary">{{ $log->toLocation->code ?? '' }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px; font-size: 10px;">
                                                {{ substr($log->user->name, 0, 1) }}
                                            </div>
                                            <span>{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <i class="fa-solid fa-robot me-1"></i> Hệ thống tự động
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $log->note ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-box-open fa-3x text-gray-300"></i>
                                    </div>
                                    Không tìm thấy dữ liệu luân chuyển nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
