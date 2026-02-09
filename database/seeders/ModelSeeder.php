<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department; // Import Department model
use App\Models\ProductModel; // Import ProductModel model
use App\Models\Order; // Import Order model
class ModelSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 2 Model
        $modelMoc = ProductModel::create(['code' => 'V', 'name' => 'Vải']);
        $modelTP  = ProductModel::create(['code' => 'S', 'name' => 'SỢI']);

        // Lấy Department (Giả sử đã chạy seeder trước đó)
        $det = Department::where('code', 'HR')->first();
        $kho = Department::where('code', 'IT')->first();

        // Gán quyền:
        // Xưởng Dệt chỉ thấy Vải Mộc
        if ($det) $det->productModels()->attach($modelMoc->id);

        // Kho Vải thấy cả hai
        if ($kho) $kho->productModels()->attach([$modelMoc->id, $modelTP->id]);
        // Tạo 1 đơn hàng mẫu

    }
}
