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
        Specification::create(['name' => 'ONG', 'code' => 'ONG']);
        Specification::create(['name' => 'MANH', 'code' => 'MANH']);
        //
    }
}
