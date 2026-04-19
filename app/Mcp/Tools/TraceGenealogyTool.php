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

#[Description('Truy vết phả hệ (genealogy) của một cuộn vải: xem toàn bộ cây cha (nguồn gốc nguyên liệu) và cây con (thành phẩm được tạo ra). Hữu ích để truy xuất nguồn gốc sản xuất.')]
#[IsReadOnly]
#[IsIdempotent]
class TraceGenealogyTool extends Tool
{
    public function handle(Request $request): Response
    {
        $item = null;

        if ($id = $request->get('id')) {
            $item = Item::find($id);
        } elseif ($code = $request->get('code')) {
            $item = Item::where('code', $code)->first();
        }

        if (!$item) {
            return Response::error('Không tìm thấy item. Vui lòng cung cấp ID hoặc mã code hợp lệ.');
        }

        $direction = $request->get('direction') ?? 'both';

        $result = [
            'item' => [
                'id' => $item->id,
                'code' => $item->code,
                'warehouse_code' => $item->warehouse_code,
                'product' => $item->product?->name,
                'status' => $item->status?->label(),
            ],
        ];

        if (in_array($direction, ['parents', 'both'])) {
            $item->load('allParents');
            $result['parents_tree'] = $this->buildTree($item->allParents, 'parents');
        }

        if (in_array($direction, ['children', 'both'])) {
            $item->load('allChildren');
            $result['children_tree'] = $this->buildTree($item->allChildren, 'children');
        }

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    private function buildTree($relatives, string $direction, int $depth = 0): array
    {
        if ($depth > 10) return []; // Tránh vòng lặp vô hạn

        $tree = [];
        foreach ($relatives as $rel) {
            $node = [
                'id' => $rel->id,
                'code' => $rel->code,
                'warehouse_code' => $rel->warehouse_code,
                'product' => $rel->product?->name,
                'department' => $rel->department?->name,
                'color' => $rel->color?->name ?? null,
                'machine' => $rel->machine?->name ?? null,
                'action_type' => $rel->pivot->action_type ?? null,
                'used_length' => $rel->pivot->used_length ?? null,
            ];

            // Load nested tree
            $nestedRelation = $direction === 'parents' ? 'allParents' : 'allChildren';
            if ($rel->relationLoaded($nestedRelation) && $rel->$nestedRelation->count() > 0) {
                $node[$direction] = $this->buildTree($rel->$nestedRelation, $direction, $depth + 1);
            }

            $tree[] = $node;
        }
        return $tree;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('ID của item cần truy vết phả hệ.'),
            'code' => $schema->string()
                ->description('Mã code của item (dùng khi không có ID).'),
            'direction' => $schema->string()
                ->enum(['parents', 'children', 'both'])
                ->description('Hướng truy vết: parents (nguồn gốc), children (thành phẩm), both (cả hai).')
                ->default('both'),
        ];
    }
}
