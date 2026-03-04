<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Width; // Import Department model
class WidthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Width::create(['name' => '1800', 'code' => 'K1800']);
        Width::create(['name' => '1500', 'code' => 'K1500']);
        Width::create(['name' => '1000', 'code' => 'K1000']);
        //
    }
}
