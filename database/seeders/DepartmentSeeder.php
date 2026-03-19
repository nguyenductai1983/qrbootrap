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
        Department::create(['name' => 'IT', 'code' => 'I']);
        Department::create(['name' => 'Sợi', 'code' => 'S']);
        Department::create(['name' => 'Dệt', 'code' => 'D']);
        Department::create(['name' => 'May', 'code' => 'M']);
        Department::create(['name' => 'Tráng', 'code' => 'T']);
        //
    }
}
