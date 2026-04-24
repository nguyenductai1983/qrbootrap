<div class="container-fluid py-3">
    <!-- GIAO DIỆN ĐIỆN THOẠI (CHỈ HIỆN KHI MÀN HÌNH NHỎ) -->
    <div class="d-lg-none">
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="fa-solid fa-barcode me-2"></i> QUÉT MÃ CẬP NHẬT
                    </div>
                    <div class="card-body">
                        <x-scanner inputModel="codeInput" onEnter="searchByCode" onScan="searchByCode"
                            placeholder="Quét mã tem..." buttonText="Tìm" />
                    </div>
                </div>

                @if ($item)
                    <div class="card shadow-sm border-info border-start border-4 mb-3">
                        <div class="card-header bg-info text-white fw-bold d-flex justify-content-between">
                            <span>CHI TIẾT CẬP NHẬT</span>
                            <button wire:click="cancelEdit" class="btn-close btn-close-white"></button>
                        </div>
                        <div class="card-body">
                            <!-- Hiển thị tóm tắt thông tin tem -->
                            <div class="bg-light p-2 rounded mb-3 border">
                                <div class="fw-bold text-primary fs-5">{{ $item->code }}</div>
                                <div class="small text-muted">
                                    PO: {{ $item->order->code ?? 'N/A' }} |
                                    GSM: {{ $item->gsmlami ?? 0 }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">ĐƠN HÀNG MỚI</label>
                                <select wire:model="selectedOrderId" class="form-select form-select-lg">
                                    <option value="">-- Chọn Đơn hàng --</option>
                                    @foreach ($availableOrders as $order)
                                        <option value="{{ $order->id }}">
                                            {{ $order->code }} @if ($order->productionOrder)
                                                [{{ $order->productionOrder->code }}]
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">GSMLAMI MỚI</label>
                                <input type="number" step="0.1" wire:model="gsmlami"
                                    class="form-control form-control-lg text-success fw-bold">
                            </div>
                            <button wire:click="updateInfo" class="btn btn-success btn-lg w-100 fw-bold py-3 shadow-sm">
                                <i class="fa-solid fa-save me-2"></i> CẬP NHẬT
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Danh sách rút gọn cho Mobile -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-bold small py-2">
                        VỪA TRÁNG XONG (MOBILE)
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($recentItems->take(5) as $rItem)
                            <a href="javascript:void(0)" wire:click="selectItem({{ $rItem->id }})"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold {{ $item && $item->id == $rItem->id ? 'text-primary' : '' }}">
                                        {{ $rItem->code }}</div>
                                    <div class="x-small text-muted">{{ $rItem->order->code ?? 'N/A' }}</div>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $rItem->gsmlami }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GIAO DIỆN MÁY TÍNH (CHỈ HIỆN KHI MÀN HÌNH LỚN) -->
    <div class="d-none d-lg-block">
        <div class="row g-3">
            <!-- Cột trái: Form nhập -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-3 sticky-top" style="top: 1rem;">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="fa-solid fa-edit me-2"></i> CẬP NHẬT THÔNG TIN
                    </div>
                    <div class="card-body">
                        @if ($item)
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <h5 class="fw-bold mb-1">{{ $item->code }}</h5>
                                <div class="small">
                                    Đơn hàng hiện tại: <b>{{ $item->order->code ?? 'N/A' }}</b><br>
                                    GSMLAMI hiện tại: <b>{{ $item->gsmlami ?? 0 }} g/m²</b>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Chọn lại Đơn
                                    hàng</label>
                                <select wire:model="selectedOrderId" class="form-select border-primary fw-bold">
                                    <option value="">-- Chọn Đơn hàng --</option>
                                    @foreach ($availableOrders as $order)
                                        <option value="{{ $order->id }}">
                                            {{ $order->code }}
                                            @if ($order->productionOrder)
                                                [{{ $order->productionOrder->code }}]
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">GSMLAMI Mới</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" wire:model="gsmlami"
                                        class="form-control fw-bold text-success" placeholder="0.0">
                                    <span class="input-group-text bg-success text-white">g/m²</span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button wire:click="updateInfo" class="btn btn-success btn-lg fw-bold shadow-sm">
                                    <i class="fa-solid fa-save me-2"></i> LƯU THAY ĐỔI
                                </button>
                                <button wire:click="cancelEdit" class="btn btn-light text-muted">Hủy bỏ</button>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa-solid fa-qrcode fa-4x mb-3 text-light-subtle"></i>
                                <p class="text-muted small">Hãy quét mã hoặc chọn từ danh sách bên phải để sửa.</p>
                                <x-scanner inputModel="codeInput" onEnter="searchByCode" onScan="searchByCode"
                                    placeholder="Nhập mã tem..." />
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Cột phải: Danh sách & Bộ lọc -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-bold py-3">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <i class="fa-solid fa-search me-2"></i> TRA CỨU & LỌC DANH SÁCH ĐƠN HÀNG
                            </div>
                            <div class="col-6">
                                <input type="text" wire:model.live.debounce.300ms="filterOrderSearch"
                                    class="form-control form-control-sm border-secondary bg-dark text-white"
                                    placeholder="Lọc theo Đơn hàng (PO), LSX, hoặc Mã Tem...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Mã Tem</th>
                                        <th>Đơn hàng (PO)</th>
                                        <th>Lệnh Sản Xuất (LSX)</th>
                                        <th class="text-center">GSMLAMI</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentItems as $rItem)
                                        <tr
                                            class="{{ $item && $item->id == $rItem->id ? 'table-primary border-primary border-start border-4' : '' }}">
                                            <td class="ps-3">
                                                <div class="fw-bold">{{ $rItem->code }}</div>
                                                <div class="x-small text-muted">
                                                    {{ $rItem->updated_at->format('H:i d/m/Y') }}</div>
                                            </td>
                                            <td><span
                                                    class="badge bg-light text-dark border">{{ $rItem->order->code ?? 'N/A' }}</span>
                                            </td>
                                            <td><span
                                                    class="text-muted small">{{ $rItem->order->productionOrder->code ?? '---' }}</span>
                                            </td>
                                            <td class="text-center fw-bold text-success">{{ $rItem->gsmlami }}</td>
                                            <td class="text-center">
                                                <button wire:click="selectItem({{ $rItem->id }})"
                                                    class="btn btn-sm btn-outline-primary px-3 shadow-sm">
                                                    <i class="fa-solid fa-edit me-1"></i> Chọn sửa
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                Không tìm thấy dữ liệu phù hợp với từ khóa "{{ $filterOrderSearch }}".
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('alert', (event) => {
                Swal.fire({
                    icon: event[0].type ?? 'info',
                    title: 'Thông báo',
                    text: event[0].message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        });
    </script>
    <style>
        .x-small {
            font-size: 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            cursor: pointer;
        }

        .sticky-top {
            z-index: 1020;
        }
    </style>
</div>
