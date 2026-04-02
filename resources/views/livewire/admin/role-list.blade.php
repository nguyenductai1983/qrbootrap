{{-- resources/views/livewire/role-list.blade.php --}}
<div class="position-relative">
    <!-- OVERLAY LOADING -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
         style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h5 card-title">{{ __('Quản lý Vai trò') }}</h3>
                @can('roles.create')
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        {{ __('Thêm Vai trò Mới') }}
                    </a>
                @endcan
            </div>

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-3">
                <input wire:model.live="search" type="text" placeholder="{{ __('Tìm kiếm vai trò...') }}"
                    class="form-control">
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Tên Vai trò') }}</th>
                            <th scope="col">{{ __('Số lượng Người dùng') }}</th>
                            <th scope="col">{{ __('Ngày tạo') }}</th>
                            <th scope="col">{{ __('Hành động') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->users_count }}</td>
                                <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @can('roles.edit')
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-info me-2">
                                            {{ __('Sửa') }}
                                        </a>
                                    @endcan
                                    @can('roles.delete')
                                        <button 
                                            wire:confirm="{{ __('Bạn có chắc chắn muốn xóa vai trò này không?') }}"
                                            wire:click="deleteRole({{ $role->id }})"
                                            class="btn btn-sm btn-danger" @if ($role->name === 'admin' || $role->users_count > 0) disabled @endif>
                                            {{ __('Xóa') }}
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    {{ __('Không tìm thấy vai trò nào.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $roles->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
