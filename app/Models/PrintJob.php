<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_FAILED = 1;
    const STATUS_PRINTING = 2;
    const STATUS_SUCCESS = 3;

    protected $fillable = [
        'item_id',
        'printer_mac',
        'user_id',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
