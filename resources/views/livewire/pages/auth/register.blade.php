<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        // Bỏ navigate: true để ép tải lại toàn trang, giúp hiển thị đúng Sidebar của Admin
        $this->redirect(route('dashboard', absolute: false));
    }
}; ?>

{{-- Bỏ thẻ <div class="container..."><div class="card..."> vì layout gốc đã có --}}
<div>
    <h3 class="text-center fw-bold text-primary mb-4">Đăng Ký Tài Khoản</h3>

    <form wire:submit="register">

        {{-- Tên của bạn --}}
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">{{ __('Tên của bạn') }}</label>
            <input wire:model="name" id="name" class="form-control form-control-lg" type="text" name="name"
                required autofocus autocomplete="name" placeholder="Ví dụ: Nguyễn Văn A" />
            @error('name') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">{{ __('Email') }}</label>
            <input wire:model="email" id="email" class="form-control form-control-lg" type="email" name="email"
                required autocomplete="username" placeholder="name@example.com" />
            @error('email') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
        </div>

        {{-- Mật khẩu --}}
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">{{ __('Mật khẩu') }}</label>
            <input wire:model="password" id="password" class="form-control form-control-lg" type="password" name="password"
                required autocomplete="new-password" placeholder="********" />
            @error('password') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
        </div>

        {{-- Xác nhận mật khẩu --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold">{{ __('Xác nhận mật khẩu') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" class="form-control form-control-lg" type="password" name="password_confirmation"
                required autocomplete="new-password" placeholder="********" />
            @error('password_confirmation') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
        </div>

        {{-- Nút Đăng ký --}}
        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
            {{ __('Đăng ký') }}
        </button>

        {{-- Quay lại Đăng nhập --}}
        <hr class="my-4 text-muted">
        <div class="text-center">
            <p class="text-secondary mb-2 small">Đã có tài khoản?</p>
            <a class="btn btn-outline-secondary w-100" href="{{ route('login') }}" wire:navigate>
                Đăng nhập ngay
            </a>
        </div>
    </form>
</div>
