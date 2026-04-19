<?php

namespace App\Mcp\Tools;

use App\Models\Location;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tool;

#[Description('Xem tồn kho theo vị trí: liệt kê tất cả các cuộn vải đang nằm tại một vị trí kho cụ thể, hoặc tổng hợp tồn kho tất cả các vị trí.')]
#[IsReadOnly]
#[IsIdempotent]
class LocationInventoryTool extends Tool
{
    public function handle(Request $request): Response
    {
        if ($locationId = $request->get('location_id')) {
            $location = Location::with([
                'items' => function ($q) {
                    $q->with(['product:id,code,name', 'color', 'department:id,name'])
                      ->orderBy('created_at', 'desc');
                }
            ])->find($locationId);

            if (!$location) {
                return Response::error('Không tìm thấy vị trí kho với ID này.');
            }

            $result = [
                'location' => [
                    'id' => $location->id,
                    'code' => $location->code,
                    'name' => $location->name,
                    'type' => $location->type,
                ],
                'total_items' => $location->items->count(),
                'items' => $location->items->map(fn($item) => [
                    'id' => $item->id,
                    'code' => $item->code,
                    'warehouse_code' => $item->warehouse_code,
                    'product' => $item->product?->name,
                    'color' => $item->color?->name,
                    'department' => $item->department?->name,
                    'weight' => $item->weight,
                    'length' => $item->length,
                    'status' => $item->status?->label(),
                ])->values()->toArray(),
            ];
        } elseif ($locationCode = $request->get('location_code')) {
            $location = Location::with([
                'items' => function ($q) {
                    $q->with(['product:id,code,name', 'color', 'department:id,name'])
                      ->orderBy('created_at', 'desc');
                }
            ])->where('code', $locationCode)->first();

            if (!$location) {
                return Response::error('Không tìm thấy vị trí kho với mã này.');
            }

            $result = [
                'location' => [
                    'id' => $location->id,
                    'code' => $location->code,
                    'name' => $location->name,
                    'type' => $location->type,
                ],
                'total_items' => $location->items->count(),
                'items' => $location->items->map(fn($item) => [
                    'id' => $item->id,
                    'code' => $item->code,
                    'warehouse_code' => $item->warehouse_code,
                    'product' => $item->product?->name,
                    'color' => $item->color?->name,
                    'department' => $item->department?->name,
                    'weight' => $item->weight,
                    'length' => $item->length,
                    'status' => $item->status?->label(),
                ])->values()->toArray(),
            ];
        } else {
            // Tổng hợp tất cả vị trí
            $locations = Location::withCount('items')
                ->orderBy('items_count', 'desc')
                ->get();

            $result = [
                'total_locations' => $locations->count(),
                'locations' => $locations->map(fn($loc) => [
                    'id' => $loc->id,
                    'code' => $loc->code,
                    'name' => $loc->name,
                    'type' => $loc->type,
                    'items_count' => $loc->items_count,
                ])->values()->toArray(),
            ];
        }

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'location_id' => $schema->integer()
                ->description('ID vị trí kho cần xem tồn kho chi tiết.'),
            'location_code' => $schema->string()
                ->description('Mã vị trí kho (dùng khi không có ID). Nếu không truyền cả 2, sẽ liệt kê tổng hợp tất cả vị trí.'),
        ];
    }
}
