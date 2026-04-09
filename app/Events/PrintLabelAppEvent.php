<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Dùng Now
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class PrintLabelAppEvent implements ShouldBroadcastNow // Sửa ở đây
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $stationKey;

    public function __construct($stationKey, $printData)
    {
        $this->stationKey = $stationKey;
        $this->data = $printData;
    }

    public function broadcastOn(): array
    {
        // Dùng Channel thường nhưng với cái tên "bí mật"
        return [
            new Channel('printstationapp.' . $this->stationKey),
        ];
    }

    public function broadcastAs(): string
    {
        return 'PrintAppCommand'; // Tên này cực kỳ quan trọng cho C#
    }

    public function broadcastWith(): array
    {
        // Điều này đảm bảo Laravel không tự ý bọc payload vào biến "data" hay "stationKey"
        // mà đẩy nguyên gốc mảng $printData sang cho C# dễ xử lý.
        return $this->data;
    }
}
