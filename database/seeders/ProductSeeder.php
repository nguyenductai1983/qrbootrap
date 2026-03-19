<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department; // Import Department model
use App\Models\Product; // Import ProductModel model
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 2 Model
        $ProductMoc = Product::create(['code' => 'V', 'name' => 'Vải', 'is_active' => true]);
        $ProductSoi  = Product::create(['code' => 'S', 'name' => 'SỢI', 'is_active' => true]);
        $ProductTrang  = Product::create(['code' => 'T', 'name' => 'TRÁNG', 'is_active' => true]);

        // Lấy Department (Giả sử đã chạy seeder trước đó)
        $it = Department::where('id', 1)->first();


        // Kho Vải thấy cả hai
        if ($it) $it->products()->attach([$ProductMoc->id, $ProductSoi->id, $ProductTrang->id]);
        // Tạo 1 đơn hàng mẫu

    }
}
