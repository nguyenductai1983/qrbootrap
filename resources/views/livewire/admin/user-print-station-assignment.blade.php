<div class="position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
         style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="row">
    <div class="col-md-4">
        <!-- Danh sách Nhân Viên -->
        <div class="card card-custom">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title font-weight-bolder">Nhân Viên Báo Cáo Sản Xuất</h3>
            </div>
            <div class="card-body p-0 pt-3">
                <div class="list-group list-group-flush mb-5">
                    @forelse($users as $user)
                        <a href="javascript:;" wire:click="selectUser({{ $user->id }})"
                            class="list-group-item list-group-item-action {{ $selectedUser?->id == $user->id ? 'active text-white bg-primary' : '' }}">
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-column">
                                    <span
                                        class="font-weight-bold text-dark-75 mb-1 {{ $selectedUser?->id == $user->id ? 'text-white' : '' }}">
                                        {{ $user->name }}
                                    </span>
                                    <span
                                        class="text-muted font-weight-bold {{ $selectedUser?->id == $user->id ? 'text-white-50' : '' }}">{{ $user->email }}</span>
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

    <div class="col-md-8">
        <!-- Khu vực phân quyền -->
        <div class="card card-custom">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title font-weight-bolder">Trạm In (Print Stations)</h3>
            </div>
            <div class="card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Thành công!</strong> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($selectedUser)
                    <h5 class="mb-4">
                        Phân quyền Trạm in cho:&nbsp;<span class="text-primary">{{ $selectedUser->name }}</span>
                    </h5>
                    <form wire:submit.prevent="assignStations">
                        <div class="row">
                            @forelse($printStations as $station)
                                <div class="col-sm-6 col-md-4 mb-4">
                                    <div class="card border border-light">
                                        <div class="card-body p-4 text-center">
                                            <i class="fas fa-print fa-3x text-info mb-3"></i>
                                            <h6 class="font-weight-bold">{{ $station->name }}</h6>
                                            <p class="text-muted small">{{ $station->code }}</p>

                                            <div class="custom-control custom-checkbox mt-2">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="station_{{ $station->id }}"
                                                    wire:model.defer="selectedStations" value="{{ $station->id }}">
                                                <label class="custom-control-label"
                                                    for="station_{{ $station->id }}">Cho phép sử dụng</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 py-4 text-center">
                                    <span class="text-muted">Chưa có Trạm In nào đang hoạt động trong hệ thống. Vui lòng
                                        quản lý Trạm in trước.</span>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-4 pt-4 border-top">
                            <button type="submit" class="btn btn-primary font-weight-bold px-6 py-3" wire:loading.attr="disabled" wire:target="assignStations">
                                <span wire:loading.remove wire:target="assignStations">
                                    <i class="fas fa-save mr-2"></i> Lưu Cấu Hình
                                </span>
                                <span wire:loading wire:target="assignStations">
                                    <span class="spinner-border spinner-border-sm pe-2" role="status" aria-hidden="true" style="margin-right: 5px;"></span>
                                    Đang xử lý...
                                </span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="py-10 text-center">
                        <i class="fas fa-user-circle fa-4x text-muted mb-4 opacity-50"></i>
                        <h4 class="text-muted">Vui lòng chọn nhân viên ở bên trái để phân quyền Trạm In</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
</div>
