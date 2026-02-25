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
        Permission::firstOrCreate(['name' => 'view users']);
        Permission::firstOrCreate(['name' => 'create users']);
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'delete users']);
        Permission::firstOrCreate(['name' => 'assign roles to users']); // Quyền mới: gán vai trò cho người dùng

        // Quyền quản lý phòng ban
        Permission::firstOrCreate(['name' => 'view departments']);
        Permission::firstOrCreate(['name' => 'create departments']);
        Permission::firstOrCreate(['name' => 'edit departments']);
        Permission::firstOrCreate(['name' => 'delete departments']);

        // Quyền quản lý vai trò
        Permission::firstOrCreate(['name' => 'view roles']);
        Permission::firstOrCreate(['name' => 'create roles']);
        Permission::firstOrCreate(['name' => 'edit roles']);
        Permission::firstOrCreate(['name' => 'delete roles']);
        Permission::firstOrCreate(['name' => 'assign permissions to roles']); // Quyền mới: gán quyền cho vai trò

        // Quyền quản lý quyền hạn (ít dùng trực tiếp)
        Permission::firstOrCreate(['name' => 'view permissions']);
        Permission::firstOrCreate(['name' => 'create permissions']);
        Permission::firstOrCreate(['name' => 'edit permissions']);
        Permission::firstOrCreate(['name' => 'delete permissions']);
        Permission::firstOrCreate(['name' => 'print barcodes']); // Quyền mới: in mã vạch
        Permission::firstOrCreate(['name' => 'scan products']);
        // 2. Tạo Roles và gán Permissions
        // Vai trò Admin: Có tất cả các quyền
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all()); // Gán tất cả quyền cho admin
        // 3. Gán Role cho một người dùng cụ thể (ví dụ: người dùng đầu tiên)
        $user = User::first(); // Lấy người dùng đầu tiên
        if ($user) {
            $user->assignRole('admin'); // Gán vai trò 'admin' cho người dùng này
        }
        $user = User::firstOrCreate(
            ['email' => 'admin@qrcode.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'), // Đặt mật khẩu
                // 'department_id' => 1, // Gán vào phòng IT (ID 1) nếu muốn
            ]
        );

        // Gán quyền Admin cho user này
        $user->assignRole('admin');
    }
}
