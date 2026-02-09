    <x-guest-layout>
        <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center bg-body text-body">
            <div class="container py-5">

                <header class="text-center mb-4">
                    <h1 class="display-6 fw-bold text-primary">QR Code Viewer</h1>
                    <p class="lead text-muted">Ứng dụng xem và quản lý mã QR</p>
                </header>

                <main class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6 col-xl-5">

                        {{-- KHỐI CARD CHÍNH --}}
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4 text-center">

                                @if (Route::has('login'))
                                    @auth
                                        {{-- TRƯỜNG HỢP ĐÃ ĐĂNG NHẬP --}}
                                        <div class="mb-3">
                                            {{-- Avatar giả lập hoặc icon user --}}
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-3"
                                                style="width: 60px; height: 60px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                                    fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                                </svg>
                                            </div>
                                            <h5 class="card-title fw-bold">Xin chào, {{ Auth::user()->name }}!</h5>
                                            <p class="card-text text-success">
                                                <small>● Bạn đã đăng nhập thành công</small>
                                            </p>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg shadow-sm">
                                                Truy cập Dashboard
                                            </a>
                                        </div>
                                    @else
                                        {{-- TRƯỜNG HỢP CHƯA ĐĂNG NHẬP (GUEST) --}}
                                        <div class="mb-4">
                                            <h5 class="card-title fw-bold mb-2">Bắt đầu ngay</h5>
                                            <p class="card-text text-muted">Vui lòng đăng nhập hoặc tạo tài khoản để sử dụng
                                                dịch vụ.</p>
                                        </div>

                                        {{-- Sử dụng d-grid để nút bấm full chiều ngang trên mobile --}}
                                        <div class="d-grid gap-3">
                                            <a href="{{ route('login') }}"
                                                class="btn btn-primary btn-lg fw-semibold shadow-sm">
                                                Đăng nhập
                                            </a>

                                            @if (Route::has('register'))
                                                <a href="{{ route('register') }}"
                                                    class="btn btn-outline-secondary btn-lg fw-semibold">
                                                    Đăng ký tài khoản mới
                                                </a>
                                            @endif
                                        </div>
                                    @endauth
                                @endif

                            </div>
                        </div>
                        {{-- KẾT THÚC KHỐI CARD --}}

                    </div>
                </main>
            </div>
        </div>
        {{-- Footer --}}
        <footer class="bg-body text-body py-3 text-center border-top">
            <div class="container">
                Framework {{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
            </div>
        </footer>


        <script>
            // Optional: Đảm bảo trang chào mừng cũng tuân theo chế độ sáng/tối
            // nếu nó không sử dụng layout chính của bạn.
            document.addEventListener('DOMContentLoaded', function() {
                const htmlElement = document.documentElement;
                const currentTheme = localStorage.getItem('theme') || 'light';
                htmlElement.setAttribute('data-bs-theme', currentTheme);
            });
        </script>
    </x-guest-layout>
