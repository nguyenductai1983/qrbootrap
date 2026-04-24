<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus; // <-- Thêm dòng này
use App\Enums\OrderType; // <-- Thêm dòng này
class Order extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'production_order_id', 'status', 'type', 'total', 'customer_name', 'meta_data'];

    protected $casts = [
        'meta_data' => 'array', // Tự động chuyển JSON sang mảng
        'status' => OrderStatus::class, // <-- Báo cho Laravel biết status là Enum
        'type' => OrderType::class,
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }
}
