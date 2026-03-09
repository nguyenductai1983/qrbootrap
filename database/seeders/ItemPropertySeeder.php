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
        ItemProperty::create(['name' => 'Dài', 'code' => 'D', 'sort_order' => 1, 'unit' => 'm','code_usage' => 0]);
        ItemProperty::create(['name' => 'GSM', 'code' => 'GSM', 'sort_order' => 2, 'unit' => 'g','code_usage' => 0]);
        ItemProperty::create(['name' => 'Trọng Lượng', 'code' => 'TL', 'sort_order' => 3, 'unit' => 'kg','code_usage' => 0]);

        //
    }
}
