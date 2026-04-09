<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // 🌟 Bắt buộc phải có
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Item;
// In từ xa qua trang WEB
class PrintLabelRequested implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;
    public $printerMac; // (Tùy chọn) Mã của máy in hoặc trạm in
    public $printJobId; // ID của công việc in trong database

    public function __construct(Item $item, $printerMac = '01', $printJobId = null)
    {
        $this->item = $item;
        $this->printerMac = $printerMac;
        $this->printJobId = $printJobId;
    }

    // 🌟 Xác định "Ống nước" (Channel) nào sẽ truyền dữ liệu này
    public function broadcastOn()
    {
        // Gửi vào kênh chung của trạm in số 1
        return [
            new Channel('printer.' . $this->printerMac),
        ];
    }

    // (Tùy chọn) Tên sự kiện để JS lắng nghe
    public function broadcastAs()
    {
        return 'print.command';
    }
}
