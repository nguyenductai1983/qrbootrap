{{-- resources/views/livewire/user-list.blade.php --}}
<div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h5 card-title">{{ __('Danh sách Người dùng') }}</h3>
                @can('create users')
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
                            <th scope="col">ID</th>
                            <th scope="col">{{ __('Tên') }}</th>
                            <th scope="col">Email</th>
                            <th scope="col">{{ __('Vai trò') }}</th> {{-- <-- Cột Vai trò Spatie --}}
                            <th scope="col">{{ __('Phòng ban') }}</th>
                            <th scope="col">{{ __('Hành động') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role) {{-- <-- Hiển thị vai trò từ Spatie --}}
                                        <span class="badge bg-secondary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->department->name ?? '-' }}</td>
                                <td>
                                    @can('users edit')
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info me-2">
                                            {{ __('Sửa') }}
                                        </a>
                                    @endcan
                                    @can('users delete')
                                        <button wire:click="deleteUser({{ $user->id }})"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');"
                                                class="btn btn-sm btn-danger"
                                                @if(auth()->id() === $user->id) disabled @endif> {{-- Không cho tự xóa --}}
                                            {{ __('Xóa') }}
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted"> {{-- <-- Cập nhật colspan --}}
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
