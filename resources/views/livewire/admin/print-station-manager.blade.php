<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING -->
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
        style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status">
        </div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary"><i class="fa-solid fa-print me-2"></i>Quản Lý Trạm In</h4>
        <button wire:click="resetForm" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Thêm Mới
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
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Tên Trạm</th>
                            <th>Loại Trạm</th>
                            <th>Mã (Code)</th>
                            <th>Trạng Thái</th>
                            <th width="150" class="text-center">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stations as $station)
                            <tr>
                                <td>{{ $station->id }}</td>
                                <td>{{ $station->name }}</td>
                                <td>
                                    @if ($station->client_type === 'app')
                                        <span><i class="fa-solid fa-desktop me-1"></i> App
                                        </span>
                                    @else
                                        <span><i class="fa-brands fa-chrome me-1"></i> Web
                                            Kiosk</span>
                                    @endif
                                </td>
                                <td><span>{{ $station->code }}</span></td>
                                <td>
                                    @if ($station->status)
                                        <span>Đang hoạt động</span>
                                    @else
                                        <span>Ngừng hoạt động</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button wire:click="edit({{ $station->id }})"
                                        class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button
                                        wire:confirm="Xóa trạm in '{{ $station->name }}'? Thao tác không thể hoàn tác."
                                        wire:click="delete({{ $station->id }})" class="btn btn-sm btn-outline-danger"
                                        title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $stations->links() }}</div>
        </div>

        <!-- Modal Form -->
        <div x-data="{ show: @entangle('showModal') }" x-show="show" style="display: none;">
            <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.45);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $stationId ? 'Cập Nhật Trạm In' : 'Thêm Mới Trạm In' }}</h5>
                            <button type="button" class="btn-close" @click="show = false"
                                wire:click="closeModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="stationName">Tên Trạm In <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="stationName"
                                    placeholder="VD: Trạm In Máy Tráng 1" wire:model="name">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="stationCode">Mã Danh Tính (Code/MAC) <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="stationCode" placeholder="VD: station_01"
                                    wire:model="code">
                                <small class="text-muted">Mã này được hệ thống dùng để định danh trạm khi kết nối qua
                                    Web hoặc truy vấn CSDL.</small>
                                @error('code')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="clientType">Loại hình chạy trạm in <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="clientType" wire:model.live="client_type">
                                    <option value="browser">Trình duyệt Web Kiosk (Mặc định)</option>
                                    <option value="app">Phần mềm App (Windows Form, v.v...)</option>
                                </select>
                                @error('client_type')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($client_type === 'app')
                                <div class="p-3 mb-3 border rounded ">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-gears me-1"></i> Cấu
                                        hình riêng cho App</h6>
                                        
                                    <div class="alert alert-info py-2 mb-3">
                                        <strong>Cấu hình Server (Dành cho Dev nhập vào App C#):</strong>
                                        <ul class="mb-0 small ps-3">
                                            <li>App ID: <code>{{ config('broadcasting.connections.reverb.app_id') ?: env('REVERB_APP_ID') }}</code></li>
                                            <li>App Key: <code>{{ config('broadcasting.connections.reverb.key') ?: env('REVERB_APP_KEY') }}</code></li>
                                            <li>App Secret: <code>{{ config('broadcasting.connections.reverb.secret') ?: env('REVERB_APP_SECRET') }}</code></li>
                                            <li>Host: <code>{{ request()->getHost() }}</code> (Port: <code>{{ config('broadcasting.connections.reverb.options.port') ?: env('REVERB_PORT', 8080) }}</code>)</li>
                                        </ul>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold" for="appKey">App Key (Kênh bảo mật) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="stationToken"
                                            placeholder="VD: TRAM1_SECRET" wire:model="station_token">
                                        <small class="text-muted">App sẽ lắng nghe trên kênh:
                                            <code>printstationapp.{App Key}</code></small>
                                        <br>
                                        @error('station_token')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold" for="templateName">Tên Template (.btw)
                                            gốc</label>
                                        <input type="text" class="form-control" id="templateName"
                                            placeholder="VD: Barcode_Template" wire:model="template_name">
                                        <small class="text-muted">Gắn kèm theo payload gửi qua WebSockets, dùng để
                                            móc với đường dẫn thư mục lưu template trên máy đó.</small>
                                        <br>
                                        @error('template_name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="stationStatus"
                                    wire:model="status">
                                <label class="form-check-label fw-bold" for="stationStatus">Trạng Thái Hoạt
                                    Động</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="show = false"
                                wire:click="closeModal">Hủy</button>
                            <button type="button" class="btn btn-primary" wire:click="store"
                                wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store"><i
                                        class="fa-solid fa-floppy-disk me-1"></i>
                                    {{ $stationId ? 'Cập Nhật' : 'Lưu' }}</span>
                                <span wire:loading wire:target="store"><span
                                        class="spinner-border spinner-border-sm me-1"></span> Đang lưu...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
