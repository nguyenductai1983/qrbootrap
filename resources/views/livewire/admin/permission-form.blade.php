{{-- resources/views/livewire/permission-form.blade.php --}}
<div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="h5 card-title mb-4">{{ __($title) }}</h3>

            <form wire:submit.prevent="savePermission">
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Tên Quyền hạn') }}</label>
                    <input wire:model="name" type="text" id="name"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary me-2">
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
