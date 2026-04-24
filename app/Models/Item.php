<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ItemStatus;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'warehouse_code',
        'type',
        'status',
        'current_location_id',
        'order_id',
        'product_id', // <-- Thêm khóa ngoại liên kết với Product
        'department_id', // 🌟 Added
        'machine_id', // 🌟 Added
        'properties',
        'created_by',
        'verified_at',
        'verified_by',
        'color_id',
        'specification_id',
        'plastic_type_id',
        'width_original',
        'width',
        'lami',
        'gsmlami',
        'original_length',
        'length',
        'gsm',
        'weight',
        'weight_original',
        'notes',
        'warehoused_by',
        'warehoused_at',
        'shift',

    ];

    protected $casts = [
        'properties'   => 'array',
        'verified_at'  => 'datetime',
        'warehoused_at' => 'datetime',
        'status'       => ItemStatus::class,
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

    public function warehouser()
    {
        // Người nhập kho
        return $this->belongsTo(User::class, 'warehoused_by');
    }
    // --- Quan hệ Phả hệ (Genealogy) ---

    // Lấy danh sách "Con" (Ví dụ: Cây vải -> ra các Tấm vải)
    // Quan hệ: Item này là Parent trong bảng Genealogy
    public function children()
    {
        return $this->belongsToMany(Item::class, 'item_genealogies', 'parent_item_id', 'child_item_id')
            ->withPivot('action_type', 'used_length', 'created_at'); // 🌟 Thêm used_length
    }

    public function allChildren()
    {
        return $this->children()->with(['allChildren', 'product', 'department', 'color', 'creator', 'machine']);
    }

    public function parents()
    {
        return $this->belongsToMany(Item::class, 'item_genealogies', 'child_item_id', 'parent_item_id')
            ->withPivot('action_type', 'used_length', 'created_at'); // 🌟 Thêm used_length
    }

    public function allParents()
    {
        return $this->parents()->with(['allParents', 'product', 'department', 'color', 'creator', 'machine']);
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

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    public function specification()
    {
        return $this->belongsTo(Specification::class, 'specification_id');
    }
    public function plasticType()
    {
        return $this->belongsTo(PlasticType::class, 'plastic_type_id');
    }

    // --- Scope để lọc theo trạng thái ---
    public function scopeActive($query)
    {
        return $query->where('status', ItemStatus::VERIFIED);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }
    public function itemType()
    {
        return $this->belongsTo(ItemType::class, 'type');
    }

    // --- Lịch sử thay đổi ---
    public function histories()
    {
        return $this->hasMany(ItemHistory::class)->orderBy('created_at', 'desc');
    }
}
