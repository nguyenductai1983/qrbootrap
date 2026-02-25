<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemProperty; // Import Department model
class ItemPropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemProperty::create(['name' => 'Dài', 'code' => 'DAI']);
        ItemProperty::create(['name' => 'Rộng', 'code' => 'RONG']);
        ItemProperty::create(['name' => 'Cao', 'code' => 'CAO']);
        ItemProperty::create(['name' => 'Trọng Lượng', 'code' => 'TRONGLUONG']);
        ItemProperty::create(['name' => 'GSM', 'code' => 'GSM']);
        ItemProperty::create(['name' => 'Màu Sắc', 'code' => 'MAU']);
        ItemProperty::create(['name' => 'Kích Thước', 'code' => 'KICHTHUOC']);
        ItemProperty::create(['name' => 'Chất Liệu', 'code' => 'CHATLIEU']);
        //
    }
}
