<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Item;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $items;
    protected $dynamicKeys = [];

    /**
     * @param  \Illuminate\Support\Collection|string  $itemsOrOrderId  Either a ready-made collection or an order ID
     * @param  int|null  $modelId  Product ID — required when $itemsOrOrderId is an order ID
     */
    public function __construct($itemsOrOrderId, $modelId = null)
    {
        if ($modelId !== null) {
            // Called from ExcelManager with two IDs → query internally
            $this->items = Item::with(['order', 'product', 'color'])
                ->where('order_id', $itemsOrOrderId)
                ->where('product_id', $modelId)
                ->get();
        } else {
            // Called from ItemManager with a ready-made collection
            $this->items = $itemsOrOrderId;
        }

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

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        $headings = [
            'code',
            'original_length',
            'length',
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
            $item->original_length,
            $item->length,
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
