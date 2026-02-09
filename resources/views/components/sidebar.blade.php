{{-- sidebar.blade.php --}}
<div class="border-right" id="sidebar-wrapper">
    <div class="sidebar-heading py-4 ps-4  d-flex justify-content-between align-items-center"> {{-- Thêm d-flex, justify-content-between, align-items-center --}}
        {{-- <div> Bọc icon và text trong một div để giữ chúng lại với nhau --}}

        <x-mh-logo-icon class="size-5 fill-current text-white dark:text-black" />
        {{-- </div> --}}
        {{-- Nút đóng sidebar cho mobile --}}
        <button class="btn btn-link  d-block d-lg-none me-2" id="sidebarClose" aria-label="Close sidebar">
            <i class="fas fa-times"></i> {{-- Icon dấu X --}}
        </button>
    </div>
    <div class="list-group list-group-flush">
        <a href="/dashboard" class="list-group-item list-group-item-action py-3 ps-4  ">
            <i class="fas fa-tachometer-alt me-2"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
        @can('view users')
            <a href="{{ route('qrcodes.index') }}" class="list-group-item list-group-item-action py-3 ps-4  ">
                <i class="fa-solid fa-qrcode"></i>
                <span class="sidebar-text">QR Code List</span>
            </a>
        @endcan
        @role('admin')
            <div class="list-group-item py-3 ps-4 pe-2 d-flex justify-content-between align-items-center sidebar-dropdown-toggle  "
                data-bs-toggle="collapse" href="#userSubmenu" role="button"
                aria-expanded="{{ isset($menu) && str_contains($menu, 'user') ? 'true' : 'false' }}"
                aria-controls="userSubmenu">

                <div>
                    <i class="fa-solid fa-user"></i>
                    <span class="sidebar-text">Người dùng</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
            </div>
            <div class="collapse {{ isset($menu) && str_contains($menu, 'user') ? 'show' : '' }}" id="userSubmenu"
                data-bs-parent="#sidebar-wrapper">
                <a href="{{ route('users.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && $menu == 'user' ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span class="sidebar-text">Danh sách</span> </a>

                <a href="{{ route('departments.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary ">
                    <i class="fa-solid fa-user-group"></i>
                    <span class="sidebar-text">Phòng ban</span>
                </a>
                <a href="{{ route('roles.index') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-secondary ">
                    <i class="fa-solid fa-users-gear"></i>
                    <span class="sidebar-text">Vai trò</span>
                </a>
                <a href="{{ route('permissions.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary ">
                    <i class="fa-solid fa-user-shield"></i>
                    <span class="sidebar-text">Phân quyền</span>
                </a>
            </div>
        @endrole
        {{-- Menu cho khối Sản xuất / Thống kê --}}
        @can('print barcodes')
            <a href="{{ route('production.barcode-generator') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.barcode-generator') ? 'active' : '' }}">
                <i class="fa-solid fa-print me-2"></i>
                <span class="sidebar-text">In Tem Mã Vạch</span>
            </a>
        @endcan
        {{-- @can('admin')
            <a href="{{ route('departments.index') }}" class="list-group-item list-group-item-action py-3 ps-4 ">
               <i class="fa-solid fa-user-group"></i>
                <span class="sidebar-text">Phòng ban</span>
            </a>
        @endcan --}}
    </div>
</div>
