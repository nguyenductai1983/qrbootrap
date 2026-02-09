<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ScanProduct extends Component
{
    public $lastScannedCode = null;
    public $scanStatus = '';
    public $message = '';
    public $itemInfo = []; // Lưu thông tin item để hiển thị
    public $orders;
    public $selectedOrderId = '';
    public function mount()
    {
        // Chỉ lấy các đơn hàng ĐANG CHẠY (Status = RUNNING) để đỡ rối
        $this->orders = Order::where('status', 'RUNNING')->get();
    }
    public function handleScan($code)
    {
        // 1. Dọn dẹp mã (trim khoảng trắng)
        $code = strtoupper(trim($code));

        // Nếu mã giống mã vừa quét xong thì bỏ qua (tránh double click)
        if ($this->lastScannedCode === $code && $this->scanStatus === 'success') {
            return;
        }

        $this->processCode($code);
    }

    public function processCode($code)
    {
        $this->lastScannedCode = $code;
        $this->itemInfo = []; // Reset thông tin cũ

        // Tìm trong DB (Lưu ý: Mã trong DB và mã quét phải y hệt nhau)
        $item = Item::where('code', $code)->first();

        if (!$item) {
            $this->scanStatus = 'error';
            $this->message = "❌ Lỗi: Không tìm thấy mã '$code'";
            $this->dispatch('scan-finished', status: 'error'); // Báo cho JS biết đã xong
            return;
        }
        if ($this->selectedOrderId && $item->order_id != $this->selectedOrderId) {
            $this->scanStatus = 'error';
            $this->message = "❌ SAI ĐƠN HÀNG! Mã này thuộc PO khác.";
            $this->dispatch('play-error-sound');
            return;
        }
        // Kiểm tra đã nhập kho chưa
        if ($item->status == 'VERIFIED' || $item->verified_at) {
            $this->scanStatus = 'warning';
            $this->message = "⚠️ Cảnh báo: Mã này ĐÃ ĐƯỢC QUÉT trước đó!";
            $this->itemInfo = $item->properties; // Vẫn hiện thông tin để đối chiếu
            $this->dispatch('scan-finished', status: 'warning');
            return;
        }

        // CẬP NHẬT DỮ LIỆU
        $item->update([
            'status' => 'VERIFIED',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        $this->scanStatus = 'success';
        $this->message = "✅ ĐÃ XÁC NHẬN!";
        $this->itemInfo = $item->properties; // Hiển thị thông tin vải vừa quét

        // Gửi sự kiện xuống JS để phát âm thanh
        $this->dispatch('play-success-sound');

        // Quan trọng: Báo cho JS biết xử lý xong để dừng loading
        $this->dispatch('scan-finished', status: 'success');
    }

    public function resetScan()
    {
        $this->scanStatus = '';
        $this->message = '';
        $this->itemInfo = [];
        $this->lastScannedCode = null;

        // Gửi lệnh cho JS để bật lại Camera
        $this->dispatch('resume-camera');
    }

    public function render()
    {
        return view('livewire.production.scan-product');
    }
}
