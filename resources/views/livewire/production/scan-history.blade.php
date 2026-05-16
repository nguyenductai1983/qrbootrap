<div class="container-fluid py-3">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử Ca Làm Việc</h4>
        </div>
    </div>

    {{-- BỘ LỌC --}}
    <div class="card shadow-sm mb-3 border-primary">
        <div class="card-header bg-primary text-white py-2">
            <h6 class="mb-0 fw-bold"><i class="fa-solid fa-filter me-2"></i>Bộ lọc tìm kiếm</h6>
        </div>
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="small text-muted fw-bold">Từ ngày</label>
                    <input type="date" wire:model.live="startDate" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="small text-muted fw-bold">Đến ngày</label>
                    <input type="date" wire:model.live="endDate" class="form-control form-control-sm">
                </div>

                @if($this->canViewAllShift())
                    <div class="col-6 col-md-2">
                        <label class="small text-muted fw-bold">Lọc theo ca</label>
                        <select wire:model.live="filterShiftId" class="form-select form-select-sm">
                            <option value="">-- Tất cả các ca --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="small text-muted fw-bold">Nhân viên</label>
                        <select wire:model.live="filterUserId" class="form-select form-select-sm">
                            <option value="">-- Tất cả nhân viên --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-12 col-md-1 text-end">
                    <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100" title="Bỏ lọc">
                        <i class="fa-solid fa-rotate-right"></i> Mặc định
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- THỐNG KÊ --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-4">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body p-2 d-flex align-items-center">
                    <div class="display-6 me-3"><i class="fa-solid fa-barcode"></i></div>
                    <div>
                        <div class="fw-bold fs-5">{{ number_format($totalItems) }}</div>
                        <div class="small">Tổng số cuộn/tem</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body p-2 d-flex align-items-center">
                    <div class="display-6 me-3"><i class="fa-solid fa-ruler-horizontal"></i></div>
                    <div>
                        <div class="fw-bold fs-5">{{ number_format($totalLength, 1) }}</div>
                        <div class="small">Tổng số mét thực (m)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 text-end d-flex align-items-center justify-content-end">
            <a href="{{ route('production.scan') ?? '/production/scan' }}" class="btn btn-outline-primary shadow-sm fw-bold">
                <i class="fa-solid fa-qrcode me-2"></i>Quay lại màn hình Quét
            </a>
        </div>
    </div>

    {{-- BẢNG DỮ LIỆU --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped table-bordered align-middle mb-0 text-center" style="font-size: 0.9rem;">
                <thead class="table-primary text-nowrap">
                    <tr>
                        <th>#</th>
                        <th>Thời gian</th>
                        <th>Mã Tem</th>
                        <th>Người Quét</th>
                        <th>PO</th>
                        <th>Model</th>
                        <th>Màu</th>
                        <th>Số Mét</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $items->firstItem() + $index }}</td>
                            <td class="text-nowrap">{{ $item->verified_at ? $item->verified_at->format('d/m/Y H:i') : '' }}</td>
                            <td class="fw-bold text-primary">{{ $item->code }}</td>
                            <td>
                                {{ $item->verifier->name ?? '-' }}
                                @if($item->verifier && $item->verifier->shift)
                                    <br><span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $item->verifier->shift->name }}</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $item->order->code ?? '-' }}</td>
                            <td class="text-truncate" style="max-width: 150px;" title="{{ $item->product->name ?? '-' }}">
                                {{ $item->product->code ?? '-' }}
                            </td>
                            <td>{{ $item->color->code ?? '-' }}</td>
                            <td class="fw-bold text-success fs-6">{{ $item->length ?? 0 }} m</td>
                            <td>
                                <button wire:click="reprintItems([{{ $item->id }}])" class="btn btn-sm btn-outline-info" title="In lại tem">
                                    <i class="fa-solid fa-print"></i> In
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-folder-open fs-2 mb-2 d-block"></i>
                                Không có dữ liệu quét trong khoảng thời gian này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-2">
            {{ $items->links() }}
        </div>
    </div>
</div>
