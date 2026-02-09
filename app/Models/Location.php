<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'type'];

    // Một vị trí có thể chứa nhiều Item
    public function items()
    {
        return $this->hasMany(Item::class, 'current_location_id');
    }
}
