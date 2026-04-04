<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Dùng Now
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrintLabelEvent implements ShouldBroadcastNow // Sửa ở đây
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
        // Phải khớp với kênh C# đang nghe: print-stationvb.station_001_secret
        return [
            new Channel('print-stationvb.' . $this->stationKey),
        ];
    }

    public function broadcastAs(): string
    {
        return 'PrintCommand'; // Tên này cực kỳ quan trọng cho C#
    }
}
