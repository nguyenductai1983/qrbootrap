<?php

namespace App\Mcp\Tools;

use App\Models\Item;
use App\Enums\ItemStatus;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tool;

#[Description('Tìm kiếm và lọc danh sách cuộn vải/item trong kho. Hỗ trợ lọc theo mã code, warehouse_code, trạng thái, sản phẩm, phòng ban, vị trí kho. Trả về danh sách item kèm thông tin liên quan.')]
#[IsReadOnly]
#[IsIdempotent]
class SearchItemsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $query = Item::with(['product', 'department', 'location', 'color', 'machine', 'creator', 'itemType']);

        if ($code = $request->get('code')) {
            $query->where('code', 'like', "%{$code}%");
        }

        if ($warehouseCode = $request->get('warehouse_code')) {
            $query->where('warehouse_code', 'like', "%{$warehouseCode}%");
        }

        if ($status = $request->get('status')) {
            $statusEnum = ItemStatus::tryFrom((int) $status);
            if ($statusEnum) {
                $query->where('status', $statusEnum);
            }
        }

        if ($productId = $request->get('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($departmentId = $request->get('department_id')) {
            $query->where('department_id', $departmentId);
        }

        if ($locationId = $request->get('location_id')) {
            $query->where('current_location_id', $locationId);
        }

        if ($machineId = $request->get('machine_id')) {
            $query->where('machine_id', $machineId);
        }

        $limit = min((int) ($request->get('limit') ?? 20), 100);
        $page = max((int) ($request->get('page') ?? 1), 1);

        $items = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $result = [
            'total' => $items->total(),
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'per_page' => $items->perPage(),
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'warehouse_code' => $item->warehouse_code,
                    'status' => $item->status?->label(),
                    'status_value' => $item->status?->value,
                    'product' => $item->product?->name,
                    'product_code' => $item->product?->code,
                    'department' => $item->department?->name,
                    'location' => $item->location?->name,
                    'location_code' => $item->location?->code,
                    'color' => $item->color?->name ?? null,
                    'machine' => $item->machine?->name ?? null,
                    'width' => $item->width,
                    'length' => $item->length,
                    'original_length' => $item->original_length,
                    'gsm' => $item->gsm,
                    'weight' => $item->weight,
                    'weight_original' => $item->weight_original,
                    'shift' => $item->shift,
                    'notes' => $item->notes,
                    'created_by' => $item->creator?->name,
                    'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                    'warehoused_at' => $item->warehoused_at?->format('Y-m-d H:i:s'),
                ];
            })->values()->toArray(),
        ];

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'code' => $schema->string()
                ->description('Tìm theo mã code cuộn vải (tìm kiếm gần đúng).'),
            'warehouse_code' => $schema->string()
                ->description('Tìm theo mã kho warehouse_code (tìm kiếm gần đúng).'),
            'status' => $schema->string()
                ->enum(['0', '1', '2', '3'])
                ->description('Lọc theo trạng thái: 0=Chưa SX, 1=Đã SX, 2=Đã nhập kho, 3=Hoàn kho.'),
            'product_id' => $schema->integer()
                ->description('Lọc theo ID sản phẩm (product).'),
            'department_id' => $schema->integer()
                ->description('Lọc theo ID phòng ban/bộ phận sản xuất.'),
            'location_id' => $schema->integer()
                ->description('Lọc theo ID vị trí kho hiện tại.'),
            'machine_id' => $schema->integer()
                ->description('Lọc theo ID máy sản xuất.'),
            'limit' => $schema->integer()
                ->description('Số lượng kết quả mỗi trang (mặc định 20, tối đa 100).')
                ->default(20),
            'page' => $schema->integer()
                ->description('Trang kết quả (mặc định 1).')
                ->default(1),
        ];
    }
}
