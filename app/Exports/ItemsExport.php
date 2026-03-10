<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $items;
    protected $dynamicKeys = [];

    public function __construct($items)
    {
        $this->items = $items;

        $keys = [];
        foreach ($items as $item) {
            $properties = is_array($item->properties) ? $item->properties : json_decode($item->properties, true);
            if (is_array($properties)) {
                foreach (array_keys($properties) as $key) {
                    if (!in_array($key, ['ORDER_ID', 'PRODUCT_ID', 'PRODUCT', 'PRODUCT_NAME'])) {
                        $keys[$key] = true;
                    }
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
            'Mã Tem',
            'Đơn hàng',
            'Sản phẩm',
            'Màu',
            'Trạng thái',
            'Vị trí hiện tại'
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
            $item->order->code ?? '-',
            $item->product->name ?? '-',
            $item->color->name ?? '-',
            $item->status ? $item->status->label() : '-',
            '-', // Vị trí hiện tại
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
}
