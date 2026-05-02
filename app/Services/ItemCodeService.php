<?php

namespace App\Services;

class ItemCodeService
{
    // 🌟 THÊM KHAI BÁO SERVICE VÀO THAM SỐ CỦA HÀM

    /**
     * Sinh mã tem theo đúng chuẩn của màn hình Import Excel
     * Định dạng: [ORDER] [COLOR] [SPEC] [WIDTH] [PLASTIC] [GSM] [LENGTH] [STT]
     */
    public static function generateStandardCode(mixed $orderCode, mixed $colorCode, mixed $specCode, mixed $widthCode, mixed $plasticCode, mixed $gsm, mixed $length, mixed $sequenceNo)
    {
        // 1. Tạo Prefix
        $prefix = strtoupper(trim("$orderCode $colorCode $specCode $widthCode $plasticCode $gsm $length"));
        // 2. Ráp toàn bộ lại
        $realCode = strtoupper($prefix . " " . str_pad($sequenceNo, 3, '0', STR_PAD_LEFT));

        return $realCode;
    }
}
