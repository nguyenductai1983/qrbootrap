<?php

namespace App\Services;

class ItemCodeService
{
    // 🌟 THÊM KHAI BÁO SERVICE VÀO THAM SỐ CỦA HÀM

    /**
     * Sinh mã tem theo đúng chuẩn của màn hình Import Excel
     * Định dạng: [ORDER] [COLOR] [SPEC] [WIDTH] [PLASTIC] [GSM] [LENGTH] [STT]
     */
    public static function generateStandardCode($orderCode, $colorCode, $specCode, $widthCode, $plasticCode, array $dynamicParts, $sequenceNo)
    {
        // 1. Tạo Prefix
        $prefix = strtoupper(trim("$orderCode $colorCode $specCode $widthCode $plasticCode"));

        // 2. Nối các thuộc tính động (Lọc bỏ phần tử rỗng)
        $propParts = array_filter($dynamicParts);
        $code_properties = count($propParts) > 0 ? ' ' . implode(' ', $propParts) . ' ' : ' ';

        // 3. Ráp toàn bộ lại
        $realCode = strtoupper($prefix . $code_properties . str_pad($sequenceNo, 3, '0', STR_PAD_LEFT));

        return $realCode;
    }
}
