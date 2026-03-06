<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
    <link href="{{ asset('css/bunny.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <meta name="theme-color" content="#0d6efd" />
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    <style>
        /* Bắt buộc hiển thị menu con khi nó có class .show, bất kể sidebar đang đóng hay mở */
        #wrapper.toggled #sidebar-wrapper .collapse.show {
            display: block !important;
        }
    </style>
    {{-- THÊM ĐOẠN NÀY ĐỂ CHẶN FLICKER --}}
    <script>
        // Script này chạy ngay lập tức, chặn việc render cho đến khi set xong theme
        (function() {
            const storedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', storedTheme);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{-- THÊM DÒNG NÀY ĐỂ MỞ ĐƯỜNG CHÈN CSS TỪ CÁC TRANG CON --}}
    @stack('styles')
</head>

<body class="font-sans antialiased">
    {{-- Loại bỏ class "toggled" ban đầu khỏi #wrapper --}}
    {{-- Mặc định, trên desktop sidebar sẽ mở, trên mobile sẽ ẩn --}}
    {{-- Laravel sẽ đọc cookie 'sidebarState'. Nếu là 'toggled' thì in luôn class đó ra HTML --}}
    {{-- Dùng $_COOKIE của PHP thuần để đọc trực tiếp cookie do JS tạo ra --}}
    <div class="d-flex {{ isset($_COOKIE['sidebarState']) && $_COOKIE['sidebarState'] === 'toggled' ? 'toggled' : '' }}"
        id="wrapper">
        {{-- @include('components.sidebar') --}}

        <livewire:sidebar-navigation />
        <div id="page-content-wrapper" class="flex-grow-1 px-1">
            <nav class="navbar navbar-expand-lg border-bottom">
                <div class="container-fluid">
                    {{-- Nút Sidebar Toggle:
                         - d-block: Luôn hiển thị.
                         - d-lg-none: Ẩn trên màn hình lớn (>= lg) nếu muốn.
                           Nếu bạn muốn nút này luôn hiện trên desktop để thu gọn sidebar, hãy bỏ d-lg-none.
                    --}}
                    <button class="btn btn-primary d-block" id="sidebarToggle"><i class="fas fa-bars"
                            id="toggleIcon"></i></button>

                    {{-- Brand cho mobile: Chỉ hiển thị khi sidebar ẩn trên mobile --}}
                    <a class="navbar-brand d-lg-none ms-3" href="#">QR Mobile</a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0 flex-row align-items-center">
                            {{-- Dark/Light Mode Toggle --}}
                            <li class="nav-item me-3">
                                <button class="btn btn-link text-decoration-none" id="themeToggle">
                                    <i class="fas fa-moon d-none dark-icon"></i>
                                    <i class="fas fa-sun d-none light-icon"></i>
                                    <span class="d-none d-lg-inline ms-1">Mode</span>
                                </button>
                            </li>
                            @auth
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ Auth::user()->name }}

                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <span class="badge text-bg-secondary"> <i class="fa-solid fa-envelope"></i>
                                            {{ Auth::user()->email }}
                                        </span>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="profile"><i class="fa-solid fa-id-badge"></i>
                                            Profile</a>
                                        <div class="dropdown-divider"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item"><i
                                                    class="fa-solid fa-right-from-bracket"></i> Log Out</button>
                                        </form>
                                    </div>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}"><i
                                            class="fa-solid fa-right-to-bracket"></i> Login</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}"><i class="fa-solid fa-user"></i>
                                            Register</a>
                                    </li>
                                @endif
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="container-fluid py-2">
                {{ $slot }}
            </div>
        </div>
    </div>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
    @livewireScripts
</body>

</html>
