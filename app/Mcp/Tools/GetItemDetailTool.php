<?php

namespace App\Mcp\Tools;

use App\Models\Item;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tool;

#[Description('Lấy thông tin chi tiết một cuộn vải/item theo ID hoặc mã code. Bao gồm thông tin sản phẩm, Bộ phận, vị trí, lịch sử thay đổi, và phả hệ (parents/children).')]
#[IsReadOnly]
#[IsIdempotent]
class GetItemDetailTool extends Tool
{
    public function handle(Request $request): Response
    {
        $item = null;

        if ($id = $request->get('id')) {
            $item = Item::with([
                'product',
                'department',
                'location',
                'color',
                'specification',
                'plasticType',
                'machine',
                'creator',
                'verifier',
                'warehouser',
                'itemType',
                'order',
                'parents.product',
                'parents.department',
                'children.product',
                'children.department',
                'histories.user',
                'movements.fromLocation',
                'movements.toLocation',
                'movements.user',
            ])->find($id);
        } elseif ($code = $request->get('code')) {
            $item = Item::with([
                'product',
                'department',
                'location',
                'color',
                'specification',
                'plasticType',
                'machine',
                'creator',
                'verifier',
                'warehouser',
                'itemType',
                'order',
                'parents.product',
                'parents.department',
                'children.product',
                'children.department',
                'histories.user',
                'movements.fromLocation',
                'movements.toLocation',
                'movements.user',
            ])->where('code', $code)->first();
        }

        if (!$item) {
            return Response::error('Không tìm thấy item. Vui lòng kiểm tra lại ID hoặc mã code.');
        }

        $result = [
            'id' => $item->id,
            'code' => $item->code,
            'warehouse_code' => $item->warehouse_code,
            'type' => $item->itemType?->name,
            'status' => $item->status?->label(),
            'status_value' => $item->status?->value,
            'product' => $item->product ? [
                'id' => $item->product->id,
                'code' => $item->product->code,
                'name' => $item->product->name,
            ] : null,
            'department' => $item->department ? [
                'id' => $item->department->id,
                'code' => $item->department->code,
                'name' => $item->department->name,
            ] : null,
            'location' => $item->location ? [
                'id' => $item->location->id,
                'code' => $item->location->code,
                'name' => $item->location->name,
            ] : null,
            'order' => $item->order ? [
                'id' => $item->order->id,
                'code' => $item->order->code,
                'customer_name' => $item->order->customer_name,
            ] : null,
            'color' => $item->color?->name,
            'specification' => $item->specification?->name ?? null,
            'plastic_type' => $item->plasticType?->name ?? null,
            'machine' => $item->machine ? [
                'id' => $item->machine->id,
                'code' => $item->machine->code,
                'name' => $item->machine->name,
            ] : null,
            'measurements' => [
                'width' => $item->width,
                'width_original' => $item->width_original,
                'length' => $item->length,
                'original_length' => $item->original_length,
                'gsm' => $item->gsm,
                'weight' => $item->weight,
                'weight_original' => $item->weight_original,
                'lami' => $item->lami,
            ],
            'shift' => $item->shift,
            'notes' => $item->notes,
            'created_by' => $item->creator?->name,
            'verified_by' => $item->verifier?->name,
            'verified_at' => $item->verified_at?->format('Y-m-d H:i:s'),
            'warehoused_by' => $item->warehouser?->name,
            'warehoused_at' => $item->warehoused_at?->format('Y-m-d H:i:s'),
            'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
            'parents' => $item->parents->map(fn($p) => [
                'id' => $p->id,
                'code' => $p->code,
                'warehouse_code' => $p->warehouse_code,
                'product' => $p->product?->name,
                'department' => $p->department?->name,
                'action_type' => $p->pivot->action_type,
                'used_length' => $p->pivot->used_length,
            ])->values()->toArray(),
            'children' => $item->children->map(fn($c) => [
                'id' => $c->id,
                'code' => $c->code,
                'warehouse_code' => $c->warehouse_code,
                'product' => $c->product?->name,
                'department' => $c->department?->name,
                'action_type' => $c->pivot->action_type,
                'used_length' => $c->pivot->used_length,
            ])->values()->toArray(),
            'histories' => $item->histories->take(20)->map(fn($h) => [
                'field' => $h->field_name,
                'old_value' => $h->old_value,
                'new_value' => $h->new_value,
                'changed_by' => $h->user?->name,
                'changed_at' => $h->created_at?->format('Y-m-d H:i:s'),
            ])->values()->toArray(),
            'movements' => $item->movements->take(20)->map(fn($m) => [
                'action' => $m->action_type?->label(),
                'from' => $m->fromLocation?->name,
                'to' => $m->toLocation?->name,
                'user' => $m->user?->name,
                'note' => $m->note,
                'created_at' => $m->created_at?->format('Y-m-d H:i:s'),
            ])->values()->toArray(),
        ];

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('ID của item cần xem chi tiết.'),
            'code' => $schema->string()
                ->description('Mã code chính xác của item (dùng khi không có ID).'),
        ];
    }
}
