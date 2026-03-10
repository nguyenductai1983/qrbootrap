<?php

namespace App\Enums;

enum OrderType: string
{
    case TYPE_C = 'C';
    case TYPE_F = 'F';
    case TYPE_H = 'H';

    public function label(): string
    {
        return match ($this) {
            self::TYPE_C => 'Đơn hàng loại C',
            self::TYPE_F => 'Đơn hàng loại F',
            self::TYPE_H => 'Đơn hàng loại H',
        };
    }

    // Tùy chọn màu sắc hiển thị cho loại đơn hàng nếu bạn muốn
    public function badge(): string
    {
        return match ($this) {
            self::TYPE_C => 'bg-info text-dark',
            self::TYPE_F => 'bg-secondary',
            self::TYPE_H => 'bg-dark',
        };
    }
}
