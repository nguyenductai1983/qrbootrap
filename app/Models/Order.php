<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'status', 'customer_name', 'meta_data'];

    protected $casts = [
        'meta_data' => 'array', // Tự động chuyển JSON sang mảng
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
