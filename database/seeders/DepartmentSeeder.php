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
        Department::create(['name' => 'Admin', 'code' => 'A']); //1
        Department::create(['name' => 'Sợi', 'code' => 'S']); //2
        Department::create(['name' => 'Dệt', 'code' => 'D']); //3        
        Department::create(['name' => 'Tráng', 'code' => 'T']); //4
        Department::create(['name' => 'Cắt', 'code' => 'C']); //5
        Department::create(['name' => 'May', 'code' => 'M']); //6      
        Department::create(['name' => 'Kho', 'code' => 'K']); //7

    }
}
