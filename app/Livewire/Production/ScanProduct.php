<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
USE App\Enums\ItemStatus;
class ScanProduct extends Component
{
    // Dữ liệu chung
    public $scanStatus = ''; // 'success', 'error', 'warning'
    public $message = '';
    public $itemInfo = [];
    public $orders;

    // BIẾN MỚI
    public $products = []; // Danh sách Model theo bộ phận
    public $selectedOrderId = '';
    public $selectedProductId = ''; // Model nhân viên chọn

    public $lastScannedCode = null;
    public $scannedCodeInput = '';
    public function mount()
    {
        $user = Auth::user();
        // Lấy đơn hàng đang chạy
        $this->orders = Order::where('status','>=', 1)->orderBy('id', 'desc')->get();
        if ($user->department_id) {
            $this->products = Product::whereHas('departments', function ($q) use ($user) {
                $q->where('departments.id', $user->department_id);
            })->get();
        } else {
            // Nếu user không thuộc phòng ban nào, hoặc là Admin -> Lấy hết (hoặc rỗng tùy logic)
            // Ở đây mình lấy hết để dễ test, bạn có thể để [] nếu muốn chặt chẽ
            $this->products = Product::all();
        }
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


    // --- HÀM XỬ LÝ CHÍNH ĐÃ ĐƯỢC NÂNG CẤP ---
    public function processCode($code, $source = 'mobile')
    {
        $this->lastScannedCode = $code;
        $this->itemInfo = [];
        $this->scanStatus = '';
        $this->message = '';

        // 1. Tìm tem trong DB
        $item = Item::where('code', $code)->first();

        if (!$item) {
            $this->respondError("❌ Lỗi: Không tìm thấy mã '$code'", $source);
            return;
        }

        // 2. Kiểm tra đã quét chưa
        if ($item->status == ItemStatus::VERIFIED || $item->verified_at) {
            $this->scanStatus = 'warning';
            $this->message = "⚠️ Cảnh báo: Mã này ĐÃ ĐƯỢC QUÉT trước đó.";
            $this->itemInfo = $item->properties;

            $this->dispatch('play-warning-sound');
            if ($source == 'mobile') {
                $this->dispatch('show-toast', type: 'warning', title: 'Đã quét!', text: 'Mã này đã được xử lý rồi.');
            } else {
                $this->dispatch('focus-input');
            }
            return;
        }

        // 3. CHUẨN BỊ DỮ LIỆU CẬP NHẬT
        $updateData = [
            'status' => ItemStatus::VERIFIED,
            'order_id' => !empty($this->selectedOrderId) ? $this->selectedOrderId : $item->order_id,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ];

        $properties = $item->properties ?? [];
        $hasChange = false;

        // --- A. Cập nhật Đơn hàng (Nếu user có chọn) ---
        if (!empty($this->selectedOrderId)) {
            // Nếu tem đang thuộc đơn khác, ta chuyển nó sang đơn mới
            if ($item->order_id != $this->selectedOrderId) {
                $updateData['order_id'] = $this->selectedOrderId;

                // Cập nhật text PO trong properties để hiển thị đúng
                $order = $this->orders->find($this->selectedOrderId);
                if ($order) {
                    $properties['PO'] = $order->code;
                    $hasChange = true;
                }
            }
        }
        // Lưu ý: Nếu user để trống selectedOrderId -> Giữ nguyên order_id cũ của tem (Không làm gì cả)

        // --- B. Cập nhật Model (Nếu user có chọn) ---
        if (!empty($this->selectedProductId)) {
            if ($item->product_id != $this->selectedProductId) {
                $updateData['product_id'] = $this->selectedProductId;

                // Cập nhật MA_VAI trong properties (thường Mã vải = Mã Model)
               $product = Product::find($this->selectedProductId);
                if ($product) {
                    $properties['SP'] = $product->code;
                    $hasChange = true;
                }
            }
        }

        // Nếu có thay đổi thông tin phụ (PO, MA_VAI), cập nhật lại JSON
        if ($hasChange) {
            $updateData['properties'] = $properties;
        }

        // 4. THỰC HIỆN UPDATE
        $item->update($updateData);

        // 5. PHẢN HỒI THÀNH CÔNG
        $this->scanStatus = 'success';
        $this->message = "✅ ĐÃ XÁC NHẬN: " . $code;
        $this->itemInfo = $item->properties; // Hiển thị thông tin MỚI NHẤT

        $this->dispatch('play-success-sound');

        if ($source == 'mobile') {
            $this->dispatch('show-toast', type: 'success', title: 'Thành công!', text: 'Đã cập nhật & xác nhận: ' . $code);
        } else {
            $this->dispatch('focus-input');
        }
    }

    private function respondError($msg, $source)
    {
        $this->scanStatus = 'error';
        $this->message = $msg;
        $this->dispatch('play-error-sound');

        if ($source == 'mobile') {
            $this->dispatch('show-toast', type: 'error', title: 'Lỗi!', text: $msg);
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
