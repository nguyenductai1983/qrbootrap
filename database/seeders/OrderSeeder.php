<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;


class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::create([
            'code' => 'PO001',
            'customer_name' => 'CÔNG TY TNHH ABC',
            'status' => 1,
        ]);
    }
}
