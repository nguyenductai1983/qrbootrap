<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrScan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'qr_scans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id', // ID người dùng nếu đã đăng nhập
        'qr_code',
        'data',
        'scanner_ip',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'data' => 'json', // Laravel sẽ tự động chuyển đổi JSON sang array/object PHP và ngược lại
    ];
    //
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getUserNameAttribute(): ?string
    {
        // Kiểm tra xem mối quan hệ user có tồn tại không trước khi truy cập name
        return $this->user->name ?? null;
    }
}
