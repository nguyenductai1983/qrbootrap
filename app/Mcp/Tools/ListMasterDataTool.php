<?php

namespace App\Mcp\Tools;

use App\Models\Department;
use App\Models\Product;
use App\Models\Location;
use App\Models\Machine;
use App\Models\Color;
use App\Models\Specification;
use App\Models\PlasticType;
use App\Models\ItemType;
use App\Models\Order;
use App\Enums\ItemStatus;
use App\Enums\MovementAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tool;

#[Description('Liệt kê dữ liệu danh mục (master data) của hệ thống: Bộ phận, sản phẩm, vị trí kho, máy móc, màu sắc, quy cách, loại nhựa, loại vải, đơn hàng, enum trạng thái. Dùng để lấy ID khi cần filter hoặc tạo item mới.')]
#[IsReadOnly]
#[IsIdempotent]
class ListMasterDataTool extends Tool
{
    public function handle(Request $request): Response
    {
        $type = $request->get('type');

        $result = match ($type) {
            'departments' => Department::select('id', 'code', 'name', 'is_warehouse')
                ->get()->toArray(),

            'products' => Product::select('id', 'code', 'name', 'description')
                ->get()->toArray(),

            'locations' => Location::select('id', 'code', 'name', 'type')
                ->get()->toArray(),

            'machines' => Machine::with('department:id,name')
                ->select('id', 'code', 'name', 'department_id', 'status')
                ->get()
                ->map(fn($m) => [
                    'id' => $m->id,
                    'code' => $m->code,
                    'name' => $m->name,
                    'status' => $m->status,
                    'department' => $m->department?->name,
                ])
                ->toArray(),

            'colors' => Color::select('id', 'name')->get()->toArray(),

            'specifications' => Specification::select('id', 'name')->get()->toArray(),

            'plastic_types' => PlasticType::select('id', 'name')->get()->toArray(),

            'item_types' => ItemType::select('id', 'name')->get()->toArray(),

            'orders' => Order::select('id', 'code', 'status', 'type', 'customer_name')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(fn($o) => [
                    'id' => $o->id,
                    'code' => $o->code,
                    'status' => $o->status?->label() ?? $o->status,
                    'type' => $o->type?->label() ?? $o->type,
                    'customer_name' => $o->customer_name,
                ])
                ->toArray(),

            'enums' => [
                'item_status' => collect(ItemStatus::cases())->map(fn($s) => [
                    'value' => $s->value,
                    'label' => $s->label(),
                ])->toArray(),
                'movement_action' => collect(MovementAction::cases())->map(fn($a) => [
                    'value' => $a->value,
                    'label' => $a->label(),
                ])->toArray(),
            ],

            default => [
                'available_types' => [
                    'departments',
                    'products',
                    'locations',
                    'machines',
                    'colors',
                    'specifications',
                    'plastic_types',
                    'item_types',
                    'orders',
                    'enums',
                ],
                'hint' => 'Vui lòng chỉ định type cần liệt kê.',
            ],
        };

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()
                ->enum([
                    'departments',
                    'products',
                    'locations',
                    'machines',
                    'colors',
                    'specifications',
                    'plastic_types',
                    'item_types',
                    'orders',
                    'enums',
                ])
                ->description('Loại danh mục cần liệt kê: departments, products, locations, machines, colors, specifications, plastic_types, item_types, orders, enums.')
                ->required(),
        ];
    }
}
