<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

   protected $fillable = ['name', 'code', 'is_warehouse']; // Cho phép lưu Code, Cờ đánh dấu Kho

   protected $casts = [
       'is_warehouse' => 'boolean',
   ];
    // Định nghĩa mối quan hệ một-nhiều với User
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function products() {
        return $this->belongsToMany(Product::class, 'department_product');
    }

    public function machines()
    {
        return $this->hasMany(Machine::class);
    }
}
