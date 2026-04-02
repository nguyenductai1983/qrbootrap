<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintStation extends Model
{
    protected $fillable = ['name', 'code', 'status'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
