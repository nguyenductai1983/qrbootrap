<?php

namespace App\Enums;

enum ItemStatus: int
{
    case NONE = 0;
    case VERIFIED = 1;
    case IN_WAREHOUSE = 2;
    case SURPLUS_ENTRY = 3;

    // Hàm lấy tên tiếng Việt để hiển thị ra giao diện
    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Chưa SX',
            self::VERIFIED => 'Đã SX',
            self::IN_WAREHOUSE => 'Đã nhập kho',
            self::SURPLUS_ENTRY => 'Hoàn kho',
        };
    }

    // Hàm lấy màu sắc Bootstrap (Badge) cho từng trạng thái
    public function badge(): string
    {
        return match ($this) {
            self::NONE => 'bg-secondary text-dark',
            self::VERIFIED => 'bg-success',
            self::IN_WAREHOUSE => 'bg-info text-dark',
            self::SURPLUS_ENTRY => 'bg-warning text-dark',
        };
    }
}
