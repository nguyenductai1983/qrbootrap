<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $items;
    protected $dynamicKeys = [];

    public function __construct($orderId = null, $productId = null, $fromDate = null, $toDate = null)
    {
        $query = \App\Models\Item::query();

        if ($orderId !== null && $orderId !== '') {
            $query->where('order_id', $orderId);
        }
        if ($productId !== null && $productId !== '') {
            $query->where('product_id', $productId);
        }
        if ($fromDate !== null && $fromDate !== '' && $toDate !== null && $toDate !== '') {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $this->items = $query->get();

        // Lấy tất cả các keys từ properties để tạo dynamic columns, tương tự ItemsImport quét qua các keys lạ
        $keys = [];
        foreach ($this->items as $item) {
            $properties = is_array($item->properties) ? $item->properties : json_decode($item->properties, true);
            if (is_array($properties)) {
                foreach (array_keys($properties) as $key) {
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
        // Các cột bắt buộc tương thích với ItemsImport
        $headings = [
            'code',
            'original_length',
            'length',
            'gsm',
            'weight',
        ];

        // Lấy thêm các dynamic properties
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
            $item->original_length,
            $item->length,
            $item->gsm,
            $item->weight,
        ];

        // Đổ dữ liệu vào các cột tương ứng với properties
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
}
