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

#[Description('Báo cáo sản xuất theo khoảng thời gian: tổng số cuộn vải được tạo, phân bổ theo Bộ phận, máy, sản phẩm, ca sản xuất. Hỗ trợ đánh giá năng suất và lập kế hoạch.')]
#[IsReadOnly]
#[IsIdempotent]
class ProductionReportTool extends Tool
{
    public function handle(Request $request): Response
    {
        $dateFrom = $request->get('date_from') ?? now()->subDays(7)->format('Y-m-d');
        $dateTo = $request->get('date_to') ?? now()->format('Y-m-d');

        $query = Item::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);

        if ($departmentId = $request->get('department_id')) {
            $query->where('department_id', $departmentId);
        }

        $result = [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'summary' => [
                'total_items' => (clone $query)->count(),
                'total_weight' => round((clone $query)->sum('weight'), 2),
                'total_length' => round((clone $query)->sum('length'), 2),
            ],
            'by_department' => (clone $query)
                ->select('department_id', DB::raw('count(*) as total'), DB::raw('COALESCE(SUM(weight), 0) as total_weight'))
                ->with('department:id,name,code')
                ->groupBy('department_id')
                ->get()
                ->map(fn($row) => [
                    'department' => $row->department?->name ?? 'Chưa phân bổ',
                    'total' => $row->total,
                    'total_weight' => round($row->total_weight, 2),
                ])
                ->values()
                ->toArray(),
            'by_product' => (clone $query)
                ->select('product_id', DB::raw('count(*) as total'), DB::raw('COALESCE(SUM(weight), 0) as total_weight'))
                ->with('product:id,name,code')
                ->groupBy('product_id')
                ->get()
                ->map(fn($row) => [
                    'product' => $row->product?->name ?? 'Chưa gán',
                    'product_code' => $row->product?->code ?? '-',
                    'total' => $row->total,
                    'total_weight' => round($row->total_weight, 2),
                ])
                ->values()
                ->toArray(),
            'by_machine' => (clone $query)
                ->select('machine_id', DB::raw('count(*) as total'), DB::raw('COALESCE(SUM(weight), 0) as total_weight'))
                ->with('machine:id,name,code')
                ->groupBy('machine_id')
                ->get()
                ->map(fn($row) => [
                    'machine' => $row->machine?->name ?? 'Chưa gán',
                    'total' => $row->total,
                    'total_weight' => round($row->total_weight, 2),
                ])
                ->values()
                ->toArray(),
            'by_shift' => (clone $query)
                ->select('shift', DB::raw('count(*) as total'), DB::raw('COALESCE(SUM(weight), 0) as total_weight'))
                ->groupBy('shift')
                ->get()
                ->map(fn($row) => [
                    'shift' => $row->shift ?? 'Không rõ',
                    'total' => $row->total,
                    'total_weight' => round($row->total_weight, 2),
                ])
                ->values()
                ->toArray(),
            'by_date' => (clone $query)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'), DB::raw('COALESCE(SUM(weight), 0) as total_weight'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get()
                ->map(fn($row) => [
                    'date' => $row->date,
                    'total' => $row->total,
                    'total_weight' => round($row->total_weight, 2),
                ])
                ->values()
                ->toArray(),
        ];

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'date_from' => $schema->string()
                ->description('Ngày bắt đầu báo cáo (format: YYYY-MM-DD). Mặc định 7 ngày trước.'),
            'date_to' => $schema->string()
                ->description('Ngày kết thúc báo cáo (format: YYYY-MM-DD). Mặc định hôm nay.'),
            'department_id' => $schema->integer()
                ->description('Lọc theo Bộ phận cụ thể (tùy chọn).'),
        ];
    }
}
