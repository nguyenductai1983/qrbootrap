<?php

namespace App\Mcp\Tools;

use App\Models\Item;
use App\Enums\ItemStatus;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tool;

#[Description('Thống kê tổng quan kho hàng: tổng số item theo trạng thái, theo phòng ban, theo sản phẩm, theo vị trí. Hữu ích để nắm bắt tình hình tồn kho nhanh chóng.')]
#[IsReadOnly]
#[IsIdempotent]
class WarehouseStatsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $groupBy = $request->get('group_by') ?? 'status';

        $result = [];

        // Tổng quan chung
        $result['overview'] = [
            'total_items' => Item::count(),
            'by_status' => [
                'Chưa SX' => Item::where('status', ItemStatus::NONE)->count(),
                'Đã SX' => Item::where('status', ItemStatus::VERIFIED)->count(),
                'Đã nhập kho' => Item::where('status', ItemStatus::IN_WAREHOUSE)->count(),
                'Hoàn kho' => Item::where('status', ItemStatus::SURPLUS_ENTRY)->count(),
            ],
        ];

        switch ($groupBy) {
            case 'department':
                $result['grouped'] = Item::select('department_id', DB::raw('count(*) as total'))
                    ->with('department:id,name,code')
                    ->groupBy('department_id')
                    ->get()
                    ->map(fn($row) => [
                        'department' => $row->department?->name ?? 'Chưa phân bổ',
                        'department_code' => $row->department?->code ?? '-',
                        'total' => $row->total,
                    ])
                    ->values()
                    ->toArray();
                break;

            case 'product':
                $result['grouped'] = Item::select('product_id', DB::raw('count(*) as total'))
                    ->with('product:id,name,code')
                    ->groupBy('product_id')
                    ->get()
                    ->map(fn($row) => [
                        'product' => $row->product?->name ?? 'Chưa gán',
                        'product_code' => $row->product?->code ?? '-',
                        'total' => $row->total,
                    ])
                    ->values()
                    ->toArray();
                break;

            case 'location':
                $result['grouped'] = Item::select('current_location_id', DB::raw('count(*) as total'))
                    ->with('location:id,name,code')
                    ->groupBy('current_location_id')
                    ->get()
                    ->map(fn($row) => [
                        'location' => $row->location?->name ?? 'Chưa xác định',
                        'location_code' => $row->location?->code ?? '-',
                        'total' => $row->total,
                    ])
                    ->values()
                    ->toArray();
                break;

            case 'machine':
                $result['grouped'] = Item::select('machine_id', DB::raw('count(*) as total'))
                    ->with('machine:id,name,code')
                    ->groupBy('machine_id')
                    ->get()
                    ->map(fn($row) => [
                        'machine' => $row->machine?->name ?? 'Chưa gán',
                        'machine_code' => $row->machine?->code ?? '-',
                        'total' => $row->total,
                    ])
                    ->values()
                    ->toArray();
                break;

            default: // status (đã có trong overview)
                $result['grouped'] = collect(ItemStatus::cases())->map(fn($s) => [
                    'status' => $s->label(),
                    'value' => $s->value,
                    'total' => Item::where('status', $s)->count(),
                ])->toArray();
                break;
        }

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'group_by' => $schema->string()
                ->enum(['status', 'department', 'product', 'location', 'machine'])
                ->description('Nhóm thống kê theo: status (trạng thái), department (phòng ban), product (sản phẩm), location (vị trí kho), machine (máy).')
                ->default('status'),
        ];
    }
}
