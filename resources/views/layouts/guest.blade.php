<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
    {{-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
    <link rel="stylesheet" href="{{ asset('css/fontawesome/css/all.min.css') }}">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body >
    {{-- Wrapper chính chiếm tối thiểu 100% chiều cao màn hình --}}
    <div class="d-flex flex-column min-vh-100">

        {{-- 1. TOP: THANH ĐIỀU HƯỚNG (NAVIGATION) --}}
        @if (Route::has('login'))
            <div class="w-100 d-flex justify-content-end p-3 p-md-4">
                <livewire:welcome.navigation />
            </div>
        @endif

        {{-- 2. MAIN: NỘI DUNG CHIA 2 CỘT --}}
        <div class="container flex-grow-1 d-flex align-items-center justify-content-center pb-5">
            <div class="row w-100 align-items-center gy-5">
                {{-- CỘT TRÁI: LOGO VÀ GIỚI THIỆU --}}
                <div class="col-lg-5 d-none d-lg-block text-lg-start pe-lg-5">

                    {{-- Logo --}}
                    <a href="/" wire:navigate class="d-inline-block mb-4">
                        {{-- Ép size logo an toàn bằng width. mx-auto mx-lg-0 giúp canh giữa trên điện thoại, canh trái trên PC --}}
                        <div class="mx-auto mx-lg-0" style="width: 120px; max-width: 100%;">
                            <x-application-logo />
                        </div>
                    </a>

                    {{-- Dòng giới thiệu (Bạn có thể tùy chỉnh chữ ở đây) --}}
                    <h1 class="fw-bold mb-3 display-6">
                        Chào mừng đến với hệ thống
                    </h1>
                    <p class="fs-5 mb-0">
                        Nền tảng quản lý thông minh, kiểm soát tem nhãn và theo dõi tiến độ sản xuất hiện đại. Đăng nhập
                        để bắt đầu công việc.
                    </p>
                </div>

                {{-- CỘT PHẢI: FORM ĐĂNG NHẬP ($slot) --}}
                <div class="col-lg-7 col-12 d-flex justify-content-center justify-content-lg-end">

                    {{-- Thẻ Card bọc form đăng nhập cho bo góc mềm mại, đổ bóng nổi bật --}}
                    <div class="card shadow-lg border-0 rounded-4 w-100">
                        <div class="card-body p-4 p-sm-5">
                            {{ $slot }}
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</body>

</html>
