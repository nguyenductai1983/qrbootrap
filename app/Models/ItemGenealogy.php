<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemGenealogy extends Model
{
    use HasFactory;

    protected $table = 'item_genealogies';

    // Tắt timestamps mặc định vì bảng này chỉ cần created_at (đã định nghĩa trong migration)
    // hoặc bạn có thể giữ nguyên nếu migration có $table->timestamps()
    public $timestamps = false;

    protected $fillable = [
        'parent_item_id', 'child_item_id', 'action_type', 'created_by', 'created_at'
    ];
}
