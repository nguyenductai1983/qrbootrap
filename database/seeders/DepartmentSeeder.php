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
        Department::create(['name' => 'Admin', 'code' => 'A', 'is_admin' => true]); //1
        Department::create(['name' => 'Vải', 'code' => 'V']); //2          
        Department::create(['name' => 'Tráng', 'code' => 'T']); //3
        Department::create(['name' => 'Cắt', 'code' => 'C']); //4
        Department::create(['name' => 'May', 'code' => 'M']); //5      
        Department::create(['name' => 'Kho', 'code' => 'K', 'is_warehouse' => true]); //6
    }
}
