<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'specs'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function departments() {
        return $this->belongsToMany(Department::class, 'department_product_model');
    }
}
