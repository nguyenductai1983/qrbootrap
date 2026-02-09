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

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Bao bọc form trong một container Bootstrap để căn giữa và tạo hiệu ứng thẻ --}}
    <div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px);">
        {{-- Khoảng trống giả định cho Navbar nếu có --}}
        <div class="card p-4 shadow-lg" style="width: 450px;">
            <h2 class="text-center mb-4">Đăng ký tài khoản</h2>

            <form wire:submit="register">
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Tên của bạn') }}</label>
                    <input wire:model="name" id="name" class="form-control" type="text" name="name" required autofocus autocomplete="name" />
                    @error('name') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input wire:model="email" id="email" class="form-control" type="email" name="email" required autocomplete="username" />
                    @error('email') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Mật khẩu') }}</label>
                    <input wire:model="password" id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
                    @error('password') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('Xác nhận mật khẩu') }}</label>
                    <input wire:model="password_confirmation" id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
                    @error('password_confirmation') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a class="text-secondary text-decoration-none" href="{{ route('login') }}" wire:navigate>
                        {{ __('Đã có tài khoản?') }}
                    </a>

                    <button type="submit" class="btn btn-primary">
                        {{ __('Đăng ký') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
