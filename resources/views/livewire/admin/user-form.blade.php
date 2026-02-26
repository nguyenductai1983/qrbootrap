{{-- resources/views/livewire/user-form.blade.php --}}
<div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="h5 card-title mb-4">{{ __($title) }}</h3>

            <form wire:submit.prevent="saveUser">
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Tên') }}</label>
                    <input wire:model="name" type="text" id="name"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input wire:model="email" type="email" id="email"
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Mật khẩu') }}</label>
                    <input wire:model="password" type="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           @if($user->exists) placeholder="Để trống nếu không muốn thay đổi" @else required @endif>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('Xác nhận Mật khẩu') }}</label>
                    <input wire:model="password_confirmation" type="password" id="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           @if($user->exists) placeholder="Để trống nếu không muốn thay đổi" @else required @endif autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="department_id" class="form-label">{{ __('Phòng ban') }}</label>
                    <select wire:model="department_id" id="department_id"
                            class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">{{ __('Chọn phòng ban') }}</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <-- Trường gán Vai trò (từ Spatie) --> --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('Gán Vai trò') }}</label>
                    <div class="row">
                        @foreach($allRoles as $roleOption)
                            <div class="col-md-4 col-sm-6 col-12">
                                <div class="form-check">
                                    <input wire:model="selectedRoles" class="form-check-input" type="checkbox" value="{{ $roleOption->name }}" id="role-{{ $roleOption->id }}"
                                           @if($user->id === auth()->id() && $roleOption->name === 'admin') disabled @endif> {{-- Không cho tự gỡ admin --}}
                                    <label class="form-check-label" for="role-{{ $roleOption->id }}">
                                        {{ $roleOption->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedRoles')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                    @error('selectedRoles.*')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
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
