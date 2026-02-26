{{-- sidebar-navigation.blade.php --}}
<div class="border-right" id="sidebar-wrapper">
    <div class="sidebar-heading py-4 ps-4 d-flex justify-content-between align-items-center">
        <x-mh-logo-icon class="size-5 fill-current text-white dark:text-black" />

        {{-- Nút đóng sidebar cho mobile --}}
        <button class="btn btn-link d-block d-lg-none me-2" id="sidebarClose" aria-label="Close sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="list-group list-group-flush">
        {{-- 1. DASHBOARD --}}
        <a href="/dashboard" class="list-group-item list-group-item-action py-3 ps-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            <span class="sidebar-text">Bảng điều khiển</span>
        </a>

        {{-- 2. NHÓM TÍNH NĂNG SẢN XUẤT (Production) --}}

        @can('print barcodes')
            <a href="{{ route('production.barcode-generator') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.barcode-generator') ? 'active' : '' }}">
                <i class="fa-solid fa-print me-2"></i>
                <span class="sidebar-text">In Tem Mã Vạch</span>
            </a>

            {{-- MỚI: Menu Quản lý Excel (Export/Import số liệu) --}}
            <a href="{{ route('production.excel-manager') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.excel-manager') ? 'active' : '' }}">
                <i class="fa-solid fa-file-excel me-2"></i>
                <span class="sidebar-text">Nhập/Xuất Excel</span>
            </a>
        @endcan

        @can('products scan')
            <a href="{{ route('production.scan') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.scan') ? 'active' : '' }}">
                <i class="fa-solid fa-barcode me-2"></i>
                <span class="sidebar-text">Quét Sản Phẩm</span>
            </a>
        @endcan
        @can('view barcodes')
            <a href="{{ route('production.list') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.list') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check me-2"></i>
                <span class="sidebar-text">Danh Sách Tem</span>
            </a>
        @endcan
        @role('manager|admin')
            @can('product manager')
                {{-- MỚI: Dropdown Quản lý Dữ liệu Sản xuất (Orders & products) --}}
                <div class="list-group-item py-3 ps-4 pe-2 d-flex justify-content-between align-items-center sidebar-dropdown-toggle"
                    data-bs-toggle="collapse" href="#productionConfigSubmenu" role="button"
                    aria-expanded="{{ request()->routeIs('manager.orders') || request()->routeIs('manager.products') ? 'true' : 'false' }}"
                    aria-controls="productionConfigSubmenu">
                    <div>
                        <i class="fa-solid fa-boxes-stacked me-2"></i> {{-- Icon kho hàng/dữ liệu --}}
                        <span class="sidebar-text">Dữ liệu Sản xuất</span>
                    </div>
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </div>

                <div class="collapse {{ request()->routeIs('manager.orders') || request()->routeIs('manager.products') ? 'show' : '' }}"
                    id="productionConfigSubmenu" data-bs-parent="#sidebar-wrapper">
                    {{-- Link Đơn Hàng --}}
                    <a href="{{ route('manager.orders') }}"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.orders') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice me-2"></i>
                        <span class="sidebar-text">Đơn hàng (PO)</span>
                    </a>

                    {{-- Link Sản Phẩm --}}
                    <a href="{{ route('manager.products') }}"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.products') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group me-2"></i>
                        <span class="sidebar-text"> Sản phẩm</span>
                    </a>
                    <a href="{{ route('manager.properties') }}"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.properties') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags me-2"></i>
                        <span class="sidebar-text">Thuộc Tính</span>
                    </a>
                    <a href="{{ route('manager.item-types') }}"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.item-types') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group me-2"></i>
                        <span class="sidebar-text">Loại Tem (Prefix)</span>
                    </a>
                    <a href="{{ route('manager.items') }}"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.items') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags me-2"></i>
                        <span class="sidebar-text">Code đã tạo (Items)</span>
                    </a>
                </div>
            @endcan
        @endrole
        {{-- 3. NHÓM QUẢN TRỊ (ADMIN ONLY) --}}
        @role('admin')
            {{-- MỚI: --}}
            <div class="sidebar-group-title px-4 mt-3 mb-1 text-muted small fw-bold text-uppercase">
                {{-- Quan trọng: Phải bọc trong span sidebar-text để hứng hiệu ứng CSS --}}
                <span class="sidebar-text">Quản Trị Hệ Thống</span>

                {{-- (Tùy chọn) Thêm 1 dấu gạch ngang nhỏ chỉ hiện khi thu nhỏ menu để đẹp hơn --}}
                <i class="fas fa-ellipsis-h d-none mini-icon-separator"></i>
            </div>


            {{-- Dropdown Quản lý Người dùng (Cũ) --}}
            <div class="list-group-item py-3 ps-4 pe-2 d-flex justify-content-between align-items-center sidebar-dropdown-toggle"
                data-bs-toggle="collapse" href="#userSubmenu" role="button"
                aria-expanded="{{ isset($menu) && str_contains($menu, 'admin') ? 'true' : 'false' }}"
                aria-controls="userSubmenu">

                <div>
                    <i class="fa-solid fa-user-shield me-2"></i>
                    <span class="sidebar-text">Phân Quyền User</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
            </div>

            <div class="collapse {{ isset($menu) && str_contains($menu, 'admin') ? 'show' : '' }}" id="userSubmenu"
                data-bs-parent="#sidebar-wrapper">
                <a href="{{ route('users.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && str_contains($menu, 'user') ? 'active' : '' }}">
                    <i class="fa-solid fa-users me-2"></i>
                    <span class="sidebar-text">Danh sách User</span>
                </a>

                <a href="{{ route('departments.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && str_contains($menu, 'departments') ? 'active' : '' }}">
                    <i class="fa-regular fa-building"></i>
                    <span class="sidebar-text">Phòng ban</span>
                </a>
                <a href="{{ route('roles.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && str_contains($menu, 'roles') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear me-2"></i>
                    <span class="sidebar-text">Vai trò</span>
                </a>
                <a href="{{ route('permissions.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && str_contains($menu, 'permissions') ? 'active' : '' }}">
                    <i class="fa-solid fa-key me-2"></i>
                    <span class="sidebar-text">Phân quyền</span>
                </a>
            </div>
        @endrole
    </div>
</div>
