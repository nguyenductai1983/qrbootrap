<?php

namespace App\Livewire\Quality;

use Livewire\Component;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Enums\ItemStatus;
use App\Models\Machine;
use Livewire\Attributes\Title;
use App\Models\ItemHistory;
use App\Models\ItemPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Title('Xác nhận QC Vải')]
/**
 * @method void dispatch(string $event, string $type = null, string $title = null, string $text = null)
 */
class QualityScanProduction extends Component
{
    use \App\Livewire\Traits\WithReprinting;

    // Dữ liệu chung
    public $scanStatus = ''; // 'success', 'error', 'warning'
    public $message = '';
    public $itemInfo = [];
    public mixed $orders = [];
    public $machines = [];
    public $selectedMachineId = ''; // Máy đang thực hiện tráng
    // BIẾN MỚI
    public mixed $scannedItemId = null; // Lưu lại id của tem đang hiển thị
    public $products = []; // Danh sách Model theo bộ phận
    public $selectedOrderId = '';
    public $selectedProductId = ''; // Model nhân viên chọn

    public mixed $lastScannedCode = null;
    public $scannedCodeInput = '';

    // BIẾN CẬP NHẬT GSM
    public mixed $editGsm = null;
    public $editNotes = '';

    // BIẾN ẢNH QC
    public ?array $currentPhoto = null; // Thông tin ảnh hiện tại của tem đang xem

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
    public function handleScan(string $code)
    {
        $code = strtoupper(trim($code));

        // Debounce: Tránh quét 1 mã 2 lần liên tiếp trên Camera
        if ($this->lastScannedCode === $code) {
            return;
        }

        $this->processCode($code, 'mobile');
    }


    // --- HÀM XỬ LÝ CHÍNH ĐÃ ĐƯỢC NÂNG CẤP ---   
    public function processCode(string $code, string $source = 'mobile')
    {
        $this->lastScannedCode = $code;
        $this->itemInfo = [];
        $this->scanStatus = '';
        $this->message = '';
        $this->currentPhoto = null;

        // 1. Tìm tem trong DB
        $item = Item::where('code', $code)->first();

        if (!$item) {
            $this->respondError("❌ Lỗi: Không tìm thấy mã '$code'", $source);
            return;
        }

        // 2. Kiểm tra đã quét chưa
        if ($item->status == ItemStatus::IN_WAREHOUSE || $item->warehoused_at) {
            $this->scanStatus = 'warning';
            $this->message = $code . " ⚠️ Cảnh báo: Mã này ĐÃ ĐƯỢC nhập kho.";

            // Lấy full model ra view để load được Relationship
            $this->itemInfo = Item::with(['product', 'color', 'order', 'photo'])->find($item->id);
            $this->scannedItemId = $item->id;
            $this->editGsm = $item->gsm;
            $this->editNotes = '';
            $this->loadCurrentPhoto($item->id);

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
        $this->itemInfo = Item::with(['product', 'color', 'order', 'photo'])->find($item->id);
        $this->scannedItemId = $item->id; // Lấy ID để có thể bấm nút In Lại
        $this->editGsm = $item->gsm;
        $this->editNotes = '';
        $this->loadCurrentPhoto($item->id);

        $this->dispatch('play-success-sound');

        if ($source == 'mobile') {
            $this->dispatch('show-toast', ...['type' => 'success', 'title' => 'Thành công!', 'text' => 'Đã cập nhật & xác nhận: ' . $code]);
        } else {
            $this->dispatch('focus-input');
        }
    }

    private function respondError(string $msg, string $source)
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
        $this->editGsm = null;
        $this->editNotes = '';
        $this->currentPhoto = null;
        $this->dispatch('resume-camera');
    }

    public function updateGsm()
    {
        if (!$this->scannedItemId) {
            return;
        }

        $item = Item::find($this->scannedItemId);
        if (!$item) {
            $this->dispatch('show-toast', ...[['type' => 'error', 'title' => 'Lỗi!', 'text' => 'Không tìm thấy dữ liệu vải.']]);
            return;
        }

        $oldGsm = $item->gsm;
        $newGsm = (float)$this->editGsm;

        // Lưu lịch sử nếu có thay đổi gsm
        if ($oldGsm != $newGsm) {
            ItemHistory::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'field_name' => 'gsm',
                'old_value' => $oldGsm,
                'new_value' => $newGsm,
                'note' => $this->editNotes,
            ]);
            $item->gsm = $newGsm;
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

    // ========== QUẢN LÝ ẢNH QC ==========

    /**
     * Nhận ảnh base64 từ JS, resize, lưu vào storage
     */
    public function savePhoto(string $base64Data)
    {
        if (!$this->scannedItemId) {
            $this->dispatch('show-toast', ...[['type' => 'error', 'title' => 'Lỗi!', 'text' => 'Chưa có mã vải nào được chọn.']]);
            return;
        }

        // Decode base64
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $imageData = substr($base64Data, strpos($base64Data, ',') + 1);
            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                $this->dispatch('show-toast', ...[['type' => 'error', 'title' => 'Lỗi!', 'text' => 'Ảnh không hợp lệ.']]);
                return;
            }
        } else {
            $this->dispatch('show-toast', ...[['type' => 'error', 'title' => 'Lỗi!', 'text' => 'Định dạng ảnh không hỗ trợ.']]);
            return;
        }

        // Xóa ảnh cũ nếu có
        $existingPhoto = ItemPhoto::where('item_id', $this->scannedItemId)->first();
        if ($existingPhoto) {
            $existingPhoto->delete(); // Tự động xóa file vật lý nhờ model booted()
        }

        // Tạo thư mục và tên file
        $folder = 'item-photos/' . date('Y/m');
        $filename = $this->scannedItemId . '_' . Str::random(8) . '.jpg';
        $storagePath = $folder . '/' . $filename;

        // Lưu file vào disk public
        Storage::disk('public')->put($storagePath, $imageData);

        // Tạo record DB
        $photo = ItemPhoto::create([
            'item_id'  => $this->scannedItemId,
            'user_id'  => Auth::id(),
            'path'     => $storagePath,
            'disk'     => 'public',
            'size'     => strlen($imageData),
        ]);

        $this->loadCurrentPhoto($this->scannedItemId);
        $this->dispatch('show-toast', ...[['type' => 'success', 'title' => 'Đã lưu ảnh!', 'text' => 'Ảnh phiếu vải đã được lưu thành công.']]);
    }

    /**
     * Xóa ảnh hiện tại của item đang xem
     */
    public function deletePhoto()
    {
        if (!$this->scannedItemId) return;

        $photo = ItemPhoto::where('item_id', $this->scannedItemId)->first();
        if ($photo) {
            $photo->delete();
            $this->currentPhoto = null;
            $this->dispatch('show-toast', ...[['type' => 'info', 'title' => 'Đã xóa ảnh', 'text' => 'Bạn có thể chụp lại ảnh mới.']]);
        }
    }

    /**
     * Load ảnh hiện tại của item vào state
     */
    private function loadCurrentPhoto(int $itemId): void
    {
        $photo = ItemPhoto::where('item_id', $itemId)->latest()->first();
        if ($photo) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk($photo->disk);
            $this->currentPhoto = [
                'id'  => $photo->id,
                'url' => $disk->url($photo->path),
                'created_at' => $photo->created_at->format('d/m/Y H:i'),
            ];
        } else {
            $this->currentPhoto = null;
        }
    }

    public function render()
    {
        $this->js("console.log('Quyét mã code')");
        return view('livewire.quality.scan-product');
    }
}
