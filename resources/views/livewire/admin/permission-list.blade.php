{{-- resources/views/livewire/permission-list.blade.php --}}
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
                <h3 class="h5 card-title">{{ __('Quản lý Quyền hạn') }}</h3>
                @can('permissions.create')
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
            <div class="mb-3 position-relative">

                {{-- Ô Input: Thêm padding-right: 35px để chừa chỗ cho nút X --}}
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="{{ __('Tìm kiếm quyền hạn...') }}" class="form-control pe-5" autocomplete="off">

                {{-- 🌟 NÚT RESET (Chỉ hiển thị khi người dùng đã gõ chữ) 🌟 --}}
                @if (strlen($search) > 0)
                    <span wire:click="clearSearch" class="position-absolute top-50 translate-middle-y text-secondary"
                        style="right: 15px; cursor: pointer; z-index: 10;" title="Xóa tìm kiếm">
                        <i class="fas fa-times"></i>
                    </span>
                @endif

                {{-- Khối hiển thị danh sách gợi ý (GIỮ NGUYÊN) --}}
                @if ($showSuggestions && !empty($search) && $suggestions->isNotEmpty())
                    <ul class="list-group position-absolute w-100 shadow-sm"
                        style="z-index: 1050; max-height: 250px; overflow-y: auto; margin-top: 2px;">
                        @foreach ($suggestions as $suggestion)
                            <li wire:click="selectSuggestion('{{ $suggestion->name }}')"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                style="cursor: pointer;">
                                <span>{{ $suggestion->name }}</span>
                                <span class="badge bg-secondary rounded-pill">
                                    <i class="fas fa-search"></i>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Tên Quyền hạn') }}</th>
                            <th scope="col">{{ __('Số lượng Vai trò') }}</th>
                            <th scope="col">{{ __('Ngày tạo') }}</th>
                            <th scope="col">{{ __('Hành động') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->roles_count }}</td>
                                <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @can('permissions.edit')
                                        <a href="{{ route('permissions.edit', $permission) }}"
                                            class="btn btn-sm btn-info me-2">
                                            {{ __('Sửa') }}
                                        </a>
                                    @endcan
                                    @can('permissions.delete')
                                        <button 
                                            wire:confirm="{{ __('Bạn có chắc chắn muốn xóa quyền hạn này không?') }}"
                                            wire:click="deletePermission({{ $permission->id }})"
                                            class="btn btn-sm btn-danger" @if ($permission->roles_count > 0) disabled @endif>
                                            {{ __('Xóa') }}
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
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
