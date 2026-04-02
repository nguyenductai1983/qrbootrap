<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\MovementAction;
// nhật kí hoạt động của item
class ItemMovement extends Model
{
    use HasFactory;

    public $timestamps = false; // Chỉ cần created_at

    protected $fillable = [
        'item_id',
        'action_type',
        'from_location_id',
        'to_location_id',
        'user_id',
        'note',
        'created_at'
    ];

    protected $casts = [
        'action_type' => MovementAction::class,
        'created_at'  => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
