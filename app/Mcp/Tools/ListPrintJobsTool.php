<?php

namespace App\Mcp\Tools;

use App\Models\PrintJob;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tool;

#[Description('Xem danh sách lệnh in (print jobs) của hệ thống. Lọc theo trạng thái, item, hoặc printer. Hỗ trợ giám sát hoạt động in tem nhãn cuộn vải.')]
#[IsReadOnly]
#[IsIdempotent]
class ListPrintJobsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $query = PrintJob::with(['item:id,code,warehouse_code', 'user:id,name']);

        if ($status = $request->get('status')) {
            $query->where('status', (int) $status);
        }

        if ($itemId = $request->get('item_id')) {
            $query->where('item_id', $itemId);
        }

        if ($printerMac = $request->get('printer_mac')) {
            $query->where('printer_mac', $printerMac);
        }

        $limit = min((int) ($request->get('limit') ?? 30), 100);

        $jobs = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $statusLabels = [
            PrintJob::STATUS_PENDING => 'Chờ in',
            PrintJob::STATUS_FAILED => 'Lỗi',
            PrintJob::STATUS_PRINTING => 'Đang in',
            PrintJob::STATUS_SUCCESS => 'Thành công',
        ];

        $result = [
            'total_returned' => $jobs->count(),
            'print_jobs' => $jobs->map(fn($j) => [
                'id' => $j->id,
                'item_id' => $j->item_id,
                'item_code' => $j->item?->code,
                'warehouse_code' => $j->item?->warehouse_code,
                'printer_mac' => $j->printer_mac,
                'status' => $statusLabels[$j->status] ?? 'Không rõ',
                'status_value' => $j->status,
                'user' => $j->user?->name,
                'created_at' => $j->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $j->updated_at?->format('Y-m-d H:i:s'),
            ])->values()->toArray(),
        ];

        return Response::text(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()
                ->enum(['0', '1', '2', '3'])
                ->description('Lọc theo trạng thái: 0=Chờ in, 1=Lỗi, 2=Đang in, 3=Thành công.'),
            'item_id' => $schema->integer()
                ->description('Lọc theo ID item.'),
            'printer_mac' => $schema->string()
                ->description('Lọc theo MAC address của máy in.'),
            'limit' => $schema->integer()
                ->description('Số kết quả tối đa (mặc định 30, tối đa 100).')
                ->default(30),
        ];
    }
}
