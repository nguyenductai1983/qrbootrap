<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Bao bọc form trong một container Bootstrap để căn giữa và tạo hiệu ứng thẻ --}}
    <div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px);">
        {{-- Khoảng trống giả định cho Navbar nếu có --}}
        <div class="card p-4 shadow-lg" style="width: 450px;">
            <div class="mb-4 text-muted">
                {{ __('Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.') }}
            </div>

            <form wire:submit="confirmPassword">
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Mật khẩu') }}</label>
                    <input wire:model="password"
                           id="password"
                           class="form-control"
                           type="password"
                           name="password"
                           required autocomplete="current-password" />
                    @error('password') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Xác nhận') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
