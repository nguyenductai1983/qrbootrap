<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color; // Import Department model
class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Color::create(['name' => 'Xanh', 'code' => 'BLU']);
        Color::create(['name' => 'Trắng', 'code' => 'WHT']);
        Color::create(['name' => 'Lục', 'code' => 'GRN']);
        //
    }
}
