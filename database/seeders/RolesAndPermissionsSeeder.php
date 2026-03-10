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
        Permission::firstOrCreate(['name' => 'users view']);
        Permission::firstOrCreate(['name' => 'users create']);
        Permission::firstOrCreate(['name' => 'users edit']);
        Permission::firstOrCreate(['name' => 'users delete']);
        Permission::firstOrCreate(['name' => 'users assign roles']); // Quyền mới: gán vai trò cho người dùng

        // Quyền quản lý phòng ban
        Permission::firstOrCreate(['name' => 'departments view']);
        Permission::firstOrCreate(['name' => 'departments create']);
        Permission::firstOrCreate(['name' => 'departments edit']);
        Permission::firstOrCreate(['name' => 'departments delete']);

        // Quyền quản lý vai trò
        Permission::firstOrCreate(['name' => 'roles view']);
        Permission::firstOrCreate(['name' => 'roles create']);
        Permission::firstOrCreate(['name' => 'roles edit']);
        Permission::firstOrCreate(['name' => 'roles delete']);
        Permission::firstOrCreate(['name' => 'roles assign permissions']); // Quyền mới: gán quyền cho vai trò

        // Quyền quản lý quyền hạn (ít dùng trực tiếp)
        Permission::firstOrCreate(['name' => 'permissions view']);
        Permission::firstOrCreate(['name' => 'permissions create']);
        Permission::firstOrCreate(['name' => 'permissions edit']);
        Permission::firstOrCreate(['name' => 'permissions delete']);
        Permission::firstOrCreate(['name' => 'print barcodes']); // Quyền mới: in mã vạch
        Permission::firstOrCreate(['name' => 'products scan']); // Quyền mới: quét sản phẩm
        Permission::firstOrCreate(['name' => 'product manager']); // Quyền mới: quản lý sản phẩm
        // 2. Tạo Roles và gán Permissions
        // Vai trò Admin: Có tất cả các quyền
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $adminRole->givePermissionTo(Permission::all()); // Gán tất cả quyền cho admin
        $managerRole->givePermissionTo([
            'departments view',
            'departments create',
            'departments edit',
            'departments delete',
            'print barcodes', // Quyền mới: in mã vạch
            'products scan',
            'product manager',
        ]);
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
