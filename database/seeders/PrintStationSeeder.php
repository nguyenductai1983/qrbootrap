<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PrintStation; // Import Department model
use App\Models\User;

class PrintStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PrintStation::create(['name' => '01', 'code' => '01']);
        $user = User::where('username', 'trang')->first(); //trang
        if ($user) {
            $user->printStations()->attach([1]);
        }
    }
}
