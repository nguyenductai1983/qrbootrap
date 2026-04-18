<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// dùng cho Vải nhập lại đi chung với ItemsTemplateExport
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
            $ignoredColumns = ['code', 'created_at', 'original_length', 'length', 'gsm', 'weight', 'notes', 'shift'];

            // 2. Cập nhật các trường cố định
            if (array_key_exists('original_length', $row)) {
                if (is_null($row['original_length']) || $row['original_length'] === '') {
                    $item->original_length = null;
                } else {
                    $item->original_length = is_numeric($row['original_length']) ? (float) $row['original_length'] : $item->original_length;
                }
            }
            if (array_key_exists('length', $row)) {
                if (is_null($row['length']) || $row['length'] === '') {
                    $item->length = null;
                } else {
                    $item->length = is_numeric($row['length']) ? (float) $row['length'] : $item->length;
                }
            }
            if (array_key_exists('gsm', $row)) {
                if (is_null($row['gsm']) || $row['gsm'] === '') {
                    $item->gsm = null;
                } else {
                    $item->gsm = is_numeric($row['gsm']) ? (float) $row['gsm'] : $item->gsm;
                }
            }
            if (array_key_exists('weight', $row)) {
                if (is_null($row['weight']) || $row['weight'] === '') {
                    $item->weight = null;
                } else {
                    $item->weight = is_numeric($row['weight']) ? (float) $row['weight'] : $item->weight;
                }
            }
            if (array_key_exists('notes', $row)) {
                if (is_null($row['notes']) || $row['notes'] === '') {
                    $item->notes = null;
                } else {
                    $item->notes = $row['notes'];
                }
            }
            if (array_key_exists('shift', $row)) {
                if (is_null($row['shift']) || $row['shift'] === '') {
                    $item->shift = null;
                } else {
                    $item->shift = $row['shift'];
                }
            }

            // 3. Quét các cột còn lại để đưa vào properties
            foreach ($row as $key => $value) {

                // BƯỚC A: Bỏ qua các cột cố định
                if (in_array($key, $ignoredColumns)) {
                    continue;
                }

                $dbKey = strtoupper($key);

                // BƯỚC B: Gán giá trị động, nếu trống thì gán chuỗi rỗng để có thể xoá dữ liệu cũ
                if (is_null($value) || $value === '') {
                    $props[$dbKey] = ''; // hoặc unset($props[$dbKey]); tùy nhu cầu, nhưng để trống là an toàn
                } else {
                    $props[$dbKey] = $value;
                }
            }
            // 4. Lưu lại thông tin
            // Cập nhật cả properties và các trường cố định (như po) nếu có thay đổi
            $item->properties = $props;
            $item->save();
        }

        return null;
    }
}
