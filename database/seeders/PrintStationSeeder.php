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
        PrintStation::create(['name' => 'Web 01', 'code' => '01', 'client_type' => 'browser']);
        PrintStation::create(['name' => '02', 'code' => '02', 'client_type' => 'app', 'station_token' => 'ov4pxg61zv6lsnqpbp3h', 'template_name' => 'Mau01']);
        $user = User::where('username', 'trang')->first(); //trang
        if ($user) {
            $user->printStations()->attach([1]);
        }
        $user = User::where('username', 'admin')->first(); //trang
        if ($user) {
            $user->printStations()->attach([1]);
        }
    }
}
