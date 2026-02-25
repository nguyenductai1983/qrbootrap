<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'type',
        'status',
        'current_location_id',
        'order_id',
        'product_id', // <-- Thêm khóa ngoại liên kết với Product
        'properties',
        'created_by',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'properties' => 'array', // Lưu màu, khổ, trọng lượng
        'verified_at' => 'datetime',
    ];

    // --- Quan hệ cơ bản ---
    public function location()
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function verifier()
    {
        // Người quét/Xác nhận (verified_by)
        return $this->belongsTo(User::class, 'verified_by');
    }
    // --- Quan hệ Phả hệ (Genealogy) ---

    // Lấy danh sách "Con" (Ví dụ: Cây vải -> ra các Tấm vải)
    // Quan hệ: Item này là Parent trong bảng Genealogy
    public function children()
    {
        return $this->belongsToMany(Item::class, 'item_genealogies', 'parent_item_id', 'child_item_id')
            ->withPivot('action_type', 'created_at');
    }

    // Lấy danh sách "Cha" (Ví dụ: Tấm vải -> được cắt từ Cây nào)
    // Quan hệ: Item này là Child trong bảng Genealogy
    public function parents()
    {
        return $this->belongsToMany(Item::class, 'item_genealogies', 'child_item_id', 'parent_item_id')
            ->withPivot('action_type', 'created_at');
    }

    // Lấy lịch sử di chuyển
    public function movements()
    {
        return $this->hasMany(ItemMovement::class);
    }
    public function product()
    {
        // Chỉ định rõ khóa ngoại là 'product_model_id' vì nó không theo chuẩn tên mặc định 'product_id'
        return $this->belongsTo(Product::class, 'product_id');
    }
}
