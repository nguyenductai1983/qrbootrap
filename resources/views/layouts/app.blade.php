<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
</head>

<body class="font-sans antialiased">
    {{-- Loại bỏ class "toggled" ban đầu khỏi #wrapper --}}
    {{-- Mặc định, trên desktop sidebar sẽ mở, trên mobile sẽ ẩn --}}
    <div class="d-flex" id="wrapper">
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
                    <button class="btn btn-primary d-block" id="sidebarToggle"><i class="fas fa-bars"></i></button>

                    {{-- Brand cho mobile: Chỉ hiển thị khi sidebar ẩn trên mobile --}}
                    <a class="navbar-brand d-lg-none ms-3" href="#">Laravel Admin</a>

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. KHAI BÁO BIẾN ---
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const wrapper = document.getElementById('wrapper');
            // Lấy tất cả các nút menu có khả năng sổ xuống (Menu cha)
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown-toggle');

            // --- 2. XỬ LÝ NÚT 3 GẠCH (MỞ/THU NHỎ THỦ CÔNG) ---
            if (sidebarToggle && wrapper) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();

                    // (Tùy chọn) Trước khi thu nhỏ, đóng hết các menu con đang mở cho gọn
                    if (!wrapper.classList.contains('toggled')) {
                        document.querySelectorAll('#sidebar-wrapper .collapse.show').forEach(openMenu => {
                            openMenu.classList.remove('show');
                            let trigger = document.querySelector(`[href="#${openMenu.id}"]`);
                            if (trigger) trigger.setAttribute('aria-expanded', 'false');
                        });
                    }

                    wrapper.classList.toggle('toggled');
                });
            }

            // --- 3. XỬ LÝ NÚT ĐÓNG (TRÊN MOBILE) ---
            if (sidebarClose && wrapper) {
                sidebarClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    wrapper.classList.remove('toggled');
                });
            }

            // --- 4. [TÍNH NĂNG BẠN CẦN]: TỰ ĐỘNG MỞ TO KHI CHỌN MENU ---
            if (dropdownToggles && wrapper) {
                dropdownToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        // Kiểm tra: Nếu Sidebar đang bị thu nhỏ (có class toggled)
                        if (wrapper.classList.contains('toggled')) {
                            // Thì xóa class toggled đi => Sidebar sẽ tự động mở to ra ngay lập tức
                            wrapper.classList.remove('toggled');
                        }
                        // Nếu Sidebar đang to sẵn rồi thì thôi, để Bootstrap tự xử lý việc sổ menu con
                    });
                });
            }

            // --- 5. XỬ LÝ DARK/LIGHT MODE (GIỮ NGUYÊN) ---
            const themeToggleBtn = document.getElementById('themeToggle');
            const htmlElement = document.documentElement;
            const darkIcon = document.querySelector('.dark-icon');
            const lightIcon = document.querySelector('.light-icon');

            // Hàm cập nhật Icon dựa trên theme hiện tại
            const updateThemeIcon = () => {
                // Lấy theme trực tiếp từ HTML tag (đã được set ở Head)
                const currentTheme = htmlElement.getAttribute('data-bs-theme');

                if (currentTheme === 'dark') {
                    darkIcon.classList.remove('d-none');
                    lightIcon.classList.add('d-none');
                } else {
                    lightIcon.classList.remove('d-none');
                    darkIcon.classList.add('d-none');
                }
            };

            // Chạy ngay lập tức để icon đúng với theme
            updateThemeIcon();
            // Xử lý sự kiện click
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    // Đảo ngược theme
                    const newTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' :
                    'dark';

                    // Cập nhật lại HTML và LocalStorage
                    htmlElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);

                    // Cập nhật Icon
                    updateThemeIcon();
                });
            }
        });
    </script>
</body>

</html>
