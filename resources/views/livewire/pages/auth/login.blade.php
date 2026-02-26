<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Ép tải lại trang bằng redirect bình thường để tải lại Sidebar / Layout chính
        $this->redirectIntended(default: route('dashboard', absolute: false));
    }
}; ?>

{{-- Bỏ thẻ <div class="card..."> ở đây vì layouts.guest đã có sẵn thẻ Card rồi --}}
<div>
    <h3 class="text-center fw-bold text-primary mb-4">Đăng Nhập</h3>

    <x-auth-session-status class="mb-3 alert alert-info" :status="session('status')" />

    <form wire:submit="login">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">{{ __('Email') }}</label>
            <input wire:model="form.email" id="email" class="form-control form-control-lg" type="email" name="email"
                value="{{ old('email') }}" required autofocus placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">{{ __('Password') }}</label>
            <input id="password" class="form-control form-control-lg" wire:model="form.password" type="password"
                name="password" required placeholder="********" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
        </div>

        {{-- Remember Me & Forgot Password --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input wire:model="form.remember" id="remember_me" type="checkbox" class="form-check-input"
                    name="remember">
                <label for="remember_me" class="form-check-label text-secondary">{{ __('Remember me') }}</label>
            </div>

            @if (Route::has('password.request'))
                <a class="text-decoration-none small fw-semibold" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Quên mật khẩu?') }}
                </a>
            @endif
        </div>

        {{-- Nút Login --}}
        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
            {{ __('Đăng nhập') }}
        </button>

        {{-- Phần Đăng ký --}}
        @if (Route::has('register'))
            <hr class="my-4 text-muted">
            <div class="text-center">
                <p class="text-secondary mb-2 small">Bạn chưa có tài khoản?</p>
                <a class="btn btn-outline-secondary w-100" href="{{ route('register') }}" wire:navigate>
                    Đăng ký ngay
                </a>
            </div>
        @endif
    </form>
</div>
