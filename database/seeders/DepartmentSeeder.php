<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department; // Import Department model
class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create(['name' => 'IT', 'code' => 'ITS']);
        Department::create(['name' => 'Đùn', 'code' => 'DUN']);
        Department::create(['name' => 'Sợi', 'code' => 'SOI']);
        Department::create(['name' => 'Dệt', 'code' => 'DET']);
        Department::create(['name' => 'May', 'code' => 'MAY']);
        //
    }
}
