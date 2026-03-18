<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ghi nhận mối quan hệ giữa các cây item
class ItemGenealogy extends Model
{
    use HasFactory;

    protected $table = 'item_genealogies';

    // Tắt timestamps mặc định vì bảng này chỉ cần created_at (đã định nghĩa trong migration)
    // hoặc bạn có thể giữ nguyên nếu migration có $table->timestamps()
    public $timestamps = false;

    protected $fillable = [
        'parent_item_id', // 🌟 ID cây Mộc (Nguyên liệu gốc)
        'child_item_id',  // 🌟 ID cây Tráng (Thành phẩm mới sinh ra)
        'action_type',    // Hành động tạo ra mối quan hệ này (Ví dụ: COATING, CUTTING)
        'user_id',        // Ai làm
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
