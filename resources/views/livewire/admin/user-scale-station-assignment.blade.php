<div class="position-relative">
    <!-- OVERLAY LOADING -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
         style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="row">
        <!-- Cột trái: Danh sách nhân viên -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-users text-primary me-2"></i>Nhân Viên</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($users as $user)
                            <a href="javascript:;" wire:click="selectUser({{ $user->id }})"
                                class="list-group-item list-group-item-action px-3 py-2 {{ $selectedUser?->id == $user->id ? 'active text-white bg-primary' : '' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-user-circle fa-lg {{ $selectedUser?->id == $user->id ? 'text-white-50' : 'text-muted' }}"></i>
                                    <div>
                                        <div class="fw-semibold small">{{ $user->name }}</div>
                                        <div class="small {{ $selectedUser?->id == $user->id ? 'text-white-50' : 'text-muted' }}">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">Không có nhân viên nào.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Phân công trạm cân -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-weight-scale text-warning me-2"></i>Trạm Cân (Scale Stations)</h5>
                </div>
                <div class="card-body">
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($selectedUser)
                        <h6 class="mb-4">
                            Phân quyền Trạm Cân cho:&nbsp;
                            <span class="badge bg-primary fs-6">{{ $selectedUser->name }}</span>
                        </h6>
                        <form wire:submit.prevent="assignStations">
                            <div class="row g-3">
                                @forelse($scaleStations as $station)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="card border h-100 {{ in_array($station->id, $selectedStations) ? 'border-primary' : 'border-light' }}">
                                            <div class="card-body p-3 text-center">
                                                <i class="fa-solid fa-weight-scale fa-2x mb-2 {{ in_array($station->id, $selectedStations) ? 'text-primary' : 'text-muted opacity-50' }}"></i>
                                                <h6 class="fw-bold small mb-1">{{ $station->name }}</h6>
                                                <p class="text-muted small mb-2"><code>{{ $station->code }}</code></p>
                                                <div class="form-check d-flex justify-content-center">
                                                    <input type="checkbox" class="form-check-input me-2"
                                                        id="scale_{{ $station->id }}"
                                                        wire:model="selectedStations"
                                                        value="{{ $station->id }}">
                                                    <label class="form-check-label small" for="scale_{{ $station->id }}">
                                                        Cho phép
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 py-4 text-center text-muted">
                                        <i class="fa-solid fa-weight-scale fa-3x mb-3 d-block opacity-25"></i>
                                        Chưa có trạm cân nào đang hoạt động.<br>
                                        Vui lòng <a href="{{ route('manager.scale-stations') }}">quản lý trạm cân</a> trước.
                                    </div>
                                @endforelse
                            </div>

                            @if($scaleStations->isNotEmpty())
                                <div class="mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary px-4"
                                        wire:loading.attr="disabled" wire:target="assignStations">
                                        <span wire:loading.remove wire:target="assignStations">
                                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Phân Công
                                        </span>
                                        <span wire:loading wire:target="assignStations">
                                            <span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </form>
                    @else
                        <div class="py-5 text-center text-muted">
                            <i class="fa-solid fa-user-circle fa-4x mb-3 d-block opacity-25"></i>
                            <h5>Vui lòng chọn nhân viên ở bên trái</h5>
                            <p class="small">để phân quyền sử dụng Trạm Cân</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
