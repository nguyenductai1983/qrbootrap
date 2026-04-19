<?php

namespace App\Ai\Tools;

use App\Models\Item;
use App\Enums\ItemStatus;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class WarehouseStatsTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Thống kê tổng quan kho hàng: tổng số item theo trạng thái, theo Bộ phận, theo sản phẩm, theo vị trí kho.';
    }

    public function handle(Request $request): Stringable|string
    {
        $groupBy = $request['group_by'] ?? 'status';

        $result = [
            'overview' => [
                'total' => Item::count(),
                'Chưa SX' => Item::where('status', ItemStatus::NONE)->count(),
                'Đã SX' => Item::where('status', ItemStatus::VERIFIED)->count(),
                'Đã nhập kho' => Item::where('status', ItemStatus::IN_WAREHOUSE)->count(),
                'Hoàn kho' => Item::where('status', ItemStatus::SURPLUS_ENTRY)->count(),
            ],
        ];

        if ($groupBy === 'department') {
            $result['by_department'] = Item::select('department_id', DB::raw('count(*) as total, COALESCE(SUM(weight),0) as total_weight'))
                ->with('department:id,name')
                ->groupBy('department_id')->get()
                ->map(fn($r) => ['department' => $r->department?->name ?? '-', 'total' => $r->total, 'weight' => round($r->total_weight, 1)])
                ->toArray();
        } elseif ($groupBy === 'product') {
            $result['by_product'] = Item::select('product_id', DB::raw('count(*) as total, COALESCE(SUM(weight),0) as total_weight'))
                ->with('product:id,name')
                ->groupBy('product_id')->get()
                ->map(fn($r) => ['product' => $r->product?->name ?? '-', 'total' => $r->total, 'weight' => round($r->total_weight, 1)])
                ->toArray();
        } elseif ($groupBy === 'location') {
            $result['by_location'] = Item::select('current_location_id', DB::raw('count(*) as total'))
                ->with('location:id,name,code')
                ->groupBy('current_location_id')->get()
                ->map(fn($r) => ['location' => $r->location?->name ?? '-', 'code' => $r->location?->code ?? '-', 'total' => $r->total])
                ->toArray();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'group_by' => $schema->string()
                ->enum(['status', 'department', 'product', 'location'])
                ->description('Nhóm thống kê: status, department, product, location'),
        ];
    }
}
