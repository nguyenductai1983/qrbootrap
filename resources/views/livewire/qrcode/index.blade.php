<div wire:poll.30s="loadNewQrcodes"> {{-- Đặt wire:poll ở đây để chỉ kích hoạt phương thức loadNewQrcodes --}}

    <div class="mb-2">
        <h2 class="h3 mb-1">Danh sách các lượt quét QR</h2>
        <p class="text-muted mt-2">
            Cập nhật lần cuối:
            {{ $lastFetchedAt ? \Carbon\Carbon::parse($lastFetchedAt)->format('H:i:s d/m/Y') : 'Chưa có dữ liệu' }}
            Tổng số bản ghi trên trang này: {{ $qrcodes->count() }}
            (Tổng cộng: {{ $qrcodes->total() }} bản ghi)
        </p>
    </div>

    {{-- Form lọc và xuất Excel --}}
    <form wire:submit.prevent="exportDataToExcel">
        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-3 col-6">
                <label for="startdate" class="form-label">Ngày bắt đầu:</label>
                <input type="date" id="startdate" wire:model="startdate" name="startdate" class="form-control">
            </div>
            <div class="col-md-3 col-6">
                <label for="enddate" class="form-label">Ngày kết thúc:</label>
                <input type="date" id="enddate" wire:model="enddate" name="enddate" class="form-control">
            </div>

            <div class="col-md-2 col-4">
                <label for="sortBy" class="form-label">Sắp xếp theo:</label>
                <select id="sortBy" wire:model="sortBy" class="form-select">
                    <option value="created_at">Thời gian tạo</option>
                    <option value="qr_code_id">Mã QR</option>
                </select>
            </div>
            <div class="col-md-2 col-4">
                <label for="sortDirection" class="form-label">Kiểu:</label>
                <select id="sortDirection" wire:model="sortDirection" class="form-select">
                    <option value="asc">Tăng dần</option>
                    <option value="desc">Giảm dần</option>
                </select>
            </div>
            <div class="col-md-2 col-4">
                <label for="numpaginate" class="form-label">Số dòng:</label>
                <select id="numpaginate" wire:model.live="numpaginate" class="form-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
        <div class="input-group">
            <input type="text" id="search" wire:model.live="search" class="form-control"
                placeholder="Nhập mã QR để tìm...">
            {{-- Dùng wire:model.live để tìm kiếm tức thì khi gõ --}}

            <button type="button" wire:click="applyFilters" wire:loading.attr="disabled"
                class="btn btn-primary input-group-text me-2">
                <span wire:loading wire:target="applyFilters" class="spinner-border spinner-border-sm" role="status"
                    aria-hidden="true"></span>
                <i class="fa-solid fa-magnifying-glass"></i> Tìm
            </button>
            <button type="submit" class="btn btn-success input-group-text3">
                <i class="fas fa-file-excel me-2"></i>
                Xuất chi tiết Data ra Excel
            </button>
        </div>
    </form>

    {{-- Nút tải bản ghi mới (hiển thị khi có bản ghi mới) --}}
    @if ($showNewItemsButton)
        <div class="text-center mb-3">
            <button wire:click="refreshData" class="btn btn-info btn-lg">
                <i class="fas fa-sync-alt me-2"></i> Tải {{ $newItemsCount }} bản ghi mới
            </button>
        </div>
    @endif

    {{-- Bảng hiển thị QR codes --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Mã QR</th>
                    <th scope="col">Dữ liệu Chi tiết</th>
                    <th scope="col">Thời gian quét</th>
                     <th scope="col">Thời gian cập nhật</th>
                    <th scope="col">Người quyét</th>
                    {{-- Các cột khác nếu có --}}
                </tr>
            </thead>
            <tbody>
                @forelse($qrcodes as $qrcode)
                    <tr>
                        <td>{{ $qrcode->id }}</td>
                        <td>{{ $qrcode->qr_code }}</td> {{-- Đã đổi từ qr_code_id sang qr_code nếu đó là cột thực tế --}}
                        <td>
                            @if (is_array($qrcode->data) && !empty($qrcode->data))
                                @php $stt = 1; @endphp
                                @foreach ($qrcode->data as $key => $value)
                                    <div class="small">
                                        <strong>{{ $stt }} - {{  $key }}:</strong>
                                        <span class="text-muted">{{ $value ?? 'N/A' }}</span>
                                    </div>
                                    @php $stt ++; @endphp
                                @endforeach
                            @else
                                <div class="text-muted fst-italic">Không có dữ liệu chi tiết</div>
                            @endif
                        </td>
                        <td>{{ $qrcode->created_at->format('H:i:s d/m/Y') }}</td>
                        <td>{{ $qrcode->updated_at->format('H:i:s d/m/Y') }}</td>
                        <td>{{ $qrcode->username }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Không có dữ liệu nào phù hợp với bộ lọc.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Liên kết phân trang --}}
    {{-- <div class="d-flex justify-content-center mt-4"> --}}
    {{ $qrcodes->links() }}
    {{-- </div> --}}

</div>
