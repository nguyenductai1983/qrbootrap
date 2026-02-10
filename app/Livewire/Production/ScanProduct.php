<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ScanProduct extends Component
{
    // Dữ liệu chung
    public $scanStatus = ''; // 'success', 'error', 'warning'
    public $message = '';
    public $itemInfo = [];
    public $orders;
    public $selectedOrderId = '';

    // Dữ liệu cho Camera (Mobile)
    public $lastScannedCode = null;

    // Dữ liệu cho Máy quét (PC)
    public $scannedCodeInput = '';

    public function mount()
    {
        // Lấy đơn hàng đang chạy
        $this->orders = Order::where('status', 'RUNNING')->orderBy('id', 'desc')->get();
    }

    // --- XỬ LÝ 1: TỪ MÁY QUÉT CẦM TAY / BÀN PHÍM (Sự kiện Enter) ---
    public function handleKeyInput()
    {
        $code = trim($this->scannedCodeInput);

        if (!empty($code)) {
            $this->processCode($code, 'pc');
        }

        // Reset ô nhập liệu ngay lập tức để quét mã tiếp theo
        $this->scannedCodeInput = '';
    }

    // --- XỬ LÝ 2: TỪ CAMERA ĐIỆN THOẠI (JS gọi lên) ---
    public function handleScan($code)
    {
        $code = strtoupper(trim($code));

        // Debounce: Tránh quét 1 mã 2 lần liên tiếp trên Camera
        if ($this->lastScannedCode === $code && $this->scanStatus === 'success') {
            return;
        }

        $this->processCode($code, 'mobile');
    }

    // --- HÀM XỬ LÝ CHUNG (CORE LOGIC) ---
    public function processCode($code, $source = 'mobile')
    {
        $this->lastScannedCode = $code;
        $this->itemInfo = [];
        $this->scanStatus = '';
        $this->message = '';

        // 1. Tìm trong DB
        $item = Item::where('code', $code)->first();

        // 2. Kiểm tra tồn tại
        if (!$item) {
            $this->respondError("❌ Lỗi: Không tìm thấy mã '$code'", $source);
            return;
        }

        // 3. Kiểm tra đúng Đơn hàng (Nếu có chọn lọc)
        if ($this->selectedOrderId && $item->order_id != $this->selectedOrderId) {
            $this->respondError("❌ SAI ĐƠN HÀNG! Mã này thuộc PO khác.", $source);
            return;
        }

        // 4. Kiểm tra đã quét chưa (Tránh quét trùng)
        if ($item->status == 'VERIFIED' || $item->verified_at) {
            $this->scanStatus = 'warning';
            $this->message = "⚠️ Cảnh báo: Mã này ĐÃ ĐƯỢC QUÉT lúc " . $item->verified_at;
            $this->itemInfo = $item->properties;

            // Nếu là PC thì vẫn phát âm báo lỗi/cảnh báo
            $this->dispatch('play-warning-sound');

            if ($source == 'mobile') {
                $this->dispatch('scan-finished', status: 'warning');
            } else {
                $this->dispatch('focus-input'); // PC: Focus lại để quét tiếp
            }
            return;
        }

        // 5. CẬP NHẬT THÀNH CÔNG
        $item->update([
            'status' => 'VERIFIED',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        $this->scanStatus = 'success';
        $this->message = "✅ ĐÃ XÁC NHẬN: " . $code;
        $this->itemInfo = $item->properties;

        // Gửi sự kiện xuống JS
        $this->dispatch('play-success-sound');

        if ($source == 'mobile') {
            $this->dispatch('scan-finished', status: 'success');
        } else {
            $this->dispatch('focus-input'); // PC: Focus lại ngay
        }
    }

    private function respondError($msg, $source)
    {
        $this->scanStatus = 'error';
        $this->message = $msg;
        $this->dispatch('play-error-sound');

        if ($source == 'mobile') {
            $this->dispatch('scan-finished', status: 'error');
        } else {
            $this->dispatch('focus-input');
        }
    }

    // Nút "Quét tiếp" trên Mobile
    public function resetScan()
    {
        $this->scanStatus = '';
        $this->message = '';
        $this->itemInfo = [];
        $this->lastScannedCode = null;
        $this->dispatch('resume-camera');
    }

    public function render()
    {
        return view('livewire.production.scan-product');
    }
}
