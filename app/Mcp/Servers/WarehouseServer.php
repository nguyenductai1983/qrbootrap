<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\SearchItemsTool;
use App\Mcp\Tools\GetItemDetailTool;
use App\Mcp\Tools\WarehouseStatsTool;
use App\Mcp\Tools\TraceGenealogyTool;
use App\Mcp\Tools\ListMasterDataTool;
use App\Mcp\Tools\GetMovementHistoryTool;
use App\Mcp\Tools\ListPrintJobsTool;
use App\Mcp\Tools\ProductionReportTool;
use App\Mcp\Tools\LocationInventoryTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Warehouse Server')]
#[Version('1.0.0')]
#[Instructions('Server quản lý kho vải (Warehouse Management System). Cung cấp các công cụ để:
- Tìm kiếm và xem chi tiết cuộn vải/item
- Truy vết phả hệ nguồn gốc nguyên liệu (genealogy)
- Thống kê tồn kho theo nhiều chiều: trạng thái, phòng ban, sản phẩm, vị trí
- Xem lịch sử di chuyển và hoạt động kho
- Báo cáo sản xuất theo thời gian
- Giám sát lệnh in tem nhãn
- Liệt kê dữ liệu danh mục (master data)

Hệ thống quản lý quy trình: Nguyên liệu → Sản xuất (Tráng/Cắt) → Nhập kho → Xuất kho.
Mỗi cuộn vải có mã code QR, warehouse_code, và các thuộc tính: chiều rộng, chiều dài, GSM, trọng lượng.')]
class WarehouseServer extends Server
{
    protected array $tools = [
        SearchItemsTool::class,
        GetItemDetailTool::class,
        WarehouseStatsTool::class,
        TraceGenealogyTool::class,
        ListMasterDataTool::class,
        GetMovementHistoryTool::class,
        ListPrintJobsTool::class,
        ProductionReportTool::class,
        LocationInventoryTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
