<?php

namespace App\Ai\Tools;

use App\Models\Item;
use App\Enums\ItemStatus;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchItemsTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Tìm kiếm cuộn vải/item trong kho. Hỗ trợ lọc theo mã code, warehouse_code, trạng thái (0=Chưa SX, 1=Đã SX, 2=Đã nhập kho, 3=Hoàn kho), product_id, department_id, location_id.';
    }

    public function handle(Request $request): Stringable|string
    {
        $query = Item::with(['product:id,code,name', 'department:id,name', 'location:id,code,name', 'color:id,name']);

        if ($code = $request['code'] ?? null) {
            $query->where('code', 'like', "%{$code}%");
        }
        if ($wc = $request['warehouse_code'] ?? null) {
            $query->where('warehouse_code', 'like', "%{$wc}%");
        }
        if (isset($request['status'])) {
            $query->where('status', (int) $request['status']);
        }
        if ($pid = $request['product_id'] ?? null) {
            $query->where('product_id', $pid);
        }
        if ($did = $request['department_id'] ?? null) {
            $query->where('department_id', $did);
        }
        if ($lid = $request['location_id'] ?? null) {
            $query->where('current_location_id', $lid);
        }

        $limit = min((int) ($request['limit'] ?? 15), 50);
        $items = $query->orderBy('created_at', 'desc')->limit($limit)->get();

        if ($items->isEmpty()) {
            return 'Không tìm thấy cuộn vải nào phù hợp với tiêu chí tìm kiếm.';
        }

        $rows = $items->map(fn($i) => [
            'id' => $i->id,
            'code' => $i->code,
            'warehouse_code' => $i->warehouse_code,
            'status' => $i->status?->label(),
            'product' => $i->product?->name,
            'department' => $i->department?->name,
            'location' => $i->location?->name,
            'weight' => $i->weight,
            'length' => $i->length,
            'created_at' => $i->created_at?->format('d/m/Y H:i'),
        ]);

        return json_encode(['total_found' => $items->count(), 'items' => $rows], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'code' => $schema->string()->description('Mã code cuộn vải (tìm gần đúng)'),
            'warehouse_code' => $schema->string()->description('Mã kho warehouse_code (tìm gần đúng)'),
            'status' => $schema->integer()->description('Trạng thái: 0=Chưa SX, 1=Đã SX, 2=Đã nhập kho, 3=Hoàn kho'),
            'product_id' => $schema->integer()->description('ID sản phẩm'),
            'department_id' => $schema->integer()->description('ID phòng ban'),
            'location_id' => $schema->integer()->description('ID vị trí kho'),
            'limit' => $schema->integer()->description('Số kết quả tối đa (mặc định 15, tối đa 50)'),
        ];
    }
}
