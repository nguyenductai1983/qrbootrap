<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tachometer-alt me-2 text-primary"></i> {{ __('Bảng điều khiển') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. THÔNG BÁO NẾU KHÔNG CÓ QUYỀN GÌ --}}
            <div class="mb-4">
                @if (Auth::user()->roles->isEmpty() && Auth::user()->permissions->isEmpty())
                    <div class="alert alert-warning shadow-sm border-0">
                        <h5 class="fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Tài khoản chưa được phân
                            quyền!</h5>
                        <p class="mb-0">Vui lòng liên hệ quản trị viên để được cấp quyền truy cập các chức năng.</p>
                    </div>
                @endif
            </div>

            {{-- 2. BANNER HƯỚNG DẪN SỬ DỤNG (MỚI THÊM) --}}
            {{-- Thiết kế dạng banner ngang, màu xanh gradient nổi bật --}}
            <div class="card shadow-sm border-0 bg-primary bg-gradient text-white mb-5">
                <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="mb-3 mb-md-0">
                        <h4 class="fw-bold"><i class="fa-solid fa-book-open me-2"></i> Hướng Dẫn Sử Dụng Hệ Thống</h4>
                        <p class="mb-0 text-white-50">Xem tài liệu chi tiết cách in tem, nhập liệu Excel và quy trình
                            quét mã.</p>
                    </div>
                    <a href="{{ route('guide') }}"
                        class="btn btn-light text-primary fw-bold shadow-sm px-4 py-2 hover-scale">
                        <i class="fa-regular fa-circle-question me-2"></i> Xem Hướng Dẫn
                    </a>
                </div>
            </div>

            {{-- 3. KHU VỰC TÁC VỤ SẢN XUẤT --}}
            @if (Auth::user()->can('print barcodes') || Auth::user()->can('scan products') || Auth::user()->can('view barcodes'))
                <h4 class="fw-bold text-secondary mb-3 border-start border-4 border-primary ps-3">
                    Tác Vụ Sản Xuất
                </h4>
                <div class="row g-4 mb-5">

                    {{-- Chức năng: IN TEM --}}
                    @can('print barcodes')
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm border-0 hover-card">
                                <div class="card-body d-flex flex-column text-center p-4">
                                    <div class="mb-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3">
                                            <i class="fa-solid fa-print fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title fw-bold">In Tem Mã Vạch</h5>
                                    <p class="card-text text-muted small mb-4">Tạo và in tem QR/Barcode cho sản phẩm mới.
                                    </p>

                                    <a href="{{ route('production.barcode-generator') }}"
                                        class="btn btn-outline-primary w-100 mt-auto stretched-link">
                                        Truy cập <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Chức năng: EXCEL --}}
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm border-0 hover-card">
                                <div class="card-body d-flex flex-column text-center p-4">
                                    <div class="mb-3">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-3">
                                            <i class="fa-solid fa-file-excel fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title fw-bold">Nhập/Xuất Excel</h5>
                                    <p class="card-text text-muted small mb-4">Cập nhật dữ liệu hàng loạt từ file Excel.</p>

                                    <a href="{{ route('production.excel-manager') }}"
                                        class="btn btn-outline-success w-100 mt-auto stretched-link">
                                        Truy cập <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan

                    {{-- Chức năng: QUÉT TEM --}}
                    @can('products scan')
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm border-0 hover-card">
                                <div class="card-body d-flex flex-column text-center p-4">
                                    <div class="mb-3">
                                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3">
                                            <i class="fa-solid fa-barcode fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title fw-bold">Quét Sản Phẩm</h5>
                                    <p class="card-text text-muted small mb-4">Kiểm tra thông tin và truy xuất nguồn gốc.
                                    </p>

                                    <a href="{{ route('production.scan') }}"
                                        class="btn btn-outline-warning  w-100 mt-auto stretched-link">
                                        Truy cập <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan

                    {{-- Chức năng: DANH SÁCH TEM --}}
                    @can('view barcodes')
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm border-0 hover-card">
                                <div class="card-body d-flex flex-column text-center p-4">
                                    <div class="mb-3">
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex p-3">
                                            <i class="fa-solid fa-list-check fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title fw-bold">Danh Sách Tem</h5>
                                    <p class="card-text text-muted small mb-4">Xem lịch sử và quản lý danh sách tem đã tạo.
                                    </p>

                                    <a href="{{ route('production.list') }}"
                                        class="btn btn-outline-info text-dark w-100 mt-auto stretched-link">
                                        Truy cập <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            @endif

            {{-- 4. KHU VỰC QUẢN TRỊ HỆ THỐNG --}}
            @role('manager|admin')
                <h4 class="fw-bold text-secondary mb-3 border-start border-4 border-danger ps-3">
                    Quản Trị Hệ Thống
                </h4>

                {{-- Nhóm Dữ liệu --}}
                <h6 class="text-muted fw-bold text-uppercase small mb-3 mt-4"><i class="fa-solid fa-database me-1"></i> Cấu
                    hình Dữ liệu</h6>
                <div class="row g-4 mb-4">
                    {{-- Đơn Hàng --}}
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded me-3">
                                    <i class="fa-solid fa-file-invoice fa-xl"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">Đơn Hàng (PO)</h6>
                                    <small class="text-muted">Quản lý PO nhập khẩu</small>
                                </div>
                                <a href="{{ route('manager.orders') }}" class="stretched-link text-secondary"><i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Model Sản Phẩm --}}
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded me-3">
                                    <i class="fa-solid fa-layer-group fa-xl"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">Sản Phẩm</h6>
                                    <small class="text-muted">Danh mục sản phẩm</small>
                                </div>
                                <a href="{{ route('manager.products') }}" class="stretched-link text-secondary"><i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded me-3">
                                    <i class="fa-solid fa-tags fa-xl"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">Loại Sản Phẩm</h6>
                                    <small class="text-muted">Danh mục loại sản phẩm</small>
                                </div>
                                <a href="{{ route('manager.item-types') }}" class="stretched-link text-secondary">
                                    <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded me-3">
                                    <i class="fa-solid fa-layer-group fa-xl"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">Thuộc Tính</h6>
                                    <small class="text-muted">Danh mục thuộc tính sản phẩm</small>
                                </div>
                                <a href="{{ route('manager.properties') }}" class="stretched-link text-secondary"><i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endrole
            @role('admin')
                {{-- Nhóm Phân Quyền --}}
                <h6 class="text-muted fw-bold text-uppercase small mb-3"><i class="fa-solid fa-user-shield me-1"></i> Phân
                    Quyền & Người Dùng</h6>
                <div class="row g-4">
                    {{-- User --}}
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded me-3">
                                    <i class="fa-solid fa-users fa-xl"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">Người Dùng</h6>
                                    <small class="text-muted">Tài khoản & Password</small>
                                </div>
                                <a href="{{ route('users.index') }}" class="stretched-link text-danger"><i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Phòng Ban --}}
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded me-3">
                                    <i class="fa-regular fa-building fa-xl"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">Phòng Ban</h6>
                                    <small class="text-muted">Cơ cấu tổ chức</small>
                                </div>
                                <a href="{{ route('departments.index') }}" class="stretched-link text-danger"><i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Vai Trò --}}
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded me-3">
                                    <i class="fa-solid fa-users-gear fa-xl"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">Vai Trò (Roles)</h6>
                                    <small class="text-muted">Định nghĩa vai trò</small>
                                </div>
                                <a href="{{ route('roles.index') }}" class="stretched-link text-danger"><i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Quyền Hạn --}}
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded me-3">
                                    <i class="fa-solid fa-key fa-xl"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">Quyền Hạn</h6>
                                    <small class="text-muted">Chi tiết quyền</small>
                                </div>
                                <a href="{{ route('permissions.index') }}" class="stretched-link text-danger"><i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endrole

        </div>
    </div>

    <style>
        .hover-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        /* Hiệu ứng cho nút Hướng dẫn */
        .hover-scale {
            transition: transform 0.2s;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }
    </style>
</x-app-layout>
