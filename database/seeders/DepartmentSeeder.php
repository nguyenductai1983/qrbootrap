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
        Department::create(['name' => 'IT', 'code' => 'IT']);
        Department::create(['name' => 'Marketing', 'code' => 'MARKETING']);
        Department::create(['name' => 'Sales', 'code' => 'SALES']);
        Department::create(['name' => 'HR', 'code' => 'HR']);
        Department::create(['name' => 'Finance', 'code' => 'FINANCE']);
        //
    }
}
