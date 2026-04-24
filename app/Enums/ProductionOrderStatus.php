<?php

namespace App\Enums;

enum ProductionOrderStatus: int
{
    case PENDING = 1;
    case RUNNING = 2;
    case COMPLETED = 3;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ xử lý',
            self::RUNNING => 'Đang chạy',
            self::COMPLETED => 'Đã hoàn thành',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PENDING => 'bg-warning text-dark',
            self::RUNNING => 'bg-primary',
            self::COMPLETED => 'bg-success',
        };
    }
}
