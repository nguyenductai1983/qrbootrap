<div class="container-fluid py-4 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 flex-column justify-content-center align-items-center"
        style="background: transparent; z-index: 1050;">
        <div class="spinner-border text-warning" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status">
        </div>
        <h4 class="mt-3 fw-bold text-warning">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <h4 class="fw-bold text-warning mb-4"><i class="fa-solid fa-user-gear me-2"></i>Phân Công Máy Cho Nhân Viên</h4>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Panel trái: Chọn user và chọn máy --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning bg-opacity-25 fw-bold">
                    <i class="fa-solid fa-sliders me-1"></i> Phân Công
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold d-flex align-items-center" for="selectUser">
                            Chọn Nhân Viên <span class="text-danger ms-1">*</span>
                        </label>
                        <select wire:model.live="selectedUserId" id="selectUser" class="form-select">
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                    @if ($user->department)
                                        ({{ $user->department->name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('selectedUserId')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($selectedUserId)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn Máy Phân Công</label>
                            <div class="card p-3" style="max-height:350px; overflow-y:auto;">
                                @php
                                    $machinesByDept = $allMachines->groupBy(fn($m) => $m->department->name ?? 'Khác');
                                @endphp
                                @foreach ($machinesByDept as $deptName => $machinesInDept)
                                    <p class="fw-bold text-primary mb-1 mt-2">{{ $deptName }}</p>
                                    @foreach ($machinesInDept as $machine)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="selectedMachineIds" value="{{ $machine->id }}"
                                                id="machine_{{ $machine->id }}">
                                            <label class="form-check-label" for="machine_{{ $machine->id }}">
                                                <span
                                                    class="badge bg-secondary font-monospace me-1">{{ $machine->code }}</span>
                                                {{ $machine->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <button wire:click="saveAssignment" class="btn btn-warning w-100" wire:loading.attr="disabled"
                            wire:target="saveAssignment">
                            <span wire:loading.remove wire:target="saveAssignment">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Phân Công
                            </span>
                            <span wire:loading wire:target="saveAssignment">
                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true"></span>
                                Đang lưu...
                            </span>
                        </button>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fa-solid fa-arrow-up fa-lg mb-2 d-block"></i>
                            Chọn nhân viên để xem và chỉnh sửa phân công máy.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Panel phải: Danh sách tổng quan --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    <i class="fa-solid fa-list me-1"></i> Danh Sách Phân Công Hiện Tại
                </div>
                <div class="card-body">
                    <input type="text" wire:model.live="searchTerm" class="form-control mb-3" id="assignSearch"
                        placeholder="Tìm kiếm nhân viên...">

                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Nhân Viên</th>
                                    <th>Bộ phận</th>
                                    <th>Các Máy Được Giao</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($userList as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            <span class="text-muted small">{{ $user->department->name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @forelse ($user->machines as $machine)
                                                <span
                                                    class="badge bg-primary font-monospace me-1 mb-1">{{ $machine->code }}</span>
                                            @empty
                                                <span class="text-muted small">Chưa phân công</span>
                                            @endforelse
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Không tìm thấy nhân viên.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $userList->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
