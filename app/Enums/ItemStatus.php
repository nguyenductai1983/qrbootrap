<?php

namespace App\Enums;

enum ItemStatus: int
{
    case NONE = 0;
    case VERIFIED = 1;


    // Hàm lấy tên tiếng Việt để hiển thị ra giao diện
    public function label(): string
    {
        return match($this) {
            self::NONE => 'Chưa xác nhận',
            self::VERIFIED => 'Đã xác nhận',
        };
    }

    // Hàm lấy màu sắc Bootstrap (Badge) cho từng trạng thái
    public function badge(): string
    {
        return match($this) {
            self::NONE => 'bg-secondary text-dark',
            self::VERIFIED => 'bg-success',

        };
    }
}
