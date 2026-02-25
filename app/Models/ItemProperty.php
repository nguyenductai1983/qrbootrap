<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemProperty extends Model
{
    protected $fillable = ['code', 'name', 'type', 'options', 'is_required', 'is_global', 'sort_order', 'is_active'];

    protected $casts = [
        'options' => 'array', // Tự động cast JSON sang Array
        'is_required' => 'boolean',
        'is_global' => 'boolean', // <-- Thêm dòng này
        'is_active' => 'boolean',
    ];
    // Định nghĩa quan hệ
    public function products()
    {
        return $this->belongsToMany(Product::class, 'item_property_product');
    }
}
