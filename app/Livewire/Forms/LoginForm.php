<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;
use Livewire\Form;
use  App\Models\User;
class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $login_id = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticateold(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => trans('Thông tin đăng nhập không chính xác.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Phân biệt người dùng nhập email hay username
        $field = filter_var($this->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 2. Tìm kiếm User trong Database theo field
        $user = User::where($field, $this->login_id)->first();

        // 3. KỊCH BẢN 1: Nếu không tìm thấy User trong hệ thống
        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.login_id' => 'Email hoặc Username này chưa được đăng ký trong hệ thống.',
            ]);
        }

        // 4. KỊCH BẢN 2: Đã tìm thấy Email, nhưng check Mật khẩu bị sai
        if (! Hash::check($this->password, $user->password)) {
           RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.password' => 'Mật khẩu bạn nhập không chính xác.',
            ]);
        }

        // 4. KỊCH BẢN 3: Đúng cả Email và Mật khẩu -> Cho phép đăng nhập
        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
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

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->login_id) . '|' . request()->ip());
    }
}
