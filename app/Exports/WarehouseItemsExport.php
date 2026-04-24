<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Item;
use App\Enums\ItemStatus;

class WarehouseItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $items;
    protected $dynamicKeys = [];

    public function __construct($productId = null, $fromDate = null, $toDate = null)
    {
        $query = Item::with([
            'order',
            'product',
            'color',
            'department',
            'machine',
            'specification',
            'plasticType',
            'location',
            'creator',
            'verifier',
            'warehouser'
        ])
            ->where(function ($q) {
                $q->where('warehoused_by', '!=', null)
                    ->orWhereNotNull('current_location_id');
            });

        // Áp dụng bộ lọc Nếu có
        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($fromDate && $toDate) {
            $query->whereBetween('warehoused_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $this->items = $query->orderBy('id', 'desc')->get();

        $keys = [];
        foreach ($this->items as $item) {
            $properties = is_array($item->properties) ? $item->properties : json_decode($item->properties, true);
            if (is_array($properties)) {
                foreach (array_keys($properties) as $key) {
                    // Loại bỏ các cột legacy đã được tách thành cột riêng trong Database hoặc các biến hệ thống
                    // if (!in_array(strtoupper($key), ['ORDER_ID', 'PRODUCT_ID', 'PRODUCT', 'PRODUCT_NAME', 'ORDER_CODE', 'PO', 'GSM', 'WEIGHT', 'NOTES', 'GHI_CHU'])) {
                    //     $keys[$key] = true;
                    // }
                    $keys[$key] = true;
                }
            }
        }
        $this->dynamicKeys = array_keys($keys);
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        $headings = [
            'Mã Barcode',
            'Mã Kho',
            'Màu Sắc',
            'Loại Vải',
            'Khổ',
            'Loại Nhựa',
            'Máy',
            'Định Lượng (GSM)',
            'Mã Đơn Hàng',
            'Ghi Chú',
            'Số Mét',
            'Trọng Lượng',
            'Người Nhập Kho',
            'Ngày Nhập Kho',
            'Bộ phận',
            'Vị Trí Hiện Tại',
            'Loại (Type)',
            'Đứng máy',
            'Mã Sản Phẩm',
            'Ca làm việc'
        ];

        foreach ($this->dynamicKeys as $key) {
            $headings[] = $key;
        }

        return $headings;
    }

    public function map($item): array
    {
        $properties = is_array($item->properties) ? $item->properties : json_decode($item->properties, true);
        $properties = is_array($properties) ? $properties : [];

        $row = [
            $item->code,
            $item->warehouse_code,
            $item->color ? $item->color->code : '',
            $item->specification ? $item->specification->code : '',
            $item->width,
            $item->plasticType ? $item->plasticType->code : '',
            $item->machine ? $item->machine->code : '',
            $item->gsmlami ?? ($item->gsm ?? ($properties['GSM'] ?? ($properties['gsm'] ?? ''))),
            $item->order ? $item->order->code : '',
            $item->notes ?? ($properties['NOTES'] ?? ($properties['notes'] ?? ($properties['ghi_chu'] ?? ''))),
            $item->original_length,
            $item->weight ?? ($properties['WEIGHT'] ?? ($properties['weight'] ?? '')),
            $item->warehouser ? $item->warehouser->name : '',
            $item->warehoused_at ? clone $item->warehoused_at : '', // Sửa lỗi ngày tháng bằng cách parse trực tiếp object Carbon
            $item->department ? $item->department->code : '',
            $item->location ? $item->location->code : '',
            $item->type,
            $item->verifier ? $item->verifier->name : '',
            $item->product ? $item->product->code : '',
            $item->shift,
        ];

        foreach ($this->dynamicKeys as $key) {
            $row[] = $properties[$key] ?? '';
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'N' => 'dd/mm/yyyy hh:mm:ss', // Định dạng ngày tháng chuẩn Excel
        ];
    }
}
