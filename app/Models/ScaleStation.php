<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScaleStation extends Model
{
    protected $fillable = [
        'name',
        'code',
        'station_token',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
