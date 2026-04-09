<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintStation extends Model
{
    protected $fillable = ['name', 'code', 'status', 'client_type', 'station_token', 'template_name'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
