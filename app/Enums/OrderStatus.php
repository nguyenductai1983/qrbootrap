<?php

namespace App\Enums;

enum OrderStatus: int
{
    case PENDING = 1;
    case RUNNING = 2;
    case COMPLETED = 3;

    // Hàm lấy tên tiếng Việt để hiển thị ra giao diện
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Chờ xử lý',
            self::RUNNING => 'Đang chạy (Sản xuất)',
            self::COMPLETED => 'Đã hoàn thành',
        };
    }

    // Hàm lấy màu sắc Bootstrap (Badge) cho từng trạng thái
    public function badge(): string
    {
        return match($this) {
            self::PENDING => 'bg-warning text-dark',
            self::RUNNING => 'bg-primary',
            self::COMPLETED => 'bg-success',
        };
    }
}
