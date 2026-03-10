<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemType; // Import Department model
class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemType::create(['name' => 'Nguyên Liệu', 'code' => 'NL']);
        ItemType::create(['name' => 'Bán Thành Phẩm', 'code' => 'BP']);
        ItemType::create(['name' => 'Thành Phẩm', 'code' => 'TP']);
        //
    }
}
