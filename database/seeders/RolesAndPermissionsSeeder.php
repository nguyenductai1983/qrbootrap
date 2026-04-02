<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Import User model
use Illuminate\Support\Facades\Hash; // Thêm dòng này để mã hóa mật khẩu
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Tạo Permissions
        // Quyền quản lý người dùng
        Permission::firstOrCreate(['name' => 'users.view']);
        Permission::firstOrCreate(['name' => 'users.create']);
        Permission::firstOrCreate(['name' => 'users.edit']);
        Permission::firstOrCreate(['name' => 'users.delete']);
        Permission::firstOrCreate(['name' => 'users.assign_roles']);

        // Quyền quản lý phòng ban
        Permission::firstOrCreate(['name' => 'departments.view']);
        Permission::firstOrCreate(['name' => 'departments.create']);
        Permission::firstOrCreate(['name' => 'departments.edit']);
        Permission::firstOrCreate(['name' => 'departments.delete']);

        // Quyền quản lý vai trò
        Permission::firstOrCreate(['name' => 'roles.view']);
        Permission::firstOrCreate(['name' => 'roles.create']);
        Permission::firstOrCreate(['name' => 'roles.edit']);
        Permission::firstOrCreate(['name' => 'roles.delete']);
        Permission::firstOrCreate(['name' => 'roles.assign_permissions']);

        // Quyền quản lý quyền hạn
        Permission::firstOrCreate(['name' => 'permissions.view']);
        Permission::firstOrCreate(['name' => 'permissions.create']);
        Permission::firstOrCreate(['name' => 'permissions.edit']);
        Permission::firstOrCreate(['name' => 'permissions.delete']);

        // Quyền truy cập Manager (menu quản lý)
        Permission::firstOrCreate(['name' => 'manager']);

        // Quyền quản lý Tem/Item
        Permission::firstOrCreate(['name' => 'items.view']);    // Xem danh sách tem
        Permission::firstOrCreate(['name' => 'view_all_departments']);// Xem toàn bộ dữ liệu (Tem, Dashboard) của tất cả bộ phận
        Permission::firstOrCreate(['name' => 'items.create']);  // Tạo tem mới
        Permission::firstOrCreate(['name' => 'items.edit']);    // Sửa tem
        Permission::firstOrCreate(['name' => 'items.delete']);  // Xóa tem

        // Quyền sản xuất       
        Permission::firstOrCreate(['name' => 'products.print']); // Truy cập trang in tem (route middleware)
        Permission::firstOrCreate(['name' => 'products.scan']);  // Quét sản phẩm
        Permission::firstOrCreate(['name' => 'coating.scan']);   // Xác nhận tráng

        // Quyền kho
        Permission::firstOrCreate(['name' => 'warehouse.scan']);      // Nhập kho / quét vị trí
        Permission::firstOrCreate(['name' => 'warehouse.location']); // Quản lý vị trí kho
        Permission::firstOrCreate(['name' => 'warehouse.report']); // Quản lý vị trí kho
        // Quyền báo cáo / phân tích (Dashboard)
        // Permission::firstOrCreate(['name' => 'analytics.view_all']);  // Đã gộp vào view_all_departments

        // 2. Tạo Roles và gán Permissions
        $adminRole    = Role::firstOrCreate(['name' => 'admin']);
        $managerRole  = Role::firstOrCreate(['name' => 'manager']);
        $productsRole = Role::firstOrCreate(['name' => 'products']);
        $warehouseRole = Role::firstOrCreate(['name' => 'warehouse']);
        $CoatingRole  = Role::firstOrCreate(['name' => 'coating']);

        // Admin: tất cả quyền
        $adminRole->syncPermissions(Permission::all());

        // Manager: quản lý dữ liệu, máy móc, trạm in, kho
        $managerRole->syncPermissions([
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'manager',
            'products.print',
            'products.scan',
            'coating.scan',
            'items.view',
            'view_all_departments',
            'items.create',
            'items.edit',
            'items.delete',
            'warehouse.scan',
            'warehouse.location',
            'warehouse.report',
        ]);

        // Products: in tem, quét, xem danh sách
        $productsRole->syncPermissions([
            'products.print',
            'products.scan',
            'items.view',
        ]);

        // Warehouse: nhập kho, xem vị trí, xem báo cáo
        $warehouseRole->syncPermissions([
            'warehouse.scan',
            'warehouse.location',
            'warehouse.report',
            'items.view',
            'view_all_departments',
        ]);

        // Coating: xác nhận tráng
        $CoatingRole->syncPermissions([
            'coating.scan',
            'items.view',
        ]);
        // 3. Gán Role cho một người dùng cụ thể (ví dụ: người dùng đầu tiên)       
        $user = User::firstOrCreate(
            ['email' => 'admin@qrcode.com'],
            [
                'username' => 'admin',
                'name' => 'Admin',
                'password' => Hash::make('12345678'), // Đặt mật khẩu
                'department_id' => 1, // Gán vào phòng IT (ID 1) nếu muốn
            ]
        );
        $user = User::first(); // Lấy người dùng đầu tiên
        if ($user) {
            $user->assignRole('admin'); // Gán vai trò 'admin' cho người dùng này
        }
        // Gán quyền Admin cho user này 1        
        $user = User::firstOrCreate(
            ['email' => 'tien@qrcode.com'],
            [
                'username' => 'tien',
                'name' => 'tien',
                'password' => Hash::make('12345678'), // Đặt mật khẩu
                'department_id' => 1, // Gán vào phòng IT (ID 1) nếu muốn
            ]
        );
        // Gán quyền Admin cho user này 1
        $user->assignRole('admin');
        $user = User::firstOrCreate(
            ['email' => 'khang@qrcode.com'],
            [
                'username' => 'khang',
                'name' => 'khang',
                'password' => Hash::make('12345678'), // Đặt mật khẩu
                'department_id' => 1, // Gán vào phòng IT (ID 1) nếu muốn
            ]
        );
        // Gán quyền quan ly cho user này 2
        $user->assignRole('manager');
        $user = User::firstOrCreate(
            ['email' => 'vai@qrcode.com'],
            [
                'username' => 'vai',
                'name' => 'vai',
                'password' => Hash::make('12345678'), // Đặt mật khẩu
                'department_id' => 2, // Gán vào phòng IT (ID 1) nếu muốn
            ]
        );
        // Gán quyền kho cho user này 3
        $user->assignRole('warehouse');
        $user = User::firstOrCreate(
            ['email' => 'kho@qrcode.com'],
            [
                'username' => 'kho',
                'name' => 'kho',
                'password' => Hash::make('12345678'), // Đặt mật khẩu
                'department_id' => 7, // Gán vào phòng IT (ID 1) nếu muốn
            ]
        );
        // Gán quyền kho cho user này 4
        $user->assignRole('coating');
        $user = User::firstOrCreate(
            ['email' => 'trang@qrcode.com'],
            [
                'username' => 'trang',
                'name' => 'trang',
                'password' => Hash::make('12345678'), // Đặt mật khẩu
                'department_id' => 4, // Gán vào phòng IT (ID 1) nếu muốn
            ]
        );
        // Gán quyền coating cho user này
        $user->assignRole('coating');
    }
}
