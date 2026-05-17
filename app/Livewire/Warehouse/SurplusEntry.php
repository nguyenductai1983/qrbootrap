<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Item;
use App\Models\Location;
use App\Models\ItemMovement;
use App\Models\ScaleStation;
use App\Enums\ItemStatus;
use App\Enums\MovementAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use App\Services\ItemWeightService;

#[Title('Tái nhập Dư')]
class SurplusEntry extends Component
{
    // Trạng thái quét
    public string $scanStatus = '';   // 'success' | 'error' | 'warning' | 'location'
    public string $message    = '';
    public mixed $itemInfo    = null;
    public mixed $lastScannedCode = null;
    public string $scannedCodeInput = '';

    // Vị trí kệ (tùy chọn, có thể quét hoặc để trống)
    public mixed $currentLocation   = null;
    public ?int  $currentLocationId = null;

    // Trọng lượng
    public mixed $manualWeight = null;
    public mixed $scaleWeight  = null;
    public bool  $scaleStable  = false;
    public $scaleStations = [];
    public string $selectedScaleCode = '';

    // Ghi chú lý do tái nhập
    public string $warehouseNote = '';

    // Lịch sử phiên
    public array $sessionItems = [];

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->scaleStations = $user->scaleStations()->where('status', true)->orderBy('code')->get();

        $cachedScale = cache()->get('selected_scale_code_' . Auth::id());
        if ($cachedScale && collect($this->scaleStations)->contains('code', $cachedScale)) {
            $this->selectedScaleCode = $cachedScale;
        } elseif (count($this->scaleStations) === 1) {
            $this->selectedScaleCode = collect($this->scaleStations)->first()->code;
        }
    }

    public function updatedSelectedScaleCode(): void
    {
        cache()->forever('selected_scale_code_' . Auth::id(), $this->selectedScaleCode);
    }

    /** Nhận trọng lượng từ WebSocket */
    public function updateScaleWeight(float $weight, bool $isStable = false): void
    {
        $this->scaleWeight = $weight;
        $this->scaleStable = $isStable;
    }

    /** Trọng lượng hiệu lực: WebSocket > Nhập tay */
    public function getEffectiveWeight(): ?float
    {
        if ($this->scaleWeight !== null && $this->scaleWeight > 0) {
            return (float) $this->scaleWeight;
        }
        if ($this->manualWeight !== null && (float) $this->manualWeight > 0) {
            return (float) $this->manualWeight;
        }
        return null;
    }

    // --- Xử lý nhập phím / máy quét ---
    public function handleKeyInput(): void
    {
        $code = strtoupper(trim($this->scannedCodeInput));
        $this->scannedCodeInput = '';
        if (!empty($code)) {
            $this->processCode($code);
        }
    }

    public function handleScan(string $code): void
    {
        $code = strtoupper(trim($code));
        if ($this->lastScannedCode === $code) {
            return;
        }
        $this->processCode($code);
    }

    /** Hàm xử lý chính */
    public function processCode(string $code): void
    {
        $this->lastScannedCode = $code;
        $this->scanStatus      = '';
        $this->message         = '';
        $this->itemInfo        = null;

        // Kiểm tra xem có phải mã Vị trí không
        $location = Location::where('code', $code)->first();
        if ($location) {
            $this->currentLocation   = $location;
            $this->currentLocationId = $location->id;
            $this->scanStatus        = 'location';
            $this->message           = "📍 Đã chọn vị trí: [{$location->code}] {$location->name}";
            $this->dispatch('play-success-sound');
            $this->dispatch('focus-input');
            return;
        }

        // Tìm Item
        $item = Item::with(['product', 'color', 'order', 'location'])
            ->where('code', $code)
            ->first();

        if (!$item) {
            $this->error("❌ Không tìm thấy mã '$code' trong hệ thống.");
            return;
        }

        // Kiểm tra trọng lượng bắt buộc
        $effectiveWeight = $this->getEffectiveWeight();
        if ($effectiveWeight === null) {
            $this->error("⚖️ Vui lòng nhập trọng lượng trước khi quét mã cây vải.");
            $this->itemInfo = $item;
            return;
        }

        $this->handleSurplusEntry($item, $effectiveWeight);
    }

    /** Xử lý tái nhập dư */
    private function handleSurplusEntry(Item $item, float $newWeight): void
    {
        $service = new ItemWeightService();
        $result = $service->updateWeight(
            $item,
            $newWeight,
            Auth::id(),
            $this->warehouseNote,
            forceSurplus: true  // Luôn là SURPLUS_ENTRY
        );

        // Cập nhật vị trí nếu có
        if ($this->currentLocationId) {
            $oldLocationId = $item->current_location_id;
            $item->current_location_id = $this->currentLocationId;
            $item->save();

            if ($oldLocationId != $this->currentLocationId) {
                ItemMovement::create([
                    'item_id'          => $item->id,
                    'action_type'      => MovementAction::CONFIRM_LOCATION->value,
                    'from_location_id' => $oldLocationId,
                    'to_location_id'   => $this->currentLocationId,
                    'user_id'          => Auth::id(),
                    'note'             => 'Cập nhật vị trí khi tái nhập dư' . ($this->warehouseNote ? " | {$this->warehouseNote}" : ''),
                    'created_at'       => now(),
                ]);
            }
        }

        $item->refresh()->load(['product', 'color', 'order', 'location', 'movements.user']);
        $this->addToSession($item);

        $locText = $this->currentLocationId && $this->currentLocation
            ? " → [{$this->currentLocation->code}]"
            : '';
        $noteText = $this->warehouseNote ? " | \"{$this->warehouseNote}\"" : '';

        $this->scanStatus = 'success';
        $this->message    = "♻️ TÁI NHẬP DƯ: {$item->code} | {$result['old_weight']}kg → {$result['new_weight']}kg{$locText}{$noteText}";
        $this->itemInfo   = $item;

        $this->dispatch('play-success-sound');
        $this->dispatch('focus-input');
    }

    private function error(string $msg): void
    {
        $this->scanStatus = 'error';
        $this->message    = $msg;
        $this->dispatch('play-error-sound');
    }

    private function addToSession(Item $item): void
    {
        array_unshift($this->sessionItems, [
            'code'          => $item->code,
            'product_code'  => optional($item->product)->code,
            'color_code'    => optional($item->color)->code,
            'location_code' => optional($item->location)->code,
            'weight'        => $item->weight,
            'note'          => $this->warehouseNote,
            'time'          => now()->format('H:i:s'),
        ]);
        $this->sessionItems = array_slice($this->sessionItems, 0, 20);
    }

    public function clearLocation(): void
    {
        $this->currentLocation   = null;
        $this->currentLocationId = null;
        $this->scanStatus        = '';
        $this->message           = '';
        $this->itemInfo          = null;
        $this->lastScannedCode   = null;
    }

    public function resetScan(): void
    {
        $this->scanStatus      = '';
        $this->message         = '';
        $this->itemInfo        = null;
        $this->lastScannedCode = null;
        $this->dispatch('resume-camera');
    }

    public function clearSession(): void
    {
        $this->sessionItems    = [];
        $this->scanStatus      = '';
        $this->message         = '';
        $this->itemInfo        = null;
        $this->lastScannedCode = null;
    }

    public function render()
    {
        return view('livewire.warehouse.surplus-entry', [
            'effectiveWeight' => $this->getEffectiveWeight(),
        ]);
    }
}
