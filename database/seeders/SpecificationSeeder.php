<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specification; // Import Department model
class SpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specification::create(['name' => 'DOUBLE', 'code' => 'D']);
        Specification::create(['name' => 'SINGLE', 'code' => 'S']);
        //
    }
}
