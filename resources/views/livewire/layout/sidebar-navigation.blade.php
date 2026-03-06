{{-- sidebar-navigation.blade.php --}}
<div class="border-right" id="sidebar-wrapper">
    <div class="sidebar-heading d-flex justify-content-between align-items-center">
        <x-mh-logo-icon class="size-5 fill-current text-white dark:text-black" />

        {{-- Nút đóng sidebar cho mobile --}}
        <button class="btn btn-link d-block d-lg-none me-2" id="sidebarClose" aria-label="Close sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="list-group list-group-flush">
        {{-- 1. DASHBOARD --}}
        <a href="/dashboard" class="list-group-item list-group-item-action py-3 ps-4" title="Bảng điều khiển">
            <i class="fas fa-tachometer-alt me-2"></i>
            <span class="sidebar-text">Bảng điều khiển</span>
        </a>

        {{-- 2. NHÓM TÍNH NĂNG SẢN XUẤT (Production) --}}

        @can('print barcodes')
            <a href="{{ route('production.barcode-generator') }}" title="In Tem Mã Vạch"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.barcode-generator') ? 'active' : '' }}">
                <i class="fa-solid fa-print me-2"></i>
                <span class="sidebar-text">In Tem Mã Vạch</span>
            </a>

            {{-- MỚI: Menu Quản lý Excel (Export/Import số liệu) --}}
            <a href="{{ route('production.excel-manager') }}" title="Quản lý Excel"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.excel-manager') ? 'active' : '' }}">
                <i class="fa-solid fa-file-excel me-2"></i>
                <span class="sidebar-text">Nhập/Xuất Excel</span>
            </a>
        @endcan

        @can('products scan')
            <a href="{{ route('production.scan') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.scan') ? 'active' : '' }}" title="Quét Sản Phẩm">
                <i class="fa-solid fa-barcode me-2"></i>
                <span class="sidebar-text">Quét Sản Phẩm</span>
            </a>
        @endcan
        @can('view barcodes')
            <a href="{{ route('production.list') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.list') ? 'active' : '' }}" title="Danh Sách Tem">
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
                    aria-controls="productionConfigSubmenu" title="Quản lý Dữ liệu Sản xuất">
                    <div>
                        <i class="fa-solid fa-boxes-stacked me-2"></i> {{-- Icon kho hàng/dữ liệu --}}
                        <span class="sidebar-text" title="Dữ liệu Sản xuất">Dữ liệu Sản xuất</span>
                    </div>
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </div>

                <div class="collapse {{ request()->routeIs('manager.*') ? 'show' : '' }}" id="productionConfigSubmenu"
                    data-bs-parent="#sidebar-wrapper">
                    {{-- Link Đơn Hàng --}}
                    <a href="{{ route('manager.orders') }}" title="Đơn hàng (PO)"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.orders') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice me-2"></i>
                        <span class="sidebar-text">Đơn hàng (PO)</span>
                    </a>

                    {{-- Link Sản Phẩm --}}
                    <a href="{{ route('manager.products') }}" title="Sản phẩm"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.products') ? 'active' : '' }}">
                        <i class="fa-brands fa-product-hunt me-2"></i>
                        <span class="sidebar-text"> Sản phẩm</span>
                    </a>
                    <a href="{{ route('manager.properties') }}" title="Thuộc tính sản phẩm"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.properties') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags me-2"></i>
                        <span class="sidebar-text">Thuộc Tính</span>
                    </a>
                    <a href="{{ route('manager.item-types') }}" title="Loại tem (Prefix)"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.item-types') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group me-2"></i>
                        <span class="sidebar-text">Loại Tem (Prefix)</span>
                    </a>
                    <a href="{{ route('manager.items') }}" title="Code đã tạo (Items)"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.items') ? 'active' : '' }}">
                       <i class="fa-solid fa-code me-2"></i>
                        <span class="sidebar-text">Code đã tạo (Items)</span>
                    </a>
                    <a href="{{ route('manager.categories') }}" title="Danh mục sản phẩm"
                        class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ request()->routeIs('manager.categories') ? 'active' : '' }}">
                        <i class="fa-solid fa-list me-2"></i>
                        <span class="sidebar-text">Danh mục</span>
                    </a>
                </div>
            @endcan
        @endrole
        {{-- 3. NHÓM QUẢN TRỊ (ADMIN ONLY) --}}
        @role('admin')
            {{-- MỚI: --}}

            {{-- Dropdown Quản lý Người dùng (Cũ) --}}
            <div class="list-group-item py-3 ps-4 pe-2 d-flex justify-content-between align-items-center sidebar-dropdown-toggle"
                data-bs-toggle="collapse" href="#userSubmenu" role="button"
                aria-expanded="{{ isset($menu) && str_contains($menu, 'admin') ? 'true' : 'false' }}"
                aria-controls="userSubmenu" title="Quản lý Người dùng">
                <div>
                    <i class="fa-solid fa-user-shield me-2"></i>
                    <span class="sidebar-text" title="Quản lý Người dùng">Quản lý Người dùng</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
            </div>

            <div class="collapse {{ isset($menu) && str_contains($menu, 'admin') ? 'show' : '' }}" id="userSubmenu"
                data-bs-parent="#sidebar-wrapper">
                <a href="{{ route('users.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && str_contains($menu, 'user') ? 'active' : '' }}">
                    <i class="fa-solid fa-users me-2"></i>
                    <span class="sidebar-text" title="Danh sách User">Danh sách User</span>
                </a>

                <a href="{{ route('departments.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && str_contains($menu, 'departments') ? 'active' : '' }}">
                    <i class="fa-regular fa-building"></i>
                    <span class="sidebar-text" title="Phòng ban">Phòng ban</span>
                </a>
                <a href="{{ route('roles.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && str_contains($menu, 'roles') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear me-2"></i>
                    <span class="sidebar-text" title="Vai trò">Vai trò</span>
                </a>
                <a href="{{ route('permissions.index') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 bg-secondary {{ isset($menu) && str_contains($menu, 'permissions') ? 'active' : '' }}">
                    <i class="fa-solid fa-key me-2"></i>
                    <span class="sidebar-text" title="Phân quyền">Phân quyền</span>
                </a>
            </div>
        @endrole
    </div>
</div>
