<?php

namespace App\Ai\Tools;

use App\Models\Item;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetItemDetailTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Lấy thông tin chi tiết một cuộn vải theo ID hoặc mã code. Bao gồm thông tin sản phẩm, vị trí, phả hệ cha/con, lịch sử thay đổi.';
    }

    public function handle(Request $request): Stringable|string
    {
        $item = null;
        if ($id = $request['id'] ?? null) {
            $item = Item::find($id);
        } elseif ($code = $request['code'] ?? null) {
            $item = Item::where('code', $code)->first();
        }

        if (!$item) {
            return 'Không tìm thấy item. Vui lòng kiểm tra lại ID hoặc mã code.';
        }

        $item->load([
            'product:id,code,name', 'department:id,code,name', 'location:id,code,name',
            'color:id,name', 'machine:id,code,name', 'creator:id,name', 'order:id,code,customer_name',
            'parents:id,code,warehouse_code', 'children:id,code,warehouse_code',
            'histories' => fn($q) => $q->with('user:id,name')->latest()->limit(10),
        ]);

        $result = [
            'id' => $item->id,
            'code' => $item->code,
            'warehouse_code' => $item->warehouse_code,
            'status' => $item->status?->label(),
            'product' => $item->product?->name,
            'department' => $item->department?->name,
            'location' => $item->location?->name . ' (' . $item->location?->code . ')',
            'order' => $item->order ? $item->order->code . ' - ' . $item->order->customer_name : null,
            'color' => $item->color?->name,
            'machine' => $item->machine?->name,
            'width' => $item->width, 'length' => $item->length,
            'original_length' => $item->original_length,
            'gsm' => $item->gsm, 'weight' => $item->weight,
            'shift' => $item->shift, 'notes' => $item->notes,
            'created_by' => $item->creator?->name,
            'created_at' => $item->created_at?->format('d/m/Y H:i'),
            'warehoused_at' => $item->warehoused_at?->format('d/m/Y H:i'),
            'parents' => $item->parents->map(fn($p) => $p->code . ' (' . $p->warehouse_code . ')')->implode(', '),
            'children' => $item->children->map(fn($c) => $c->code . ' (' . $c->warehouse_code . ')')->implode(', '),
            'recent_changes' => $item->histories->map(fn($h) => [
                'field' => $h->field_name, 'old' => $h->old_value, 'new' => $h->new_value,
                'by' => $h->user?->name, 'at' => $h->created_at?->format('d/m H:i'),
            ])->toArray(),
        ];

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('ID của item'),
            'code' => $schema->string()->description('Mã code chính xác của item'),
        ];
    }
}
