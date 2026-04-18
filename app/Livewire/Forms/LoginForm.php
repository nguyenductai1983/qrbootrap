<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $login_id = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Quy trình xác thực chuẩn
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Xác định field đăng nhập (Email hay Username)
        $fieldType = filter_var($this->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 2. Sử dụng Auth::attempt thay vì Hash::check thủ công
        // Cách này giúp Laravel tự kích hoạt các sự kiện (Events) và quản lý Session chuẩn hơn
        if (! Auth::attempt([$fieldType => $this->login_id, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            // Thông báo lỗi chung để tăng tính bảo mật (Tránh bị dò tìm tài khoản)
            // Hoặc giữ lại thông báo riêng của bạn nếu là hệ thống nội bộ
            throw ValidationException::withMessages([
                'form.login_id' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Kiểm tra giới hạn đăng nhập
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.login_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->login_id) . '|' . request()->ip());
    }
}
