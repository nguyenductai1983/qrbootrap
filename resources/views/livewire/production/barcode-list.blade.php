<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary fw-bold"><i class="fa-solid fa-list-check me-2"></i>Quản Lý & Tra Cứu Tem</h4>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Từ khóa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" wire:model.live.debounce.500ms="search" class="form-control"
                            placeholder="Tìm mã barcode, ghi chú...">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Phân xưởng / Bộ phận</label>
                    <select wire:model.live="selectedDept" class="form-select">
                        <option value="">-- Tất cả bộ phận --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->code }}">{{ $dept->name }} ({{ $dept->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">Từ ngày</label>
                    <input type="date" wire:model.live="fromDate" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">Đến ngày</label>
                    <input type="date" wire:model.live="toDate" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead >
                        <tr>
                            <th class="ps-3">Mã Barcode</th>
                            <th>Thông Tin Chính</th>
                            <th>Thông Số (Properties)</th>
                            <th>Trạng Thái</th>
                            <th>Người Xác Nhận (Quét)</th>
                            <th>Ngày Tạo</th>
                            <th class="text-end pe-3">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-primary">{{ $item->code }}</div>
                                    <small class="text-muted text-uppercase">{{ $item->type }}</small>
                                </td>

                                {{-- Cột Thông tin chính (Order, Model) --}}
                                <td>
                                    @if (isset($item->properties['PO']))
                                        <div class="small">PO: <strong>{{ $item->properties['PO'] }}</strong></div>
                                    @endif
                                    @if (isset($item->properties['MA_VAI']))
                                        <div class="small text-success">{{ $item->properties['MA_VAI'] }}</div>
                                    @endif
                                </td>

                                {{-- Cột Properties (Hiển thị dạng Badge cho gọn) --}}
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        {{-- Chỉ hiện vài thông số quan trọng --}}
                                        @if (isset($item->properties['SO_MET']))
                                            <span class="badge bg-info text-dark border">
                                                {{ $item->properties['SO_MET'] }} m
                                            </span>
                                        @endif
                                        @if (isset($item->properties['MAU']))
                                            <span class="badge bg-light text-dark border">
                                                Màu: {{ $item->properties['MAU'] }}
                                            </span>
                                        @endif
                                        @if (isset($item->properties['GHI_CHU']))
                                            <span class="badge bg-warning text-dark"
                                                title="{{ $item->properties['GHI_CHU'] }}">
                                                <i class="fa-solid fa-note-sticky"></i> Note
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if ($item->status == 'NEW')
                                        <span class="badge bg-secondary">Mới tạo</span>
                                    @elseif($item->status == 'EXPORTED')
                                        <span class="badge bg-success">Đã xuất</span>
                                    @else
                                        <span class="badge bg-light text-dark border">{{ $item->status }}</span>
                                    @endif
                                </td>
                                {{-- CỘT 4 (MỚI): NGƯỜI QUÉT --}}
                                <td>
                                    @if ($item->verified_at)
                                        <div class="d-flex align-items-center">
                                            {{-- Avatar giả lập (chữ cái đầu) --}}
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ substr($item->verifier->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold small">
                                                    {{ $item->verifier->name ?? 'Unknown' }}
                                                </div>
                                                <div class="text-muted" style="font-size: 11px;">
                                                    <i class="fa-regular fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($item->verified_at)->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">-- Chưa quét --</span>
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>

                                <td class="text-end pe-3">
                                    <button wire:click="viewDetails({{ $item->id }})"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-eye"></i> Chi tiết
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fs-1 mb-3"></i>
                                    <p>Không tìm thấy dữ liệu phù hợp.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Chi tiết Tem: <span
                            class="text-primary">{{ $selectedItem->code ?? '' }}</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($selectedItem && $selectedItem->properties)
                        <table class="table table-bordered table-striped">
                            <tbody>
                                {{-- Duyệt qua mảng Properties JSON để hiển thị --}}
                                @foreach ($selectedItem->properties as $key => $value)
                                    <tr>
                                        {{-- Làm đẹp Key: SO_MET -> So Met --}}
                                        <th class="bg-light text-uppercase small" width="40%">
                                            {{ str_replace('_', ' ', $key) }}
                                        </th>
                                        <td>
                                            @if (is_array($value))
                                                {{ json_encode($value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3 text-end text-muted small">
                            Người tạo: User ID #{{ $selectedItem->created_by }} <br>
                            Ngày tạo: {{ $selectedItem->created_at }}
                        </div>
                    @else
                        <p class="text-center">Đang tải dữ liệu...</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Lắng nghe sự kiện từ PHP
            Livewire.on('open-detail-modal', () => {
                console.log(
                    'Đã nhận lệnh mở Modal!'); // Kiểm tra xem dòng này có hiện trong Console (F12) không

                const modalElement = document.getElementById('detailModal');

                if (modalElement) {
                    // Sử dụng getOrCreateInstance để đảm bảo tính ổn định
                    const myModal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
                    myModal.show();
                } else {
                    console.error('Không tìm thấy HTML của Modal có ID: detailModal');
                }
            });
        });
    </script>
</div>
