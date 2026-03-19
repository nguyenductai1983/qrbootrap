<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Machine; // Import Department model
use App\Models\User;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Machine::create(['name' => 'Máy Sợi 1', 'code' => 'S1', 'department_id' => 2, 'status' => 1]);
        Machine::create(['name' => 'Máy Sợi 2', 'code' => 'S2', 'department_id' => 2, 'status' => 1]);
        Machine::create(['name' => 'Máy Sợi 3', 'code' => 'S3', 'department_id' => 2, 'status' => 1]);
        Machine::create(['name' => 'Máy Dệt 1', 'code' => 'D1', 'department_id' => 3, 'status' => 1]);
        Machine::create(['name' => 'Máy Dệt 2', 'code' => 'D2', 'department_id' => 3, 'status' => 1]);
        Machine::create(['name' => 'Máy Dệt 3', 'code' => 'D3', 'department_id' => 3, 'status' => 1]);
        Machine::create(['name' => 'Máy Tráng 1', 'code' => 'T1', 'department_id' => 4, 'status' => 1]);
        Machine::create(['name' => 'Máy Tráng 2', 'code' => 'T2', 'department_id' => 4, 'status' => 1]);
        Machine::create(['name' => 'Máy Tráng 3', 'code' => 'T3', 'department_id' => 4, 'status' => 1]);
        Machine::create(['name' => 'Máy Cắt 1', 'code' => 'C1', 'department_id' => 5, 'status' => 1]);
        Machine::create(['name' => 'Máy Cắt 2', 'code' => 'C2', 'department_id' => 5, 'status' => 1]);
        Machine::create(['name' => 'Máy Cắt 3', 'code' => 'C3', 'department_id' => 5, 'status' => 1]);
        Machine::create(['name' => 'Máy May 1', 'code' => 'M1', 'department_id' => 6, 'status' => 1]);
        Machine::create(['name' => 'Máy May 2', 'code' => 'M2', 'department_id' => 6, 'status' => 1]);
        Machine::create(['name' => 'Máy May 3', 'code' => 'M3', 'department_id' => 6, 'status' => 1]);
        //
        $user = User::first();
        $user->machines()->attach([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]);
        $user = User::first(2);
        $user->machines()->attach([1, 4, 7, 10, 13]);
    }
}
