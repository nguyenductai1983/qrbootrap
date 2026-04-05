<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    protected $table = 'item_types';
    protected $fillable = ['code', 'name', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'type');
    }
}
