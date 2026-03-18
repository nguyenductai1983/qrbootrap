<?php

namespace App\Enums;

enum ActionType: int
{
    case MOVE = 1;
    case COATING = 2;
    case CUTTING = 3;
    // Hàm lấy tên tiếng Việt để hiển thị ra giao diện
    public function label(): string
    {
        return match ($this) {
            self::MOVE => 'Chuyển kho',
            self::COATING => 'Tráng',
            self::CUTTING => 'Cắt',
        };
    }

    // Hàm lấy màu sắc Bootstrap (Badge) cho từng trạng thái
    public function badge(): string
    {
        return match ($this) {
            self::MOVE => 'bg-secondary text-dark',
            self::COATING => 'bg-success',
            self::CUTTING => 'bg-danger',
        };
    }
}
