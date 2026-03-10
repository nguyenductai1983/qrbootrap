<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Width extends Model
{
    protected $fillable = ['code', 'name', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];
     public function item(){
        return $this->hasMany(Item::class);
     }
    //
}
