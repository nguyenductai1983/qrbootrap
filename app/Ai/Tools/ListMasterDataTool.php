<?php

namespace App\Ai\Tools;

use App\Models\Department;
use App\Models\Product;
use App\Models\Location;
use App\Models\Machine;
use App\Enums\ItemStatus;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListMasterDataTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Liệt kê dữ liệu danh mục hệ thống: departments (phòng ban), products (sản phẩm), locations (vị trí kho), machines (máy). Dùng để lấy ID khi cần filter.';
    }

    public function handle(Request $request): Stringable|string
    {
        $type = $request['type'];

        $data = match ($type) {
            'departments' => Department::select('id', 'code', 'name')->get()->toArray(),
            'products' => Product::select('id', 'code', 'name')->get()->toArray(),
            'locations' => Location::select('id', 'code', 'name')->get()->toArray(),
            'machines' => Machine::select('id', 'code', 'name', 'department_id')
                ->with('department:id,name')->get()
                ->map(fn($m) => ['id' => $m->id, 'code' => $m->code, 'name' => $m->name, 'dept' => $m->department?->name])
                ->toArray(),
            default => ['error' => 'Type phải là: departments, products, locations, machines'],
        };

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()
                ->enum(['departments', 'products', 'locations', 'machines'])
                ->description('Loại danh mục: departments, products, locations, machines')
                ->required(),
        ];
    }
}
