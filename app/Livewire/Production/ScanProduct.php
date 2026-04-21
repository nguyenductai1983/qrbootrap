<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Enums\ItemStatus;
use App\Models\Machine;
use Livewire\Attributes\Title;

#[Title('Xác nhận Cây Vải')]
/**
 * @method void dispatch(string $event, string $type = null, string $title = null, string $text = null)
 */
class ScanProduct extends Component
{
    use \App\Livewire\Traits\WithReprinting;

    // Dữ liệu chung
    public $scanStatus = ''; // 'success', 'error', 'warning'
    public $message = '';
    public $itemInfo = [];
    public $orders;
    public $machines = [];
    public $selectedMachineId = ''; // Máy đang thực hiện tráng
    // BIẾN MỚI
    public $scannedItemId = null; // Lưu lại id của tem đang hiển thị
    public $products = []; // Danh sách Model theo bộ phận
    public $selectedOrderId = '';
    public $selectedProductId = ''; // Model nhân viên chọn

    public $lastScannedCode = null;
    public $scannedCodeInput = '';

    // BIẾN CẬP NHẬT M
    public $editLength = null;
    public $editNotes = '';

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Lấy đơn hàng đang chạy
        $this->machines = $user->machines()->where('status', true)->orderBy('code')->get();
        $this->orders = Order::where('status', '>=', 1)->orderBy('id', 'desc')->get();
        if ($user->department_id) {
            $this->products = Product::whereHas('departments', function ($q) use ($user) {
                $q->where('departments.id', $user->department_id);
            })->get();
        } else {
            // Nếu user không thuộc Bộ phận nào, hoặc là Admin -> Lấy hết (hoặc rỗng tùy logic)
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
            $this->message = $code . " ⚠️ Cảnh báo: Mã này ĐÃ ĐƯỢC sử dụng.";

            // Lấy full model ra view để load được Relationship
            $this->itemInfo = Item::with(['product', 'color', 'order'])->find($item->id);
            $this->scannedItemId = $item->id;
            $this->editLength = $item->length;
            $this->editNotes = '';

            $this->dispatch('play-warning-sound');
            if ($source == 'mobile') {
                $this->dispatch('show-toast', ...['type' => 'warning', 'title' => 'Đã quét!', 'text' => 'Mã này đã được xử lý rồi.']);
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
            'machine_id' => !empty($this->selectedMachineId) ? $this->selectedMachineId : $item->machine_id,
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
        // Lấy full model ra view để load được Relationship
        $this->itemInfo = Item::with(['product', 'color', 'order'])->find($item->id);
        $this->scannedItemId = $item->id; // Lấy ID để có thể bấm nút In Lại
        $this->editLength = $item->length;
        $this->editNotes = '';

        $this->dispatch('play-success-sound');

        if ($source == 'mobile') {
            $this->dispatch('show-toast', ...['type' => 'success', 'title' => 'Thành công!', 'text' => 'Đã cập nhật & xác nhận: ' . $code]);
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
            $this->dispatch('show-toast', ...['type' => 'error', 'title' => 'Lỗi!', 'text' => $msg]);
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
        $this->scannedItemId = null;
        $this->editLength = null;
        $this->editNotes = '';
        $this->dispatch('resume-camera');
    }

    public function updateLength()
    {
        if (!$this->scannedItemId) {
            return;
        }

        $item = Item::find($this->scannedItemId);
        if (!$item) {
            $this->dispatch('show-toast', ...[['type' => 'error', 'title' => 'Lỗi!', 'text' => 'Không tìm thấy dữ liệu vải.']]);
            return;
        }

        $oldLength = $item->length;
        $newLength = (float)$this->editLength;

        // Lưu lịch sử nếu có thay đổi m
        if ($oldLength != $newLength) {
            \App\Models\ItemHistory::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'field_name' => 'length',
                'old_value' => $oldLength,
                'new_value' => $newLength,
            ]);
            $item->length = $newLength;
        }

        // Cập nhật notes
        if (!empty($this->editNotes)) {
            $existingNotes = $item->notes;
            $newNote = "[Cập nhật " . now()->format('d/m/Y H:i') . "]: " . $this->editNotes;
            $item->notes = $existingNotes ? $existingNotes . " | " . $newNote : $newNote;
        }

        if ($item->isDirty()) {
            $item->save();
            // Refresh info
            $this->itemInfo = Item::with(['product', 'color', 'order'])->find($item->id);
            $this->dispatch('show-toast', ...[['type' => 'success', 'title' => 'Thành công!', 'text' => 'Đã lưu thông số.']]);
            $this->editNotes = ''; // Reset notes sau khi lưu
        } else {
             $this->dispatch('show-toast', ...[['type' => 'info', 'title' => 'Bỏ qua', 'text' => 'Không có thay đổi nào.']]);
        }
    }

    public function render()
    {
        $this->js("console.log('Quyét mã code')");
        return view('livewire.production.scan-product');
    }
}
