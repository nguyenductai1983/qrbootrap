<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// nhật kí hoạt động của item
class ItemMovement extends Model
{
    use HasFactory;

    public $timestamps = false; // Chỉ cần created_at

    protected $fillable = [
        'item_id',         // ID của cây vải đó
        'action_type',     // Hành động (Ví dụ: MOVE, COATING_UPDATE)
        'from_location_id', // Từ kho nào
        'to_location_id',  // Đến kho nào
        'user_id',         // Ai làm
        'note',            // Ghi chú thêm
        'created_at'
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
