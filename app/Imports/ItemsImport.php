<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cột A trong Excel là 'BARCODE' (thư viện tự chuyển slug)
        $barcode = $row['BARCODE'] ?? null;

        if (!$barcode) return null;

        $item = Item::where('code', $barcode)->first();

        if ($item) {
            // Lấy properties hiện tại
            $props = $item->properties ?? [];

            // Cập nhật thông tin mới từ Excel
            // Lưu ý: Tên key mảng $row phụ thuộc vào Heading trong file Excel
            foreach ($row as $key => $value) {

                // BƯỚC A: Loại bỏ các cột hệ thống không muốn lưu vào properties
                // Ví dụ: Bỏ qua cột barcode chính, hoặc các cột rỗng
                if ($key === 'BARCODE' || is_null($value)) {
                    continue;
                }

                // BƯỚC B: Chuẩn hóa Key (Tùy chọn)
                // Vì Excel import vào sẽ ra dạng 'so_met', 'ghi_chu'...
                // Nếu bạn muốn lưu vào DB dạng 'SO_MET', 'GHI_CHU' thì dùng strtoupper
                $dbKey = strtoupper($key);

                // Gán giá trị động
                $props[$dbKey] = $value;
            }

            // Lưu lại
            $item->update([
                'properties' => $props
            ]);
        }
        return null;
    }
}
