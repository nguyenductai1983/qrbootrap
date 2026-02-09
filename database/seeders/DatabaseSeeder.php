<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Thêm dòng này để mã hóa mật khẩu

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Chạy Seeder Phòng ban trước (vì User cần Department ID)
        $this->call(DepartmentSeeder::class);

        // 2. Chạy Seeder Quyền và Vai trò
        $this->call(RolesAndPermissionsSeeder::class);

        // 3. Tạo User Admin mẫu (Sau khi đã có Role và Department)
        // Kiểm tra xem user đã tồn tại chưa để tránh lỗi trùng email khi chạy lại
        $user = User::firstOrCreate(
            ['email' => 'admin@qrcode.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'), // Đặt mật khẩu
                'department_id' => 1, // Gán vào phòng IT (ID 1) nếu muốn
            ]
        );

        // Gán quyền Admin cho user này
        $user->assignRole('admin');
    }
}
