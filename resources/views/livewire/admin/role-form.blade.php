{{-- resources/views/livewire/role-form.blade.php --}}
<div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="h5 card-title mb-4">{{ __($title) }}</h3>

            <form wire:submit.prevent="saveRole">
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Tên Vai trò') }}</label>
                    <input wire:model="name" type="text" id="name"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Quyền hạn') }}</label>
                    <div class="row">
                        @foreach($allPermissions as $permission)
                            <div class="col-md-4 col-sm-6 col-12">
                                <div class="form-check">
                                    <input wire:model="selectedPermissions" class="form-check-input" type="checkbox" value="{{ $permission->name }}" id="permission-{{ $permission->id }}">
                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedPermissions')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                    @error('selectedPermissions.*')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary me-2">
                        {{ __('Hủy') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Lưu') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
