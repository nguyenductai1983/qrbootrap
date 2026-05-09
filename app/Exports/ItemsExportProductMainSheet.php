<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExportProductMainSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    /**
     * 
     * @var mixed
     */
    protected $items;
    protected $dynamicKeys = [];
    /**
     * 
     * @param mixed $items 
     * @return void 
     */

    public function __construct($items)
    {
        $this->items = $items;

        // Thu thập tất cả dynamic keys từ properties
        $keys = [];
        foreach ($this->items as $item) {
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

    public function title(): string
    {
        return 'Danh sách Cuộn Vải';
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        $headings = [
            'Mã Tem (Code)',
            'Ngày tạo',
            'Người xác nhận',
            'Thời gian xác nhận',
            'Đơn hàng',
            'Sản phẩm',
            'Màu sắc',
            'Trạng thái',
            'Dài Gốc (m)',
            'Dài Còn (m)',
            'GSM',
            'Trọng lượng (kg)',
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
            $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '',
            $item->verifier->username ?? '',
            $item->verified_at ? $item->verified_at->format('Y-m-d H:i:s') : '',
            $item->order->code ?? '',
            $item->product->name ?? '',
            $item->color->name ?? '',
            $item->status?->label() ?? '',
            $item->original_length,
            $item->length,
            $item->gsm,
            $item->weight,
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
