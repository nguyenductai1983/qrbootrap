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
        Machine::create(['name' => 'S 1', 'code' => 'S1', 'department_id' => 2, 'status' => 1]);  //1
        Machine::create(['name' => 'S 2', 'code' => 'S2', 'department_id' => 2, 'status' => 1]);  //2
        Machine::create(['name' => 'S 3', 'code' => 'S3', 'department_id' => 2, 'status' => 1]);  //3
        Machine::create(['name' => 'D 1', 'code' => 'D1', 'department_id' => 3, 'status' => 1]);  //4
        Machine::create(['name' => 'D 2', 'code' => 'D2', 'department_id' => 3, 'status' => 1]);  //5
        Machine::create(['name' => 'D 3', 'code' => 'D3', 'department_id' => 3, 'status' => 1]);  //6
        Machine::create(['name' => 'T 1', 'code' => 'T1', 'department_id' => 4, 'status' => 1]);  //7
        Machine::create(['name' => 'T 2', 'code' => 'T2', 'department_id' => 4, 'status' => 1]);  //8
        Machine::create(['name' => 'T 3', 'code' => 'T3', 'department_id' => 4, 'status' => 1]);  //9
        Machine::create(['name' => 'C 1', 'code' => 'C1', 'department_id' => 5, 'status' => 1]);  //10
        Machine::create(['name' => 'C 2', 'code' => 'C2', 'department_id' => 5, 'status' => 1]);  //11
        Machine::create(['name' => 'C 3', 'code' => 'C3', 'department_id' => 5, 'status' => 1]);  //12
        Machine::create(['name' => 'M 1', 'code' => 'M1', 'department_id' => 6, 'status' => 1]);  //13
        Machine::create(['name' => 'M 2', 'code' => 'M2', 'department_id' => 6, 'status' => 1]);  //14
        Machine::create(['name' => 'M 3', 'code' => 'M3', 'department_id' => 6, 'status' => 1]);  //15
        //
        $user = User::first(); //admin
        $user->machines()->attach([1, 2, 3]);
        $user = User::skip(1)->first(); //Admin quản lý Tiến
        if ($user) {
            $user->machines()->attach([1, 2, 3]);
        }
        $user = User::skip(2)->first(); //Kho

        $user = User::skip(3)->first(); //trang      

        if ($user) {
            $user->machines()->attach([7, 8, 9]);
        }
    }
}
