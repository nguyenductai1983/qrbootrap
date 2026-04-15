<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Chạy Seeder Bộ phận trước (vì User cần Department ID)
        $this->call(DepartmentSeeder::class);
        // 2. Chạy Seeder Quyền và Vai trò
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ItemTypeSeeder::class);
        $this->call(ItemPropertySeeder::class);
        $this->call(OrderSeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(PlasticTypeSeeder::class);
        $this->call(SpecificationSeeder::class);
        $this->call(MachineSeeder::class);
        $this->call(PrintStationSeeder::class);
        $this->call(ScaleStationSeeder::class);
        $this->call(LocationSeeder::class);
        // 3. Tạo User Admin mẫu (Sau khi đã có Role và Department)
        // Kiểm tra xem user đã tồn tại chưa để tránh lỗi trùng email khi chạy lại
    }
}
