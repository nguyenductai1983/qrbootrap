{{-- resources/views/livewire/permission-list.blade.php --}}
<div>
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h5 card-title">{{ __('Quản lý Quyền hạn') }}</h3>
                @can('create permissions')
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                        {{ __('Thêm Quyền hạn Mới') }}
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
                <input wire:model.live="search" type="text" placeholder="{{ __('Tìm kiếm quyền hạn...') }}"
                       class="form-control">
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">{{ __('Tên Quyền hạn') }}</th>
                            <th scope="col">{{ __('Số lượng Vai trò') }}</th>
                            <th scope="col">{{ __('Ngày tạo') }}</th>
                            <th scope="col">{{ __('Hành động') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->roles_count }}</td>
                                <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @can('edit permissions')
                                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-info me-2">
                                            {{ __('Sửa') }}
                                        </a>
                                    @endcan
                                    @can('delete permissions')
                                        <button wire:click="deletePermission({{ $permission->id }})"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa quyền hạn này không?');"
                                                class="btn btn-sm btn-danger"
                                                @if($permission->roles_count > 0) disabled @endif>
                                            {{ __('Xóa') }}
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    {{ __('Không tìm thấy quyền hạn nào.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $permissions->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
