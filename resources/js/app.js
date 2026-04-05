import './bootstrap';
import './echo';
import * as bootstrap from 'bootstrap'; // Import toàn bộ Bootstrap JS
window.bootstrap = bootstrap;
document.addEventListener('livewire:navigated', function () {
    // --- 1. KHAI BÁO BIẾN ---
    const sidebarToggle = document.getElementById('sidebarToggle');
    const wrapper = document.getElementById('wrapper');
    // Lấy tất cả các nút menu có khả năng sổ xuống (Menu cha)
    const dropdownToggles = document.querySelectorAll('.sidebar-dropdown-toggle');
    const toggleIcon = document.getElementById('toggleIcon');
    // --- 2. XỬ LÝ NÚT 3 GẠCH (MỞ/THU NHỎ THỦ CÔNG) ---
    // 🌟 HÀM XỬ LÝ ĐỔI ICON 🌟
    const updateToggleIcon = () => {
        if (!toggleIcon) return; // Bảo vệ lỡ không tìm thấy icon

        // Kiểm tra xem Sidebar đang thu nhỏ (toggled) hay mở to
        if (wrapper.classList.contains('toggled')) {
            // Khi thu nhỏ: Đổi thành icon xếp dòng
            toggleIcon.className = 'fa-solid fa-align-left';
        } else {
            // Khi mở to: Trở về icon 3 gạch mặc định
            toggleIcon.className = 'fas fa-bars';
        }
    };
    updateToggleIcon();
    if (sidebarToggle && wrapper) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            // Bật/tắt class toggled
            wrapper.classList.toggle('toggled');
            updateToggleIcon();
            // 🌟 LƯU VÀO LOCAL STORAGE 🌟
            if (wrapper.classList.contains('toggled')) {
                document.cookie = "sidebarState=toggled; path=/; max-age=31536000";
            } else {
                document.cookie = "sidebarState=expanded; path=/; max-age=31536000";
            }
        });
    }
    // Gọi hàm này ngay khi trang vừa tải xong để Icon khớp với bộ nhớ Local Storage

    // Tương tự cho các nút đóng trên Mobile (dùng event delegation để tránh mất listener khi Livewire re-render)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.sidebar-close-btn');
        if (btn && wrapper) {
            e.preventDefault();
            wrapper.classList.remove('toggled');
            document.cookie = "sidebarState=expanded; path=/; max-age=31536000";
            updateToggleIcon();
        }
    });

    // --- TỰ ĐÓNG SIDEBAR TRÊN MOBILE KHI BẤM VÀO MENU ITEM ---
    // Lắng nghe click trên tất cả các link (<a>) bên trong sidebar
    const sidebarLinks = document.querySelectorAll('#sidebar-wrapper a.list-group-item');
    sidebarLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            // Chỉ đóng khi đang ở màn hình mobile (nhỏ hơn breakpoint lg = 992px)
            if (window.innerWidth < 992 && wrapper) {
                wrapper.classList.add('toggled');
                document.cookie = "sidebarState=expanded; path=/; max-age=31536000";
                updateToggleIcon();
            }
        });
    });

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
