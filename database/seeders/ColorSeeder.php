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
        Color::create(['name' => 'Trắng', 'code' => 'WE']);
        Color::create(['name' => 'Xanh', 'code' => 'BL']);
        Color::create(['name' => 'Lục', 'code' => 'GR']);
        //
    }
}
