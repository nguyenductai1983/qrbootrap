<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ItemPhoto extends Model
{
    protected $fillable = [
        'item_id',
        'user_id',
        'path',
        'disk',
        'size',
    ];

    // ========== RELATIONSHIPS ==========

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== ACCESSORS ==========

    /**
     * URL công khai của ảnh
     * @return string
     */
    public function getUrlAttribute(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);
        return $disk->url($this->path);
    }

    // ========== HELPERS ==========

    /**
     * Xóa file vật lý khi xóa record
     */
    protected static function booted(): void
    {
        static::deleting(function (ItemPhoto $photo) {
            Storage::disk($photo->disk)->delete($photo->path);
        });
    }
}
