{{-- resources/views/livewire/user-list.blade.php --}}
<div class="position-relative">
    <!-- OVERLAY LOADING -->
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
        style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status">
        </div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h5 card-title">{{ __('Danh sách Người dùng') }}</h3>
                @can('users.create')
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        {{ __('Thêm Người dùng Mới') }}
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
                <input wire:model.live="search" type="text" placeholder="{{ __('Tìm kiếm người dùng...') }}"
                    class="form-control">
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Tên') }}</th>
                            <th scope="col">Email</th>
                            <th scope="col">{{ __('Vai trò') }}</th>
                            <th scope="col">{{ __('Bộ phận') }}</th>
                            <th scope="col">{{ __('Hành động') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        {{-- <-- Hiển thị vai trò từ Spatie --}}
                                        <span class="badge bg-secondary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->department->name ?? '-' }}</td>
                                <td>
                                    @can('users.edit')
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info me-2">
                                            {{ __('Sửa') }}
                                        </a>
                                    @endcan
                                    @can('users.delete')
                                        <button wire:confirm="{{ __('Bạn có chắc chắn muốn xóa người dùng này không?') }}"
                                            wire:click="deleteUser({{ $user->id }})" class="btn btn-sm btn-danger"
                                            @if (auth()->id() === $user->id) disabled @endif>
                                            {{ __('Xóa') }}
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    {{ __('Không tìm thấy người dùng nào.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
