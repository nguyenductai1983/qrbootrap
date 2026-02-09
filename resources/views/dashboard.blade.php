<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @cannot('view users')
                        <p>{{ __('Nếu bạn chưa thấy chức năng nào, vui lòng liên hệ với quản trị viên.') }}</p>
                        <p>{{ __('Sau khi quản trị viên cài đặt vui lòng nhấn F5 để làm mới trang.') }}</p>
                    @endcannot
                    <div class="row mb-2">
                        @can('view users')
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Theo dõi mã QR</h5>
                                        <p class="card-text">Chức năng này cho phép bạn theo dõi và quản lý các mã QR.</p>
                                        <a href="{{ route('qrcodes.index') }}" class="btn btn-primary">
                                            <i class="fa-solid fa-qrcode"></i>
                                            <span class="sidebar-text">Danh sách mã QR</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @can('view users')
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Quản lý người dùng</h5>
                                        <p class="card-text">Chức năng này cho phép bạn quản lý người dùng trong hệ thống.
                                        </p>
                                        <a href="{{ route('users.index') }}" class="btn btn-primary">
                                            <i class="fa-solid fa-user"></i> Xem danh sách người dùng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                     <div class="row">
                        @can('view departments')
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Quản lý phòng ban</h5>
                                        <p class="card-text">Chức năng này cho phép bạn theo dõi và quản lý các phòng ban trong hệ thống.</p>
                                        <a href="{{ route('departments.index') }}" class="btn btn-primary">
                                            <i class="fa-solid fa-building"></i>
                                            <span class="sidebar-text">Danh sách phòng ban</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endcan

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
