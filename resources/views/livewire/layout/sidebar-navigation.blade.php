{{-- sidebar-navigation.blade.php --}}
<div class="border-right" id="sidebar-wrapper">
    {{-- Thêm position-relative và justify-content-center vào thẻ cha --}}
    <div class="sidebar-heading position-relative d-flex d-lg-flex justify-content-lg-center align-items-center">

        {{-- Logo lúc này sẽ tự động nằm ngay chính giữa --}}
        <div style="width: 100px;">
            <a href="/dashboard" class="text-reset text-decoration-none">
                <x-mh-logo-icon class="fill-current d-lg-w-100 w-50 h-auto" /></a>
        </div>

        {{-- Nút đóng sidebar cho mobile được neo tuyệt đối (absolute) sang bên phải --}}
        <button
            class="btn btn-link sidebar-close-btn d-block d-lg-none position-absolute end-0 top-50 translate-middle-y me-2"
            aria-label="Close sidebar">
            <i class="fas fa-times"></i> Close
        </button>
    </div>
    <div class="list-group list-group-flush">
        {{-- 1. DASHBOARD --}}
        <a href="/dashboard"
            class="list-group-item list-group-item-action py-3 ps-4 {{ request()->is('dashboard') ? 'active' : '' }}"
            title="Bảng điều khiển">
            <i class="fas fa-fw fa-tachometer-alt text-primary me-2"></i>
            <span class="sidebar-text">Bảng điều khiển</span>
        </a>

        {{-- 2. BIỂU ĐỒ & BÁO CÁO (tất cả user) --}}
        <a href="{{ route('analytics') }}"
            class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('analytics') ? 'active' : '' }}"
            title="Biểu Đồ & Báo Cáo">
            <i class="fa-solid fa-fw fa-chart-line text-warning me-2"></i>
            <span class="sidebar-text">Biểu Đồ &amp; Báo Cáo</span>
        </a>
        @can('items.view')
            <a href="{{ route('items') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('items') ? 'active' : '' }}"
                title="Kho Mã Tem">
                <i class="fa-solid fa-fw fa-tags text-dark me-2"></i>
                <span class="sidebar-text">Mã Tem (Items)</span>
            </a>
        @endcan
        {{-- 2. NHÓM TÍNH NĂNG SẢN XUẤT (Production) --}}
        @can('products')
            @php
                $fabricMenuActive =
                    request()->routeIs('items') ||
                    request()->routeIs('production.barcode-generator-excel') ||
                    request()->routeIs('production.excel-manager') ||
                    request()->routeIs('production.scan');
            @endphp
            <div class="list-group-item py-3 ps-4 pe-2 d-flex justify-content-between align-items-center sidebar-dropdown-toggle"
                data-bs-toggle="collapse" href="#productsSubmenu" role="button"
                aria-expanded="{{ $fabricMenuActive ? 'true' : 'false' }}" aria-controls="productsSubmenu"
                title="Quản lý Vải">
                <div>
                    <i class="fa-solid fa-fw fa-industry text-primary me-2"></i> {{-- Icon sản xuất --}}
                    <span class="sidebar-text" title="Vải">Vải</span>
                </div>
                <i class="fas fa-fw fa-chevron-down sidebar-arrow"></i>
            </div>
            <div class="collapse {{ $fabricMenuActive ? 'show' : '' }}" id="productsSubmenu"
                data-bs-parent="#sidebar-wrapper">
                <a href="{{ route('production.barcode-generator-excel') }}" title="In Tem Excel"
                    class="list-group-item list-group-item-action  py-2 ps-5  {{ request()->routeIs('production.barcode-generator-excel') ? 'active' : '' }}">
                    <i class="fa-solid fa-fw fa-print text-info me-2"></i>
                    <span class="sidebar-text">In Tem Cây Vải</span>
                </a>
                <a href="{{ route('items') }}" title="Danh sách Vải"
                    class="list-group-item list-group-item-action py-2 ps-5  {{ request()->routeIs('items') ? 'active' : '' }}">
                    <i class="fa-solid fa-fw fa-list text-success me-2"></i>
                    <span class="sidebar-text">Danh sách Vải</span>
                </a>
                {{-- MỚI: Menu Quản lý Excel (Export/Import số liệu) --}}
                <a href="{{ route('production.excel-manager') }}" title="Quản lý Excel"
                    class="list-group-item list-group-item-action py-2 ps-5  {{ request()->routeIs('production.excel-manager') ? 'active' : '' }}">
                    <i class="fa-solid fa-fw fa-file-excel text-success me-2"></i>
                    <span class="sidebar-text">Cập nhật Vải(Excel)</span>
                </a>

                <a href="{{ route('production.scan') }}"
                    class="list-group-item list-group-item-action py-2 ps-5  {{ request()->routeIs('production.scan') ? 'active' : '' }}"
                    title="Xác nhận">
                    <i class="fa-solid fa-fw fa-barcode text-primary me-2"></i>
                    <span class="sidebar-text">Xác nhận</span>
                </a>
            </div>
        @endcan
        @can('coating.scan')
            <a href="{{ route('production.coating-confirmation') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('production.coating-confirmation') ? 'active' : '' }}"
                title="Xác Nhận Tráng">
                <i class="fa-solid fa-fw fa-barcode text-warning me-2"></i>
                <span class="sidebar-text">Xác Nhận Tráng</span>
            </a>
        @endcan
        {{-- MỚI THÊM: Kho mã Code/Tem (Sử dụng chung cho các bộ phận) --}}

        @can('warehouse.scan')
            <div class="list-group-item py-3 ps-4 pe-2 d-flex justify-content-between align-items-center sidebar-dropdown-toggle"
                data-bs-toggle="collapse" href="#warehouseSubmenu" role="button"
                aria-expanded="{{ request()->routeIs('warehouse.*') ? 'true' : 'false' }}" aria-controls="warehouseSubmenu"
                title="Quản lý Kho">
                <div>
                    <i class="fa-solid fa-fw fa-warehouse text-primary me-2"></i> {{-- Icon kho hàng --}}
                    <span class="sidebar-text" title="Kho">Kho</span>
                </div>
                <i class="fas fa-fw fa-chevron-down sidebar-arrow"></i>
            </div>

            <div class="collapse {{ request()->routeIs('warehouse.*') ? 'show' : '' }}" id="warehouseSubmenu"
                data-bs-parent="#sidebar-wrapper">
                <a href="{{ route('warehouse.dashboard') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('warehouse.dashboard') ? 'active' : '' }}"
                    title="Bảng Điều Khiển">
                    <i class="fa-solid fa-fw fa-gauge-high text-warning me-2"></i>
                    <span class="sidebar-text">Bảng Điều Khiển</span>
                </a>

                <a href="{{ route('warehouse.scan-to-location') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('warehouse.scan-to-location') ? 'active' : '' }}"
                    title="Nhập Kho">
                    <i class="fa-solid fa-fw fa-barcode text-success me-2"></i>
                    <span class="sidebar-text">Nhập Kho</span>
                </a>
                <a href="{{ route('warehouse.scan-to-location-classic') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('warehouse.scan-to-location-classic') ? 'active' : '' }}"
                    title="Nhập Kho">
                    <i class="fa-solid fa-fw fa-barcode text-info me-2"></i>
                    <span class="sidebar-text">Nhập Kho (Classic)</span>
                </a>

                <a href="{{ route('warehouse.inbound-list') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('warehouse.inbound-list') ? 'active' : '' }}"
                    title="Danh Sách Nhập Kho">
                    <i class="fa-solid fa-fw fa-list-check text-primary me-2"></i>
                    <span class="sidebar-text">Danh Sách Nhập Kho</span>
                </a>

                <a href="{{ route('warehouse.locations') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('warehouse.locations') ? 'active' : '' }}"
                    title="Quản lý Vị trí">
                    <i class="fa-solid fa-fw fa-location-dot text-danger me-2"></i>
                    <span class="sidebar-text">Quản lý Vị trí</span>
                </a>

                <a href="{{ route('warehouse.movement-log') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('warehouse.movement-log') ? 'active' : '' }}"
                    title="Nhật Ký Kho">
                    <i class="fa-solid fa-fw fa-clock-rotate-left text-secondary me-2"></i>
                    <span class="sidebar-text">Nhật Ký Kho</span>
                </a>

                <a href="{{ route('warehouse.reports') }}"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('warehouse.reports') ? 'active' : '' }}"
                    title="Xuất báo cáo kho">
                    <i class="fa-solid fa-fw fa-file-excel text-success me-2"></i>
                    <span class="sidebar-text">Xuất báo cáo kho</span>
                </a>
            </div>
        @endcan
        @role('manager|admin')
            @can('manager')
                {{-- MỚI: Dropdown Quản lý Dữ liệu Sản xuất (Orders & products) --}}
                <div class="list-group-item py-3 ps-4 pe-2 d-flex justify-content-between align-items-center sidebar-dropdown-toggle"
                    data-bs-toggle="collapse" href="#productionConfigSubmenu" role="button"
                    aria-expanded="{{ request()->routeIs('manager.orders') || request()->routeIs('manager.products') ? 'true' : 'false' }}"
                    aria-controls="productionConfigSubmenu" title="Quản lý Dữ liệu Sản xuất">
                    <div>
                        <i class="fa-solid fa-fw fa-boxes-stacked text-primary me-2"></i> {{-- Icon kho hàng/dữ liệu --}}
                        <span class="sidebar-text" title="Dữ liệu Sản xuất">Cài đặt Sản xuất</span>
                    </div>
                    <i class="fas fa-fw fa-chevron-down sidebar-arrow"></i>
                </div>

                <div class="collapse {{ request()->routeIs('manager.*') ? 'show' : '' }}" id="productionConfigSubmenu"
                    data-bs-parent="#sidebar-wrapper">
                    {{-- Link Lệnh Sản Xuất --}}
                    <a href="{{ route('manager.production-orders') }}" title="Lệnh Sản Xuất (LSX)"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.production-orders') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-boxes-packing text-warning me-2"></i>
                        <span class="sidebar-text">Lệnh Sản Xuất (LSX)</span>
                    </a>

                    {{-- Link Đơn Hàng --}}
                    {{-- 🌟 THÊM d-flex align-items-center --}}
                    <a href="{{ route('manager.orders') }}" title="Đơn hàng (PO)"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.orders') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-file-invoice text-info me-2"></i> {{-- 🌟 THÊM fa-fw --}}
                        <span class="sidebar-text">Đơn hàng (PO)</span>
                    </a>

                    {{-- Link Sản Phẩm --}}
                    <a href="{{ route('manager.products') }}" title="Sản phẩm"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.products') ? 'active' : '' }}">
                        <i class="fa-brands fa-fw fa-product-hunt text-primary me-2"></i>
                        <span class="sidebar-text">Sản phẩm</span>
                    </a>
                    <a href="{{ route('manager.item-types') }}" title="Loại tem (Prefix)"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.item-types') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-layer-group text-secondary me-2"></i>
                        <span class="sidebar-text">Loại Tem (Prefix)</span>
                    </a>
                    <a href="{{ route('manager.categories') }}" title="Danh mục sản phẩm"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.categories') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-list text-success me-2"></i>
                        <span class="sidebar-text">Danh mục</span>
                    </a>
                    <a href="{{ route('manager.properties') }}" title="Thuộc tính sản phẩm"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.properties') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-tags text-warning me-2"></i>
                        <span class="sidebar-text">Thuộc Tính </span>
                    </a>

                    <a href="{{ route('manager.machines') }}" title="Quản lý Máy Móc"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.machines') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-gears text-secondary me-2"></i>
                        <span class="sidebar-text">Máy Móc</span>
                    </a>
                    <a href="{{ route('manager.user-machines') }}" title="Phân Công Máy"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.user-machines') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-user-gear text-primary me-2"></i>
                        <span class="sidebar-text">Phân Công Máy</span>
                    </a>
                    <a href="{{ route('manager.print-stations') }}" title="Trạm In"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.print-stations') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-print text-info me-2"></i>
                        <span class="sidebar-text">Trạm In</span>
                    </a>
                    <a href="{{ route('manager.user-print-stations') }}" title="Phân Công Trạm In"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.user-print-stations') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-user-tag text-success me-2"></i>
                        <span class="sidebar-text">Phân Công Trạm In</span>
                    </a>
                    <a href="{{ route('manager.scale-stations') }}" title="Trạm Cân"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.scale-stations') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-weight-scale text-warning me-2"></i>
                        <span class="sidebar-text">Trạm Cân</span>
                    </a>
                    <a href="{{ route('manager.user-scale-stations') }}" title="Phân Công Trạm Cân"
                        class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ request()->routeIs('manager.user-scale-stations') ? 'active' : '' }}">
                        <i class="fa-solid fa-fw fa-user-check text-info me-2"></i>
                        <span class="sidebar-text">Phân Công Trạm Cân</span>
                    </a>
                </div>
            @endcan
        @endrole
        {{-- AI CHAT - Tất cả user --}}
        @role('manager|admin')
            <a href="{{ route('ai.chat') }}"
                class="list-group-item list-group-item-action py-3 ps-4 {{ request()->routeIs('ai.chat') ? 'active' : '' }}"
                title="Trợ lý AI">
                <i class="fa-solid fa-fw fa-robot text-info me-2"></i>
                <span class="sidebar-text">Trợ lý AI</span>
            </a>
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
                    <i class="fa-solid fa-fw fa-user-shield text-danger me-2"></i>
                    <span class="sidebar-text" title="Quản lý Người dùng">Quản lý Người dùng</span>
                </div>
                <i class="fas fa-fw fa-chevron-down sidebar-arrow"></i>
            </div>

            <div class="collapse {{ isset($menu) && str_contains($menu, 'admin') ? 'show' : '' }}" id="userSubmenu"
                data-bs-parent="#sidebar-wrapper">
                <a href="{{ route('users.index') }}" title="Danh sách User"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ isset($menu) && str_contains($menu, 'user') ? 'active' : '' }}">
                    <i class="fa-solid fa-fw fa-users text-primary me-2"></i>
                    <span class="sidebar-text">Danh sách User</span>
                </a>

                <a href="{{ route('departments.index') }}" title="Bộ phận"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ isset($menu) && str_contains($menu, 'departments') ? 'active' : '' }}">
                    <i class="fa-regular fa-fw fa-building text-info me-2"></i> {{-- 🌟 Sửa lại icon này --}}
                    <span class="sidebar-text">Bộ phận</span>
                </a>
                <a href="{{ route('roles.index') }}" title="Vai trò"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ isset($menu) && str_contains($menu, 'roles') ? 'active' : '' }}">
                    <i class="fa-solid fa-fw fa-users-gear text-warning me-2"></i>
                    <span class="sidebar-text">Vai trò</span>
                </a>
                <a href="{{ route('permissions.index') }}" title="Phân quyền"
                    class="list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center {{ isset($menu) && str_contains($menu, 'permissions') ? 'active' : '' }}">
                    <i class="fa-solid fa-fw fa-key text-danger me-2"></i>
                    <span class="sidebar-text">Phân quyền</span>
                </a>
            </div>
        @endrole
    </div>
    <a href="#"
        class="sidebar-close-btn list-group-item list-group-item-action py-2 ps-5 d-flex align-items-center d-lg-none">
        <i class="fas fa-fw fa-times text-danger me-2"></i>
        <span class="sidebar-text">Đóng Menu</span>
    </a>
</div>
