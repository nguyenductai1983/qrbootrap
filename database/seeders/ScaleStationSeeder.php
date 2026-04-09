<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScaleStation;

class ScaleStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScaleStation::create(['name' => '01', 'code' => '01', 'station_token' => 'JPBWV1E0LC3VNU2SD7JBB8ZA0R6LT4LI', 'status' => '1']);
    }
}
