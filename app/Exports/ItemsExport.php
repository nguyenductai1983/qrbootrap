<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\ItemProperty;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orderId;
    protected $producId;
    protected $dynamicProperties; // Khai báo thêm biến này để lưu cache properties

    public function __construct($orderId, $producId)
    {
        $this->orderId = $orderId;
        $this->producId = $producId;

        // TỐI ƯU: Chỉ query ItemProperty 1 lần duy nhất khi khởi tạo class
        $this->dynamicProperties = ItemProperty::where('is_active', true)
            ->where(function ($q) use ($producId) { // Cần có 'use ($producId)' ở đây
                $q->where('is_global', true)
                    ->orWhereHas('products', function ($q2) use ($producId) {
                        $q2->where('products.id', $producId);
                    });
            })
            ->orderBy('sort_order')
            ->get();
    }

    public function collection()
    {
        return Item::where('order_id', $this->orderId)
            ->where('product_id', $this->producId)
            // Nếu trường PO nằm ở bảng Order (ví dụ $item->order->po_number),
            // bạn nên thêm ->with('order') ở đây để tránh lỗi N+1
            ->get();
    }

    public function headings(): array
    {
        // Lấy danh sách 'code' từ properties đã query sẵn
        $dynamicHeaders = $this->dynamicProperties->pluck('code')->toArray();

        // HƯỚNG DẪN 1: Thêm 'PO' vào mảng cố định (bạn có thể đổi vị trí tùy ý)
        return array_merge(['code', 'created_at', 'PO'], $dynamicHeaders);
    }

    public function map($item): array
    {
        $row = [
            $item->code,
            $item->created_at->format('d/m/Y'),
            // HƯỚNG DẪN 2: Lấy giá trị PO tương ứng.
            // Bạn hãy đổi '$item->po' thành tên cột thực tế chứa PO của bạn.
            $item->po,


        ];

        // Duyệt qua properties đã lưu sẵn, không query lại database nữa
        foreach ($this->dynamicProperties as $prop) {
            $row[] = $item->properties[$prop->code] ?? '';
        }

        return $row;
    }
}
