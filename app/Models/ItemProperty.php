<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemProperty extends Model
{
    protected $fillable = [
        'code',
        'code_usage',
        'name',
        'type',
        'options',
        'is_required',
        'is_global',
        'sort_order',
        'is_active',
        'color_id',
        'specification_id',
        'plastic_type_id',
        'unit',
        'is_code', // 🌟 THÊM DÒNG NÀY
    ];

    protected $casts = [
        'options' => 'array', // Tự động cast JSON sang Array
        'is_required' => 'boolean',
        'is_global' => 'boolean', // <-- Thêm dòng này
        'is_active' => 'boolean',
        'code_usage' => 'boolean',
        'is_code' => 'boolean',
    ];
    // Định nghĩa quan hệ
    public function products()
    {
        return $this->belongsToMany(Product::class, 'item_property_product');
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    // Liên kết với bảng Specifications
    public function specification()
    {
        return $this->belongsTo(Specification::class);
    }

    // Liên kết với bảng PlasticTypes
    public function plasticType()
    {
        return $this->belongsTo(PlasticType::class);
    }
}
