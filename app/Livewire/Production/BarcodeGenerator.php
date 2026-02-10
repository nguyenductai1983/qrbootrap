<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Livewire\WithPagination;
use App\Models\Department; // Nhớ import Model
use App\Models\Order;
use App\Models\ProductModel;
use Illuminate\Support\Str; // <-- Thêm dòng này ở đầu file
class BarcodeGenerator extends Component
{
    use WithPagination;

    // Cấu hình
    public $type = 'RM';
    public $quantity = 1;
    public $departments = [];
    public $selectedDeptCode = '';
    public $orders = [];
    public $availableModels = []; // Danh sách model thay đổi theo xưởng
    // Dữ liệu nhập liệu
    public $itemData = [
        'ORDER_ID' => '',       // Chọn đơn hàng
        'PRODUCT_MODEL_ID' => '', // Chọn Model
        'PO' => '',
        'MA_VAI' => '',
        'MA_CAY_VAI' => '',
        'MAU' => '',
        'KHO' => '',
        'SO_MET' => '',
        'TRONG_LUONG' => '',
        'CHAT_LUONG' => 'A',
        'GHI_CHU' => ''
    ];

    public $generatedItems = []; // Danh sách tem CHỜ IN (Hiện tại)

    // --- MỚI: Biến lưu danh sách các ID được chọn để in lại ---
    public $selectedHistoryIds = [];
    public $printFormat = 'QR';
    public function mount()
    {
        /** @var \App\Models\User $user */ // <-- Đã thêm dòng fix lỗi IDE
        $user = Auth::user();

        // LOGIC LẤY BỘ PHẬN:
        if ($user->hasRole('admin')) {
            // Nếu là Admin: Lấy tất cả bộ phận có Code
            $this->departments = Department::whereNotNull('code')->get();
        } else {
            // Nếu là Nhân viên: Chỉ lấy những bộ phận họ được gán
            // Sử dụng quan hệ belongsToMany đã khai báo ở Bước 2
            $this->departments = $user->departments;
        }
        // Lấy danh sách Đơn hàng đang chạy
        $this->orders = Order::where('status', 'RUNNING')->orderBy('id', 'desc')->get();
        if (count($this->departments) > 0) {
            $this->selectedDeptCode = $this->departments[0]->code;
            $this->loadModelsByDepartment();
        }
    }
    public function updatedSelectedDeptCode()
    {
        $this->loadModelsByDepartment();
    }
    // Khi người dùng chọn Model -> Tự điền Mã vải vào ô Input
    public function updatedItemDataProductModelId($value)
    {
        $model = ProductModel::find($value);
        if ($model) {
            $this->itemData['MA_VAI'] = $model->code;
        }
    }

    // Khi chọn Order -> Tự điền PO Text
    public function updatedItemDataOrderId($value)
    {
        $order = Order::find($value);
        if ($order) {
            $this->itemData['PO'] = $order->code;
        }
    }

    private function loadModelsByDepartment()
    {
        // Tìm Department theo Code đang chọn
        $dept = Department::where('code', $this->selectedDeptCode)->first();

        if ($dept) {
            // Lấy các model được gán cho Department này
            $this->availableModels = $dept->productModels;
        } else {
            $this->availableModels = [];
        }

        // Reset chọn model
        $this->itemData['PRODUCT_MODEL_ID'] = '';
        $this->itemData['MA_VAI'] = '';
    }

    private function getNextSequence($prefix)
    {
        $lastItem = Item::where('code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastItem) {
            return 1;
        }

        $parts = explode('-', $lastItem->code);
        $lastNumber = end($parts);
        return (int)$lastNumber + 1;
    }

    public function generate()
    {
        $this->validate([
            'selectedDeptCode' => 'required',
            'itemData.ORDER_ID' => 'required',
            'itemData.PRODUCT_MODEL_ID' => 'required',
            'quantity' => 'required|integer|min:1',
        ], [
            'itemData.ORDER_ID.required' => 'Vui lòng chọn Đơn hàng.',
            'itemData.PRODUCT_MODEL_ID.required' => 'Vui lòng chọn Model sản phẩm.',
        ]);

        $this->generatedItems = [];
        $this->selectedHistoryIds = [];

        // Prefix chung (Ví dụ: RMKHO1)
        $prefix = $this->type . $this->selectedDeptCode;

        // Không cần tính $startSeq nữa vì ta sẽ dùng ID

        for ($i = 0; $i < $this->quantity; $i++) {

            // 1. TẠO ITEM VỚI MÃ TẠM (Để lấy được ID từ Database)
            $item = Item::create([
                'code' => (string) Str::uuid(), // Mã tạm ngẫu nhiên để không bị lỗi trùng
                'type' => $this->type,
                'status' => 'NEW',
                'properties' => $this->itemData,
                'created_by' => Auth::id(),
                // Map thêm các cột khóa ngoại nếu bạn đã tạo trong DB
                'order_id' => $this->itemData['ORDER_ID'] ?? null,
                'product_model_id' => $this->itemData['PRODUCT_MODEL_ID'] ?? null,
            ]);

            // 2. SINH MÃ CHÍNH THỨC DỰA TRÊN ID VỪA CÓ
            // Sử dụng str_pad 6 số để mã đẹp và đều (VD: ID 5 -> ...000005)
            // Nếu ID của bạn lớn, nó sẽ tự giãn ra, không bị cắt
            $realCode = strtoupper($prefix . str_pad($item->id, 6, '0', STR_PAD_LEFT));

            // 3. CẬP NHẬT LẠI MÃ THẬT
            $item->update(['code' => $realCode]);

            // 4. Đưa vào danh sách in
            $this->generatedItems[] = [
                'code' => $realCode,
                'info' => $this->itemData
            ];
        }
        session()->flash('message', 'Đã tạo thành công ' . count($this->generatedItems) . ' tem.');
        // --- QUAN TRỌNG: THÊM DÒNG NÀY ĐỂ TỰ ĐỘNG BẬT CỬA SỔ IN ---
        $this->dispatch('trigger-print');
    }

    // --- TÍNH NĂNG MỚI: IN LẠI DANH SÁCH ĐÃ CHỌN ---
    public function reprintSelected()
    {
        if (empty($this->selectedHistoryIds)) {
            return;
        }

        // Lấy tất cả tem trong database dựa theo ID đã check
        $items = Item::whereIn('id', $this->selectedHistoryIds)->get();

        $this->generatedItems = [];

        foreach ($items as $item) {
            $this->generatedItems[] = [
                'code' => $item->code,
                'info' => $item->properties
            ];
        }

        // Kích hoạt lệnh in phía client
        $this->dispatch('trigger-print');
    }

    public function render()
    {
        $historyItems = Item::orderBy('id', 'desc')->paginate(10);
        return view('livewire.production.barcode-generator', [
            'historyItems' => $historyItems
        ]);
    }
}
