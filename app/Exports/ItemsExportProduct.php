<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class ItemsExportProduct implements WithMultipleSheets
{
    /**
     * 
     * @var Collection
     */
    protected $items;

    public function __construct($orderId = null, $productId = null, $fromDate = null, $toDate = null)
    {
        if ($orderId instanceof \Illuminate\Support\Collection) {
            // Called from ItemManager with a ready-made collection (already has parents loaded)
            $this->items = $orderId;
        } else {
            // Called from ExcelManager with IDs → query internally
            $query = \App\Models\Item::with(['order', 'product', 'color', 'verifier', 'parents', 'shift']);

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
        }
    }

    public function sheets(): array
    {
        return [
            new ItemsExportProductMainSheet($this->items),
        ];
    }
}
