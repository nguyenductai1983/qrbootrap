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
        $ProductVai = Product::create(['code' => 'V', 'name' => 'Vải', 'is_active' => true]);
        $ProductTrang  = Product::create(['code' => 'T', 'name' => 'TRÁNG', 'is_active' => true]);
        $ProductMay  = Product::create(['code' => 'M', 'name' => 'May', 'is_active' => true]);

        // Lấy Department (Giả sử đã chạy seeder trước đó)
        $admin = Department::where('code', 'A')->first();
        $vai = Department::where('code', 'V')->first();
        $trang = Department::where('code', 'T')->first();
        $may = Department::where('code', 'M')->first();
        // Kho Vải thấy cả hai
        if ($admin) $admin->products()->attach([$ProductVai->id, $ProductTrang->id, $ProductMay->id]);
        if ($vai) $vai->products()->attach([$ProductVai->id]);
        if ($trang) $trang->products()->attach([$ProductTrang->id]);
        if ($may) $may->products()->attach([$ProductMay->id]);

        // Tạo 1 đơn hàng mẫu

    }
}
