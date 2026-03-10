<nav class="navbar navbar-expand-lg bg-body border-bottom">
    <div class="container-fluid">

        <a class="navbar-brand d-lg-none ms-3" href="#">QR Code</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            {{-- Đảm bảo các liên kết này nằm trong một ul.navbar-nav --}}
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0"> {{-- ms-auto sẽ đẩy các item này sang phải --}}
                {{-- Dark/Light Mode Toggle (giữ nguyên) --}}
                <li class="nav-item me-3">
                    <button class="btn btn-link text-decoration-none" id="themeToggle">
                        {{-- Icon mặt trăng (Dark Mode): Bắt đầu ẩn --}}
                        <i class="fas fa-moon dark-icon d-none"></i>
                        {{-- Icon mặt trời (Light Mode): Bắt đầu hiển thị (giả định mặc định là chế độ sáng) --}}
                        <i class="fas fa-sun light-icon"></i>
                        <span class="d-none d-lg-inline ms-1">Mode</span>
                    </button>
                </li>

                @auth
                    {{-- Nếu người dùng đã đăng nhập, hiển thị dropdown menu --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ url('/dashboard') }}">Dashboard</a> {{-- Thêm liên kết Dashboard --}}
                            <a class="dropdown-item" href="#">Profile</a> {{-- Ví dụ: liên kết Profile --}}
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Log Out</button>
                            </form>
                        </div>
                    </li>
                @else
                    {{-- Nếu người dùng chưa đăng nhập, hiển thị Login và Register --}}
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">
                            Log in
                        </a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="nav-link">
                                Register
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</nav>
<script>
    // JS cho Sidebar Toggle
    // JS cho Dark/Light Mode
    document.addEventListener('DOMContentLoaded', function() {
        // ... (các biến và logic sidebar toggle) ...

        const themeToggleBtn = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;
        const darkIcon = document.querySelector('.dark-icon');
        const lightIcon = document.querySelector('.light-icon');

        // Set initial theme based on localStorage or default
        const currentTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-bs-theme', currentTheme);
        if (currentTheme === 'dark') {
            darkIcon.classList.remove('d-none');
            lightIcon.classList.add('d-none');
        } else { // currentTheme is 'light'
            lightIcon.classList.remove('d-none');
            darkIcon.classList.add('d-none');
        }

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                let newTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                htmlElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);

                if (newTheme === 'dark') {
                    darkIcon.classList.remove('d-none');
                    lightIcon.classList.add('d-none');
                } else {
                    lightIcon.classList.remove('d-none');
                    darkIcon.classList.add('d-none');
                }
            });
        }
    });
</script>
