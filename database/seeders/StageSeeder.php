<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stage;

class StageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['code' => 'MOC', 'name' => 'Vải Mộc', 'order_index' => 1],
            ['code' => 'TRANG', 'name' => 'Tráng Nhựa', 'order_index' => 2],
            ['code' => 'IN', 'name' => 'In Hoa Văn', 'order_index' => 3],
            ['code' => 'CHIA', 'name' => 'Chia Cuộn / Cắt Xén', 'order_index' => 4],
            ['code' => 'DONG_GOI', 'name' => 'Đóng Gói Thành Phẩm', 'order_index' => 5],
        ];

        foreach ($stages as $stage) {
            Stage::updateOrCreate(['code' => $stage['code']], $stage);
        }
    }
}
