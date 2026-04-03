<?php

namespace App\Enums;

enum MovementAction: int
{
    case IN_WAREHOUSE      = 1;      // Nhập kho bán thành phẩm
    case OUT_WAREHOUSE     = 2;     // Xuất kho đưa vào sản xuất
    case CONFIRM_LOCATION  = 3;  // Xác nhận / Cập nhật vị trí
    case MOVE              = 4;              // Chuyển vị trí trong kho

    public function label(): string
    {
        return match ($this) {
            self::IN_WAREHOUSE     => 'Nhập kho',
            self::OUT_WAREHOUSE    => 'Xuất kho',
            self::CONFIRM_LOCATION => 'Xác nhận vị trí',
            self::MOVE             => 'Chuyển vị trí',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::IN_WAREHOUSE     => 'bg-success',
            self::OUT_WAREHOUSE    => 'bg-warning text-dark',
            self::CONFIRM_LOCATION => 'bg-info text-dark',
            self::MOVE             => 'bg-primary',
        };
    }
}
