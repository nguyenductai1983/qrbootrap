<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
 {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false));
        // $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="container d-flex justify-content-center align-items-center">
    {{-- Sử dụng w-100 để full màn hình điện thoại, style max-width để giới hạn trên PC --}}
    <div class="card shadow-lg border-0 rounded-3 w-100 p-3" style="max-width: 500px;">
        <h4 class="text-center fw-bold text-primary">Login</h4>

        <x-auth-session-status class="mb-3 alert alert-info" :status="session('status')" />
        <form wire:submit="login">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">{{ __('Email') }}</label>
                <input wire:model="form.email" id="email" class="form-control form-control-lg" type="email"
                    name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com" />
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
                    <a class="text-decoration-none small" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            {{-- Nút Login: Để full width (w-100) cho dễ bấm trên điện thoại --}}
            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                {{ __('Log in') }}
            </button>

            {{-- Phần Đăng ký --}}
            @if (Route::has('register'))
                <hr class="my-4">
                <div class="text-center">
                    <p class="text-secondary mb-2 small">Chưa có tài khoản?</p>
                    <a class="btn btn-outline-secondary w-100" href="{{ route('register') }}" wire:navigate>
                        Đăng ký ngay
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
