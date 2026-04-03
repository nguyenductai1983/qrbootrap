<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Item;
use App\Models\Location;
use App\Models\ItemMovement;
use App\Enums\ItemStatus;
use App\Enums\MovementAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

#[Title('Nhập Kho Bán Thành Phẩm')]
class ScanToLocation extends Component
{
    /**
     * Chế độ hoạt động:
     *  'temp'     - (1) Nhập tạm, không cần vị trí
     *  'with_loc' - (2) Nhập có vị trí (phải quét kệ trước)
     *  'confirm'  - (3) Xác nhận / cập nhật vị trí cho cây vải đã IN_WAREHOUSE
     */
    public string $mode = 'temp';

    // Phiên làm việc
    public $currentLocation   = null;
    public ?int $currentLocationId = null;

    // Trạng thái quét
    public string $scanStatus = '';   // 'success' | 'error' | 'warning' | 'location'
    public string $message    = '';
    public $itemInfo          = null;
    public $lastScannedCode   = null;
    public string $scannedCodeInput = '';

    // Lịch sử trong phiên (tối đa 20)
    public array $sessionItems = [];

    // --- Tự động reset khi đổi mode (gọi bởi wire:model.live) ---
    public function updatedMode(): void
    {
        // Lưu lựa chọn để dùng lần sau
        cache()->forever('warehouse_mode_' . Auth::id(), $this->mode);
        $this->resetSession();
    }

    // --- XỬ LÝ BÀN PHÍM / MÁY QUÉT ---
    public function handleKeyInput(): void
    {
        $code = strtoupper(trim($this->scannedCodeInput));
        $this->scannedCodeInput = '';
        if (!empty($code)) {
            $this->processCode($code);
        }
    }

    // --- XỬ LÝ CAMERA ---
    public function handleScan($code): void
    {
        $code = strtoupper(trim($code));
        if ($this->lastScannedCode === $code && $this->scanStatus === 'success') {
            return;
        }
        $this->processCode($code);
    }

    // --- HÀM XỬ LÝ CHÍNH ---
    public function processCode(string $code): void
    {
        $this->lastScannedCode = $code;
        $this->scanStatus      = '';
        $this->message         = '';
        $this->itemInfo        = null;

        // Bước 1: Kiểm tra xem có phải mã Vị trí không (áp dụng cho mode 2 & 3)
        if (in_array($this->mode, ['with_loc', 'confirm'])) {
            $location = Location::where('code', $code)->first();
            if ($location) {
                $this->currentLocation   = $location;
                $this->currentLocationId = $location->id;
                $this->sessionItems      = [];
                $this->scanStatus        = 'location';
                $this->message           = "📍 Đã chọn vị trí: [{$location->code}] {$location->name}";
                $this->dispatch('play-success-sound');
                return;
            }
        }

        // Bước 2: Tìm Item
        $item = Item::with(['product', 'color', 'order', 'location'])
            ->where('code', $code)
            ->first();

        if (!$item) {
            $this->error("❌ Không tìm thấy mã '$code' trong hệ thống.");
            return;
        }

        // ----- PHÂN LUỒNG THEO MODE -----
        match ($this->mode) {
            'temp'     => $this->handleTemp($item),
            'with_loc' => $this->handleWithLocation($item),
            'confirm'  => $this->handleConfirmLocation($item),
        };
    }

    /** MODE 1: Nhập tạm — không cần vị trí */
    private function handleTemp(Item $item): void
    {

        if ($item->status === ItemStatus::IN_WAREHOUSE) {
            $loc = optional($item->location)->code ?? 'chưa xác định';
            $this->warn("⚠️ Cây vải này ĐÃ ĐƯỢC nhập kho rồi (vị trí: $loc). Bỏ qua.");
            $this->itemInfo = $item;
            return;
        }

        $this->doWarehouseIn($item, null, 'Nhập kho tạm (chưa có vị trí)');
    }

    /** MODE 2: Nhập có vị trí — phải chọn vị trí trước */
    private function handleWithLocation(Item $item): void
    {
        if (!$this->currentLocationId) {
            $this->error("❌ Chưa chọn vị trí! Hãy quét mã QR kệ hàng trước.");
            return;
        }

        if ($item->status === ItemStatus::NONE) {
            $this->error("⛔ Cây vải chưa được xác nhận sản xuất! Không thể nhập kho.");
            $this->itemInfo = $item;
            return;
        }

        if ($item->status === ItemStatus::IN_WAREHOUSE) {
            $loc = optional($item->location)->code ?? 'chưa xác định';
            $this->warn("⚠️ Cây vải này ĐÃ ĐƯỢC nhập kho rồi (vị trí: $loc).");
            $this->itemInfo = $item;
            return;
        }

        $this->doWarehouseIn($item, $this->currentLocationId, 'Nhập kho có vị trí');
    }

