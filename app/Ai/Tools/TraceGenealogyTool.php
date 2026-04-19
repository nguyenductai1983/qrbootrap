<?php

namespace App\Ai\Tools;

use App\Models\Item;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class TraceGenealogyTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Truy vết phả hệ nguồn gốc cuộn vải: xem cây cha (nguyên liệu gốc) và cây con (thành phẩm). Dùng để truy xuất nguồn gốc sản xuất.';
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
            return 'Không tìm thấy item.';
        }

        $direction = $request['direction'] ?? 'both';
        $result = [
            'item' => $item->code . ' (' . $item->warehouse_code . ') - ' . ($item->product?->name ?? ''),
        ];

        if (in_array($direction, ['parents', 'both'])) {
            $item->load('allParents');
            $result['parents'] = $this->flattenTree($item->allParents, 'allParents');
        }
        if (in_array($direction, ['children', 'both'])) {
            $item->load('allChildren');
            $result['children'] = $this->flattenTree($item->allChildren, 'allChildren');
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    private function flattenTree($items, string $relation, int $depth = 0): array
    {
        if ($depth > 5) return [];
        $nodes = [];
        foreach ($items as $item) {
            $prefix = str_repeat('  ', $depth) . ($depth > 0 ? '└─ ' : '');
            $nodes[] = $prefix . $item->code . ' (' . ($item->product?->name ?? '') . ', ' . ($item->department?->name ?? '') . ')';
            if ($item->relationLoaded($relation) && $item->$relation->count() > 0) {
                $nodes = array_merge($nodes, $this->flattenTree($item->$relation, $relation, $depth + 1));
            }
        }
        return $nodes;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('ID item cần truy vết'),
            'code' => $schema->string()->description('Mã code item'),
            'direction' => $schema->string()->enum(['parents', 'children', 'both'])->description('Hướng: parents, children, both'),
        ];
    }
}
