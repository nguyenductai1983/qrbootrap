<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScaleWeightUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $stationCode;
    public float $weight;
    public bool $isStable;

    public function __construct(string $stationCode, float $weight, bool $isStable = false)
    {
        $this->stationCode = $stationCode;
        $this->weight = $weight;
        $this->isStable = $isStable;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('scale.' . $this->stationCode),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ScaleWeightUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'station_code' => $this->stationCode,
            'weight'       => $this->weight,
            'is_stable'    => $this->isStable,
            'timestamp'    => now()->toIso8601String(),
        ];
    }
}
