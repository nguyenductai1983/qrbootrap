<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    {{-- Bao bọc form trong một container Bootstrap để căn giữa và tạo hiệu ứng thẻ --}}
    <div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px);">
        {{-- Khoảng trống giả định cho Navbar nếu có --}}
        <div class="card p-4 shadow-lg" style="width: 450px;">
            <div class="mb-4 text-muted">
                {{ __('Quên mật khẩu? Đừng lo lắng. Hãy cho chúng tôi biết địa chỉ email của bạn và chúng tôi sẽ gửi cho bạn một liên kết đặt lại mật khẩu để bạn có thể chọn mật khẩu mới.') }}
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="sendPasswordResetLink">
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input wire:model="email" id="email" class="form-control" type="email" name="email" required autofocus />
                    @error('email') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Gửi liên kết đặt lại mật khẩu') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