    /** MODE 3: Xác nhận / Cập nhật vị trí — chỉ gán location, không đổi status */
    private function handleConfirmLocation(Item $item): void
    {
        if (!$this->currentLocationId) {
            $this->error("❌ Chưa chọn vị trí! Hãy quét mã QR kệ hàng trước.");
            return;
        }

        // Chỉ xử lý cây vải đã IN_WAREHOUSE (hoặc VERIFIED để gán luôn)
        if ($item->status === ItemStatus::NONE) {
            $this->error("⛔ Cây vải chưa xác nhận sản xuất, không thể xác nhận vị trí.");
            $this->itemInfo = $item;
            return;
        }

        $oldLocationId = $item->current_location_id;
        $item->update([
            'current_location_id' => $this->currentLocationId,
        ]);
        if ($oldLocationId != $this->currentLocationId) {
            ItemMovement::create([
                'item_id'          => $item->id,
                'action_type'      => MovementAction::CONFIRM_LOCATION->value,
                'from_location_id' => $oldLocationId,
                'to_location_id'   => $this->currentLocationId,
                'user_id'          => Auth::id(),
                'note'             => 'Xác nhận / Cập nhật vị trí trong kho',
                'created_at'       => now(),
            ]);
        }

        $item->refresh()->load(['product', 'color', 'order', 'location']);
        $this->addToSession($item);

        $this->scanStatus = 'success';
        $this->message    = "✅ ĐÃ GÁN VỊ TRÍ: {$item->code} → [{$this->currentLocation->code}]";
        $this->itemInfo   = $item;
        $this->dispatch('play-success-sound');
        $this->dispatch('focus-input');
    }

    /** Hàm nhập kho dùng chung cho mode 1 & 2 */
    private function doWarehouseIn(Item $item, ?int $locationId, string $note): void
    {
        $oldLocationId = $item->current_location_id;

        $item->update([
            'status'             => ItemStatus::IN_WAREHOUSE,
            'current_location_id' => $locationId,
            'warehoused_by'      => Auth::id(),
            'warehoused_at'      => now(),
        ]);
        if ($oldLocationId != $locationId) {
            ItemMovement::create([
                'item_id'          => $item->id,
                'action_type'      => MovementAction::IN_WAREHOUSE->value,
                'from_location_id' => $oldLocationId,
                'to_location_id'   => $locationId,
                'user_id'          => Auth::id(),
                'note'             => $note,
                'created_at'       => now(),
            ]);
        }

        $item->refresh()->load(['product', 'color', 'order', 'location']);
        $this->addToSession($item);

        $locText = $locationId && $this->currentLocation
            ? " → [{$this->currentLocation->code}]"
            : ' (chưa có vị trí)';

        $this->scanStatus = 'success';
        $this->message    = "✅ ĐÃ NHẬP KHO: {$item->code}{$locText}";
        $this->itemInfo   = $item;
        $this->dispatch('play-success-sound');
        $this->dispatch('focus-input');
    }

    // --- HELPERS ---
    private function error(string $msg): void
    {
        $this->scanStatus = 'error';
        $this->message    = $msg;
        $this->dispatch('play-error-sound');
    }

    private function warn(string $msg): void
    {
        $this->scanStatus = 'warning';
        $this->message    = $msg;
        $this->dispatch('play-warning-sound');
    }

    private function addToSession(Item $item): void
    {
        array_unshift($this->sessionItems, [
            'code'          => $item->code,
            'product_code'  => optional($item->product)->code,
            'color_code'    => optional($item->color)->code,
            'order_code'    => optional($item->order)->code,
            'location_code' => optional($item->location)->code,
            'length'        => $item->length,
            'time'          => now()->format('H:i:s'),
        ]);
        $this->sessionItems = array_slice($this->sessionItems, 0, 20);
    }

    public function changeLocation(): void
    {
        $this->currentLocation   = null;
        $this->currentLocationId = null;
        $this->scanStatus        = '';
        $this->message           = '';
        $this->itemInfo          = null;
        $this->lastScannedCode   = null;
    }

    public function resetSession(): void
    {
        $this->currentLocation   = null;
        $this->currentLocationId = null;
        $this->sessionItems      = [];
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

    public function render()
    {
        return view('livewire.warehouse.scan-to-location');
    }
}
