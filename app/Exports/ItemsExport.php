<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orderId;
    protected $modelId;

    public function __construct($orderId, $modelId)
    {
        $this->orderId = $orderId;
        $this->modelId = $modelId;
    }

    public function collection()
    {
        // Lấy các tem thuộc Đơn hàng & Model này
        return Item::where('order_id', $this->orderId)
                   ->where('product_model_id', $this->modelId)
                   ->get();
    }

    public function headings(): array
    {
        return [
            'BARCODE', // Cột A: Khóa chính để nhận diện
            'Mã Vải',
            'Màu',
            'Số Mét',
            'Trọng Lượng',
            'Ghi Chú',
            'Ngày Tạo'
        ];
    }

    public function map($item): array
    {
        // Đổ dữ liệu có sẵn ra file
        return [
            $item->code,
            $item->properties['MA_VAI'] ?? '',
            $item->properties['MAU'] ?? '',
            $item->properties['SO_MET'] ?? '',      // Để trống hoặc lấy giá trị cũ
            $item->properties['TRONG_LUONG'] ?? '', // Để trống hoặc lấy giá trị cũ
            $item->properties['GHI_CHU'] ?? '',
            $item->created_at->format('d/m/Y'),
        ];
    }
}
