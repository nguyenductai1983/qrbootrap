<?php

namespace App\Ai\Tools;

use App\Models\Item;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ProductionReportTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Báo cáo sản xuất theo khoảng thời gian: tổng cuộn vải, phân bổ theo Bộ phận, máy, sản phẩm, ca, ngày.';
    }

    public function handle(Request $request): Stringable|string
    {
        $from = $request['date_from'] ?? now()->subDays(7)->format('Y-m-d');
        $to = $request['date_to'] ?? now()->format('Y-m-d');

        $query = Item::whereBetween('created_at', [$from, $to . ' 23:59:59']);

        if ($did = $request['department_id'] ?? null) {
            $query->where('department_id', $did);
        }

        $result = [
            'period' => "$from → $to",
            'total_items' => (clone $query)->count(),
            'total_weight' => round((clone $query)->sum('weight'), 1),
            'by_department' => (clone $query)
                ->select('department_id', DB::raw('count(*) as total, COALESCE(SUM(weight),0) as weight'))
                ->with('department:id,name')
                ->groupBy('department_id')->get()
                ->map(fn($r) => ['dept' => $r->department?->name ?? '-', 'qty' => $r->total, 'kg' => round($r->weight, 1)])
                ->toArray(),
            'by_date' => (clone $query)
                ->select(DB::raw('DATE(created_at) as date, count(*) as total, COALESCE(SUM(weight),0) as weight'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')->get()
                ->map(fn($r) => ['date' => $r->date, 'qty' => $r->total, 'kg' => round($r->weight, 1)])
                ->toArray(),
        ];

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'date_from' => $schema->string()->description('Ngày bắt đầu YYYY-MM-DD (mặc định 7 ngày trước)'),
            'date_to' => $schema->string()->description('Ngày kết thúc YYYY-MM-DD (mặc định hôm nay)'),
            'department_id' => $schema->integer()->description('Lọc theo Bộ phận (tùy chọn)'),
        ];
    }
}
