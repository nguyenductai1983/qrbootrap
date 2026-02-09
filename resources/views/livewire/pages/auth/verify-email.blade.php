<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    {{-- Bao bọc nội dung trong một container Bootstrap để căn giữa và tạo hiệu ứng thẻ --}}
    <div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px);">
        <div class="card p-4 shadow-lg text-center" style="width: 450px;">
            <h2 class="mb-3">{{ __('Xác minh địa chỉ Email của bạn') }}</h2>
            <div class="mb-4 text-muted">
                {{ __('Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, bạn có thể xác minh địa chỉ email của mình bằng cách nhấp vào liên kết chúng tôi vừa gửi qua email cho bạn không? Nếu bạn không nhận được email, chúng tôi rất vui lòng gửi lại cho bạn.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success mb-4" role="alert">
                    {{ __('Một liên kết xác minh mới đã được gửi đến địa chỉ email bạn đã cung cấp trong quá trình đăng ký.') }}
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                <button wire:click="sendVerification" type="button" class="btn btn-primary mb-2 mb-md-0">
                    {{ __('Gửi lại Email xác minh') }}
                </button>

                {{-- Nút Logout --}}
                <button wire:click="logout" type="submit" class="btn btn-link text-danger text-decoration-none">
                    {{ __('Đăng xuất') }}
                </button>
            </div>
        </div>
    </div>
</div>
