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
        $ProductMoc = Product::create(['code' => 'V', 'name' => 'Vải']);
        $ProductTP  = Product::create(['code' => 'S', 'name' => 'SỢI']);

        // Lấy Department (Giả sử đã chạy seeder trước đó)
        $det = Department::where('id', 1)->first();
        $kho = Department::where('id', 2)->first();

        // Gán quyền:
        // Xưởng Dệt chỉ thấy Vải Mộc
        if ($det) $det->products()->attach($ProductMoc->id);

        // Kho Vải thấy cả hai
        if ($kho) $kho->products()->attach([$ProductMoc->id, $ProductTP->id]);
        // Tạo 1 đơn hàng mẫu

    }
}
