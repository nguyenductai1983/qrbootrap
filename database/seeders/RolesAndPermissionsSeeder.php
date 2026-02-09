<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Import User model

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

        // 2. Tạo Roles và gán Permissions
        // Vai trò Admin: Có tất cả các quyền
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all()); // Gán tất cả quyền cho admin

        // Vai trò Editor: Có thể xem, tạo, sửa người dùng/phòng ban nhưng không xóa
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo([
           'view users',
           'create users',
           'edit users',
           'delete users',
           'assign roles to users',
           'view departments',
           'create departments',
           'edit departments',
           'delete departments',
           'view roles',
           'create roles',
           'edit roles',
           'delete roles',
           'assign permissions to roles',
           'view permissions',
           'create permissions',
           'edit permissions',
           'delete permissions',
           'print barcodes',
           'scan products',
           'view barcodes',
        ]);

        // Vai trò Viewer: Chỉ có thể xem danh sách người dùng và phòng ban
        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->givePermissionTo(['view users', 'view departments']);


        // 3. Gán Role cho một người dùng cụ thể (ví dụ: người dùng đầu tiên)
        $user = User::first(); // Lấy người dùng đầu tiên
        if ($user) {
            $user->assignRole('admin'); // Gán vai trò 'admin' cho người dùng này
        }
        // Hoặc tạo một người dùng mới và gán vai trò
        // $newAdmin = User::firstOrCreate([
        //     'email' => 'admin@example.com',
        // ], [
        //     'name' => 'Admin User',
        //     'password' => bcrypt('password'),
        // ]);
        // $newAdmin->assignRole('admin');
    }
}
