{{-- resources/views/livewire/department-list.blade.php --}}
<div>
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h5 card-title">{{ __('Quản lý Phòng ban') }}</h3>
                <a href="{{ route('departments.create') }}" class="btn btn-primary">
                    {{ __('Thêm Phòng ban Mới') }}
                </a>
            </div>

            {{-- Thông báo Flash --}}
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

            {{-- Thanh tìm kiếm --}}
            <div class="mb-3">
                <input wire:model.live="search" type="text" placeholder="{{ __('Tìm kiếm phòng ban...') }}"
                    class="form-control">
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">{{ __('Tên Phòng ban') }}</th>
                            <th scope="col">{{ __('Số lượng Người dùng') }}</th>
                            <th scope="col">{{ __('Ngày tạo') }}</th>
                            <th scope="col">{{ __('Hành động') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                            <tr>
                                <td>{{ $department->id }}</td>
                                <td>{{ $department->name }}</td>
                                <td>{{ $department->users_count ?? $department->users()->count() }}</td>
                                {{-- Hiển thị số lượng người dùng --}}
                                <td>{{ $department->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('departments.edit', $department) }}"
                                        class="btn btn-sm btn-info me-2">
                                        {{ __('Sửa') }}
                                    </a>
                                    <button wire:click="deleteDepartment({{ $department->id }})"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa phòng ban này không? Nó sẽ không thể xóa nếu có người dùng thuộc về nó.');"
                                        class="btn btn-sm btn-danger">
                                        {{ __('Xóa') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    {{ __('Không tìm thấy phòng ban nào.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $departments->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
