<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ProductionOrderStatus;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'status',
        'manager_id',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'status' => ProductionOrderStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'production_order_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
