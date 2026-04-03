{{-- resources/views/livewire/department-list.blade.php --}}
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
                <h3 class="h5 card-title">{{ __('Quản lý Bộ phận') }}</h3>
                @can('departments.create')
                    <a href="{{ route('departments.create') }}" class="btn btn-primary">
                        {{ __('Thêm Bộ phận Mới') }}
                    </a>
                @endcan
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
                <input wire:model.live="search" type="text" placeholder="{{ __('Tìm kiếm Bộ phận...') }}"
                    class="form-control">
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Tên Bộ phận') }}</th>
                            <th scope="col">{{ __('Mã') }}</th>
                            <th scope="col">{{ __('Số lượng Người dùng') }}</th>
                            <th scope="col">{{ __('Ngày tạo') }}</th>
                            <th scope="col">{{ __('Hành động') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                            <tr>
                                <td>{{ $department->name }}</td>
                                <td>{{ $department->code }}</td>
                                <td>{{ $department->users_count ?? $department->users()->count() }}</td>
                                {{-- Hiển thị số lượng người dùng --}}
                                <td>{{ $department->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('departments.edit', $department) }}"
                                        class="btn btn-sm btn-info me-2">
                                        {{ __('Sửa') }}
                                    </a>
                                    <button
                                        wire:confirm="{{ __('Bạn có chắc chắn muốn xóa Bộ phận này không? Nó sẽ không thể xóa nếu có người dùng thuộc về nó.') }}"
                                        wire:click="deleteDepartment({{ $department->id }})"
                                        class="btn btn-sm btn-danger">
                                        {{ __('Xóa') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    {{ __('Không tìm thấy Bộ phận nào.') }}
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
