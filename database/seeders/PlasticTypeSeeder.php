<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PlasticType; // Import Department model
class PlasticTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlasticType::create(['name' => 'Polyethylene', 'code' => 'PP']);
        PlasticType::create(['name' => 'Polypropylene', 'code' => 'PE']);
        //
    }
}
