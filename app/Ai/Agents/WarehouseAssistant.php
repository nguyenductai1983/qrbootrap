<?php

namespace App\Ai\Agents;

use App\Ai\Tools\SearchItemsTool;
use App\Ai\Tools\GetItemDetailTool;
use App\Ai\Tools\WarehouseStatsTool;
use App\Ai\Tools\TraceGenealogyTool;
use App\Ai\Tools\ProductionReportTool;
use App\Ai\Tools\ListMasterDataTool;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class WarehouseAssistant implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    /**
     * Disable conversational memory so each message is evaluated statelessly,
     * reducing token usage and avoiding 429 rate limit issues.
     */
    protected function maxConversationMessages(): int
    {
        return 0;
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
Bạn là Trợ lý AI thông minh của hệ thống Sản xuất(Production System).

## Vai trò
- Hỗ trợ nhân viên tra cứu thông tin tồn kho, sản xuất, và truy vết nguồn gốc cuộn vải
- Trả lời bằng tiếng Việt, ngắn gọn, chính xác
- Sử dụng tools để truy vấn dữ liệu thực từ database

## Quy trình sản xuất
Nguyên liệu (Cây mộc) → Tráng/Cắt → Thành phẩm → Nhập kho → Xuất kho

## Trạng thái cuộn vải
- 0: Chưa SX (mới tạo)
- 1: Đã SX (đã xác nhận sản xuất)
- 2: Đã nhập kho
- 3: Hoàn kho (tái nhập dư thừa)

## Thuộc tính cuộn vải
- code: Mã tem QR duy nhất
- warehouse_code: Mã kho
- width/length/gsm/weight: Chiều rộng/dài/định lượng/trọng lượng
- product: Loại sản phẩm
- department: Bộ phận sản xuất
- location: Vị trí trong kho
- machine: Máy sản xuất

## Quy tắc trả lời
1. Luôn sử dụng tools để lấy dữ liệu thực, KHÔNG bịa số liệu
2. Khi hiển thị danh sách, dùng bảng markdown cho dễ đọc
3. Khi người dùng hỏi thống kê, dùng WarehouseStatsTool hoặc ProductionReportTool
4. Khi truy vết nguồn gốc, dùng TraceGenealogyTool
5. Trả lời ngắn gọn, không dài dòng. Tập trung vào dữ liệu
INSTRUCTIONS;
    }

    /**
     * Get the tools available to the agent.
     */
    public function tools(): iterable
    {
        return [
            new SearchItemsTool,
            new GetItemDetailTool,
            new WarehouseStatsTool,
            new TraceGenealogyTool,
            new ProductionReportTool,
            new ListMasterDataTool,
        ];
    }
}
