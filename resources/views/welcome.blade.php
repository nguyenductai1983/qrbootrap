<x-guest-layout>
    <div class="text-center">

        {{-- HEADER --}}
        <header class="mb-4 mb-sm-5">
            <h1 class="display-6 fw-bold text-primary mb-2">QR Code Viewer</h1>
            <p class="text-muted mb-0">Ứng dụng xem và quản lý mã QR</p>
        </header>

        {{-- KHU VỰC ĐIỀU HƯỚNG THEO TRẠNG THÁI ĐĂNG NHẬP --}}
        @if (Route::has('login'))
            @auth
                {{-- TRƯỜNG HỢP ĐÃ ĐĂNG NHẬP --}}
                <div class="mb-4">
                    {{-- Icon user --}}
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-3"
                        style="width: 70px; height: 70px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35"
                            fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        </svg>
                    </div>
                    <h4 class="fw-bold">Xin chào, {{ Auth::user()->name }}!</h4>
                    <p class="text-success small fw-semibold">
                        ● Bạn đã đăng nhập thành công
                    </p>
                </div>

                {{-- Nút vào Dashboard --}}
                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                    Truy cập Dashboard
                </a>

            @else
                {{-- TRƯỜNG HỢP CHƯA ĐĂNG NHẬP (GUEST) --}}
                <div class="mb-4">
                    <h5 class="fw-bold mb-2">Bắt đầu ngay</h5>
                    <p class="text-muted small">Vui lòng đăng nhập hoặc tạo tài khoản để sử dụng dịch vụ.</p>
                </div>

                {{-- Sử dụng d-grid để các nút giãn đều full chiều ngang --}}
                <div class="d-grid gap-3">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg fw-bold shadow-sm" wire:navigate>
                        Đăng nhập
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg fw-semibold" wire:navigate>
                            Đăng ký tài khoản mới
                        </a>
                    @endif
                </div>
            @endauth
        @endif

        {{-- FOOTER NHỎ Ở ĐÁY CARD --}}
        <div class="mt-5 pt-3 border-top text-muted small">
            Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
        </div>

    </div>

    {{-- SCRIPT DARK MODE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const htmlElement = document.documentElement;
            const currentTheme = localStorage.getItem('theme') || 'light';
            htmlElement.setAttribute('data-bs-theme', currentTheme);
        });
    </script>
</x-guest-layout>
