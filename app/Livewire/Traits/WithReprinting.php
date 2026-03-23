<?php

namespace App\Livewire\Traits;

trait WithReprinting
{
    /**
     * Dispatch event để mở tab in ấn tập trung.
     * 
     * @param array $itemIds Mảng chứa các ID con tem cần in
     * @param string $format "QR" hoặc "BARCODE"
     * @param int $cols Số lượng mã trên 1 hàng ngang
     * @param int $rows Số hàng trên 1 tờ giấy (khổ giấy in)
     * @param int $fontSize Cỡ chữ hiển thị
     */
    public function reprintItems(array $itemIds, $format = 'QR', $cols = 2, $rows = 5, $fontSize = 10)
    {
        if (empty($itemIds)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một tem để in.');
            return;
        }

        $idString = implode(',', $itemIds);
        
        $url = route('print.labels', [
            'ids'    => $idString,
            'format' => $format,
            'cols'   => $cols,
            'rows'   => $rows,
            'size'   => $fontSize
        ]);

        // Gửi JS Event (bắt ở app.blade.php) để mở tab URL vừa tạo
        $this->dispatch('open-print-tab', url: $url);
    }
}
