<?php

namespace App\Mcp\Tools;

use App\Models\ItemMovement;
use App\Enums\MovementAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tool;

#[Description('Xem lịch sử di chuyển và hoạt động của các cuộn vải. Lọc theo item, loại hành động, khoảng thời gian. Phục vụ kiểm tra truy xuất và audit.')]
#[IsReadOnly]
#[IsIdempotent]
class GetMovementHistoryTool extends Tool
{
    public function handle(Request $request): Response
    {
        $query = ItemMovement::with([
            'item:id,code,warehouse_code',
            'fromLocation:id,code,name',
            'toLocation:id,code,name',
            'user:id,name',
        ]);

        if ($itemId = $request->get('item_id')) {
            $query->where('item_id', $itemId);
        }

        if ($actionType = $request->get('action_type')) {
            $action = MovementAction::tryFrom((int) $actionType);
            if ($action) {
                $query->where('action_type', $action);
            }
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        $limit = min((int) ($request->get('limit') ?? 30), 100);

        $movements = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $result = [
            'total_returned' => $movements->count(),
            'movements' => $movements->map(fn($m) => [
                'id' => $m->id,
                'item_id' => $m->item_id,
                'item_code' => $m->item?->code,
                'warehouse_code' => $m->item?->warehouse_code,
                'action' => $m->action_type?->label(),
                'action_value' => $m->action_type?->value,
                'from_location' => $m->fromLocation?->name,
                'to_location' => $m->toLocation?->name,
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
            'item_id' => $schema->integer()
                ->description('Lọc lịch sử theo ID item cụ thể.'),
            'action_type' => $schema->string()
                ->enum(['1', '2', '3', '4', '5', '6'])
                ->description('Lọc theo loại hành động: 1=Nhập kho, 2=Xuất kho, 3=Xác nhận vị trí, 4=Chuyển vị trí, 5=Cập nhật trọng lượng, 6=Tái nhập dư.'),
            'date_from' => $schema->string()
                ->description('Lọc từ ngày (format: YYYY-MM-DD).'),
            'date_to' => $schema->string()
                ->description('Lọc đến ngày (format: YYYY-MM-DD).'),
            'limit' => $schema->integer()
                ->description('Số kết quả tối đa (mặc định 30, tối đa 100).')
                ->default(30),
        ];
    }
}
