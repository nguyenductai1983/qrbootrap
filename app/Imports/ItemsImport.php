<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Thư viện Maatwebsite Excel (WithHeadingRow) tự động chuyển đổi header
        // thành dạng slug (viết thường, dấu cách thành gạch dưới).
        // Nên ta có thể gọi trực tiếp 'code' thay vì dùng array_key_first().
        $code = $row['code'] ?? null;

        if (!$code) return null;

        $item = Item::where('code', $code)->first();

        if ($item) {
            $props = $item->properties ?? [];

            // 1. Khai báo các cột CỐ ĐỊNH không được lưu vào JSON properties
            $ignoredColumns = ['code', 'created_at'];

            // 2. (Tùy chọn) Cập nhật trường PO nếu PO lưu ở bảng Item
            // Nếu PO nằm ở bảng khác hoặc bạn không muốn update, hãy xóa dòng này đi.
            // if (isset($row['po'])) {
            //     $item->po = $row['po'];
            // }

            // 3. Quét các cột còn lại để đưa vào properties
            foreach ($row as $key => $value) {

                // BƯỚC A: Bỏ qua các cột cố định và các ô rỗng
                if (in_array($key, $ignoredColumns) || is_null($value)) {
                    continue;
                }

                // BƯỚC B: Chuẩn hóa Key từ 'so_met' thành 'SO_MET'
                $dbKey = strtoupper($key);

                // Gán giá trị động
                $props[$dbKey] = $value;
            }

            // 4. Lưu lại thông tin
            // Cập nhật cả properties và các trường cố định (như po) nếu có thay đổi
            $item->properties = $props;
            $item->save();
        }

        return null;
    }
}
