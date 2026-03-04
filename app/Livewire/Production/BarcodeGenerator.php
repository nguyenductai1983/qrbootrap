<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Livewire\WithPagination;
use App\Models\Department; // Nhớ import Model
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str; // <-- Thêm dòng này ở đầu file
use App\Models\ItemProperty; // Thêm model này
use App\Models\ItemType;
use Livewire\Attributes\On; // Nhớ import
use SebastianBergmann\Environment\Console;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Specification;
use App\Models\Color;
use App\Models\PlasticType;
use App\Models\Width;

class BarcodeGenerator extends Component
{
    use WithPagination;

    // Cấu hình
    public $type = ''; // Bỏ giá trị 'RM' mặc định đi
    public $itemTypes = []; // Danh sách loại tem để hiện lên select box
    public $quantity = 1;
    public $departments = [];
    public $selectedDeptCode = '';
    public $orders = [];
    public $availableProducts = []; // Danh sách model thay đổi theo xưởng
    // Dữ liệu nhập liệu
    public $itemData = [];
    public $dynamicProperties = []; // Biến lưu danh sách thuộc tính động

    public $generatedItems = []; // Danh sách tem CHỜ IN (Hiện tại)

    // --- MỚI: Biến lưu danh sách các ID được chọn để in lại ---
    public $selectedHistoryIds = [];
    public $printFormat = 'QR';
    public $printColumns = 1;
    public $colors, $specifications, $plasticTypes, $widths;
    public $selectedColor, $selectedSpec, $selectedPlastic, $selectedWidth;
    // --- BIẾN CHO TẠO NHANH ĐƠN HÀNG ---
    public $newOrderType = 'C'; // Mặc định là loại C
    public $newOrderCustomer = '';
    public $newOrderTotal = 1;
    public function mount()
    {
        /** @var \App\Models\User $user */ // <-- Đã thêm dòng fix lỗi IDE
        $user = Auth::user();
        $this->colors = Color::where('is_active', true)->get();
        $this->specifications = Specification::where('is_active', true)->get();
        $this->plasticTypes = PlasticType::where('is_active', true)->get();
        $this->widths = Width::where('is_active', true)->get();
        // LOGIC LẤY BỘ PHẬN:
        if ($user->hasRole('admin')) {
            // Nếu là Admin: Lấy tất cả bộ phận có Code
            $this->departments = Department::whereNotNull('code')->get();
        } else {
            // Nếu là Nhân viên: Chỉ lấy những bộ phận họ được gán
            // Sử dụng quan hệ belongsToMany đã khai báo ở Bước 2
            $this->departments = $user->departments;
        }
        // Lấy danh sách Loại tem đang Active
        $this->itemTypes = ItemType::where('is_active', true)->get();

        // Tự động chọn loại tem đầu tiên nếu có
        if (count($this->itemTypes) > 0) {
            $this->type = $this->itemTypes[0]->code;
        }
        // Lấy danh sách Đơn hàng đang chạy
        $this->orders = Order::whereIn('status', [OrderStatus::RUNNING->value, OrderStatus::PENDING->value])->orderBy('id', 'desc')->get();
        if (count($this->departments) > 0) {
            $this->selectedDeptCode = $this->departments[0]->code;
            $this->loadProductsByDepartment();
        }
        // Lấy danh sách thuộc tính động đang Active, sắp xếp theo thứ tự
        $this->dynamicProperties = ItemProperty::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
        $this->loadDynamicProperties(null);
        // Khởi tạo mảng itemData với các key động
        $this->itemData['ORDER_ID'] = '';
        $this->itemData['PRODUCT_ID'] = '';
        $this->itemData['PRODUCT_NAME'] = ''; // <--- THÊM DÒNG NÀY
        foreach ($this->dynamicProperties as $prop) {
            $this->itemData[$prop->code] = '';
        }
    }
    // Hàm này tự chạy khi người dùng thay đổi giá trị của select box Chọn Product
    public function updatedItemDataProductId($value)
    {
        $product = Product::find($value);
        if ($product) {
            $this->itemData['PRODUCT'] = $product->code;
            $this->itemData['PRODUCT_NAME'] = $product->name;
            // Load lại danh sách thuộc tính dựa vào Product vừa chọn
            $this->loadDynamicProperties($value);
        } else {
            // Nếu người dùng chọn "-- Chọn Mã Hàng --" (value rỗng)
            $this->loadDynamicProperties(null);
        }
    }

    // Hàm xử lý lấy thuộc tính thông minh
    private function loadDynamicProperties($productId)
    {
        $query = ItemProperty::where('is_active', true)
            ->where(function ($q) use ($productId) {
                $q->where('is_global', true); // Luôn lấy thuộc tính chung

                // Nếu có ID model, lấy thêm thuộc tính riêng của model đó
                if ($productId) {
                    $q->orWhereHas('products', function ($q2) use ($productId) {
                        $q2->where('products.id', $productId);
                    });
                }
            })
            ->orderBy('sort_order', 'asc');

        $this->dynamicProperties = $query->get();

        // Giữ nguyên dữ liệu người dùng đang nhập, tạo key mới nếu chưa có
        foreach ($this->dynamicProperties as $prop) {
            if (!isset($this->itemData[$prop->code])) {
                $this->itemData[$prop->code] = '';
            }
        }
    }
    public function updatedSelectedDeptCode()
    {
        $this->js("console.log('🔵 Chọn BP: {$this->selectedDeptCode}');");

        // Khi đổi phân xưởng -> Load lại danh sách Mã Hàng
        $this->loadProductsByDepartment();

        // ĐỒNG THỜI Xóa trắng Mã Hàng đang chọn (Vì xưởng mới không có mã hàng cũ)
        $this->itemData['PRODUCT_ID'] = '';
        $this->itemData['PRODUCT'] = '';
        $this->itemData['PRODUCT_NAME'] = '';

        // Reset luôn cả danh sách thuộc tính động
        $this->loadDynamicProperties(null);
    }

    // Khi chọn Order -> Tự điền PO Text
    public function updatedItemDataOrderId($value)
    {
        $this->js("console.log('Đổi Đơn hàng sang: {$value}');");
        $order = Order::find($value);
        if ($order) {
            $this->itemData['PO'] = $order->code;
        }
    }
    // #[On('product-list-changed')] // Lắng nghe sự kiện
    // public function refreshProductList()
    // {
    //     // Chạy lại logic load sản phẩm theo phòng ban đang chọn
    //     $this->loadProductsByDepartment();

    //     // Hoặc bạn có thể thêm hiệu ứng thông báo Toast ở đây
    //     // $this->dispatch('show-toast', title: 'Danh sách sản phẩm vừa được làm mới!');
    // }
    private function loadProductsByDepartment()
    {
        $this->js("console.log('Đổi Sản phẩm với BP: {$this->selectedDeptCode}');");
        // Tìm Department theo Code đang chọn
        $dept = Department::where('code', $this->selectedDeptCode)->first();

        if ($dept) {
            // Lấy các model được gán cho Department này
            $this->availableProducts = $dept->products;
        } else {
            $this->availableProducts = [];
        }

        // Reset chọn model
        // $this->itemData['PRODUCT_ID'] = '';
        // $this->itemData['PRODUCT'] = '';
        // $this->itemData['PRODUCT_NAME'] = ''; // <--- THÊM DÒNG NÀY
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
        // 1. Định nghĩa các quy tắc kiểm tra cố định ban đầu
        $rules = [
            'selectedDeptCode' => 'required',
            'itemData.PRODUCT_ID' => 'required',
            'quantity' => 'required|integer|min:1',
            'selectedWidth' => 'required',
            'selectedColor' => 'required',
            'selectedSpec' => 'required',
            'selectedPlastic' => 'required',
        ];

        // Định nghĩa các câu báo lỗi bằng tiếng Việt cho các trường cố định
        $messages = [
            'selectedDeptCode.required' => 'Vui lòng chọn Phân xưởng.',
            'itemData.PRODUCT_ID.required' => 'Vui lòng chọn Mã Hàng.',
            'selectedWidth.required' => 'Vui lòng chọn Khổ.',
            'selectedColor.required' => 'Vui lòng chọn Màu.',
            'selectedSpec.required' => 'Vui lòng chọn Quy cách.',
            'selectedPlastic.required' => 'Vui lòng chọn Loại nhựa.',
            'quantity.min' => 'Số lượng ít nhất phải là 1.',
        ];

        // 2. 🌟 QUÉT THUỘC TÍNH ĐỘNG: Nếu is_required = true thì thêm vào mảng Rules 🌟
        foreach ($this->dynamicProperties as $prop) {
            if ($prop->is_required) {
                // Thêm rule required cho ô nhập liệu này
                $rules['itemData.' . $prop->code] = 'required';

                // Thêm câu thông báo lỗi tương ứng với Tên thuộc tính
                $messages['itemData.' . $prop->code . '.required'] = 'Vui lòng nhập ' . $prop->name . '.';
            }
        }

        // 3. Thực thi kiểm tra với mảng động vừa tạo
        $this->validate($rules, $messages);

        $this->generatedItems = [];
        $this->selectedHistoryIds = [];
        // Prefix chung (Ví dụ: RMKHO1)
        $widthCode = Width::find($this->selectedWidth)?->code ?? '';
        $colorCode = Color::find($this->selectedColor)?->code ?? '';
        $specCode = Specification::find($this->selectedSpec)?->code ?? '';
        $plasticCode = PlasticType::find($this->selectedPlastic)?->code ?? '';
        $prefix = $widthCode . ' ' . $colorCode . ' ' . $specCode . ' ' . $plasticCode;

        // Không cần tính $startSeq nữa vì ta sẽ dùng ID

        for ($i = 0; $i < $this->quantity; $i++) {

            // 1. TẠO ITEM VỚI MÃ TẠM (Để lấy được ID từ Database)
            $item = Item::create([
                'code' => (string) Str::uuid(), // Mã tạm ngẫu nhiên để không bị lỗi trùng
                'type' => $this->type,
                'status' => 1,
                'properties' => $this->itemData,
                'created_by' => Auth::id(),
                'color_id' => $this->selectedColor,
                'specification_id' => $this->selectedSpec,
                'plastic_type_id' => $this->selectedPlastic,
                'width_id' => $this->selectedWidth,
                // Map thêm các cột khóa ngoại nếu bạn đã tạo trong DB
                'order_id' => !empty($this->itemData['ORDER_ID']) ? $this->itemData['ORDER_ID'] : null,
                'product_id' => !empty($this->itemData['PRODUCT_ID']) ? $this->itemData['PRODUCT_ID'] : null,
            ]);
            // 2. SINH MÃ CHÍNH THỨC DỰA TRÊN ID VỪA CÓ
            // Sử dụng str_pad 6 số để mã đẹp và đều (VD: ID 5 -> ...000005)
            // Nếu ID của bạn lớn, nó sẽ tự giãn ra, không bị cắt
            // Thêm thuộc tính động vào item
            // 1. Tạo một mảng trống để chứa các cụm thuộc tính
            $propParts = [];

            foreach ($this->dynamicProperties as $prop) {
                if (isset($this->itemData[$prop->code]) && $this->itemData[$prop->code] !== '') {

                    $part = ''; // Biến tạm chứa chuỗi của riêng thuộc tính này

                    // Nếu admin bật code_usage -> Nối Code (VD: "GSM ")
                    if ($prop->code_usage == 1) {
                        $part .= $prop->code;
                    }

                    // Nối thêm Value và Unit (VD: "165" + "g" -> "165g")
                    $part .= $this->itemData[$prop->code] . ($prop->unit ?? '');

                    // Đẩy cụm hoàn chỉnh (VD: "GSM 165g" hoặc chỉ "165g") vào mảng
                    $propParts[] = trim($part);
                }
            }

            // 2. Dùng implode để ghép mảng lại bằng khoảng trắng
            // Lệnh này tự động ráp: "cụm 1" + " " + "cụm 2" + " " + "cụm 3" (Không bị dư ở cuối)
            $code_properties = '';
            if (count($propParts) > 0) {
                // Thêm 1 khoảng trắng ở đầu và 1 ở cuối để cách ly với Prefix và Số thứ tự (ID)
                $code_properties = ' ' . implode(' ', $propParts) . ' ';
            } else {
                // Nếu không có thuộc tính nào, chỉ cần 1 khoảng trắng để cách ly Prefix và ID
                $code_properties = ' ';
            }

            // 3. Ghép mã cuối cùng (Ví dụ: K1800 WE ONG/MANH 000005)
            $realCode = strtoupper($prefix . $code_properties . str_pad($item->id, 3, '0', STR_PAD_LEFT));
            // 3. CẬP NHẬT LẠI MÃ THẬT
            $item->update(['code' => $realCode]);

            // 4. Đưa vào danh sách in
            $printInfo = $this->itemData;
            $printInfo['type'] = $this->type; // <-- Bổ sung thêm type vào thông tin in mới
            $this->generatedItems[] = [
                'code' => $realCode,
                'info' => $printInfo
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
            $info = $item->properties ?? []; // Lấy JSON hiện tại

            // NẾU LÀ TEM CŨ (Chưa có key PRODUCT_NAME trong JSON), ta tự động bổ sung vào lúc in
            if (!isset($info['PRODUCT_NAME']) && $item->product) {
                $info['PRODUCT_NAME'] = $item->product->name;
            }
            $info['type'] = $item->type;
            $this->generatedItems[] = [
                'code' => $item->code,
                'info' => $info
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
    public function quickCreateOrder()
    {
        // 1. Validate dữ liệu nhập vào
        $this->validate([
            'newOrderType' => 'required',
            'newOrderCustomer' => 'required|string|max:255',
        ]);

        // 2. Lấy tháng và năm hiện tại
        $year = date('y'); // Lấy 2 số cuối của năm (ví dụ: 2026 -> 26)
        $month = date('m');
        $yearcount = date('Y'); // Lấy đầy đủ 4 số của năm (ví dụ: 2026)
        // 3. Đếm số đơn hàng đã tạo TRONG NĂM NAY để làm số thứ tự (STT)
        $countThisYear = Order::whereYear('created_at', $yearcount)->count();
        $sequence = $countThisYear + 1;

        // 4. Lắp ráp quy tắc sinh Mã (Code): Loại + STT (3 chữ số) + Tháng + Năm
        // Hàm sprintf('%03d', $sequence) sẽ biến 1 thành '001', 12 thành '012'
        // Ví dụ kết quả: C001-03-2026
        $orderCode = $this->newOrderType . sprintf('%03d', $sequence) .  $month .  $year;
        // 5. Lưu vào Database
        $order = Order::create([
            'code' => $orderCode,
            'type' => $this->newOrderType, // Lưu ý: Database phải nhận App\Enums\OrderType
            'total' => $this->newOrderTotal ?? 0, // Lưu tổng số lượng vào cột total
            'customer_name' => $this->newOrderCustomer,
            'status' => OrderStatus::PENDING, // Trạng thái mặc định
        ]);
        // 6. Cập nhật lại danh sách Dropdown đơn hàng
        $this->orders = Order::orderBy('id', 'desc')->get();
        // 7. TỰ ĐỘNG CHỌN luôn đơn hàng vừa tạo vào ô Chọn PO
        $tempData = $this->itemData;
        $tempData['ORDER_ID'] = (string) $order->id; // Ép kiểu chuỗi
        $tempData['PO'] = $order->code;
        // Gọi thủ công hàm này để đảm bảo mọi logic ăn theo đều hoạt động
        $this->itemData = $tempData; // Ụp ngược lại mảng chính để kích hoạt updatedItemDataOrderId
        // 6. Reset lại ô nhập liệu trên Modal cho lần sau
        $this->reset(['newOrderType', 'newOrderCustomer']);
        // 8. Đóng Modal, xóa form và báo thành công
        $this->dispatch('close-quick-order-modal');
        session()->flash('message', 'Đã tạo nhanh đơn hàng: ' . $orderCode);
    }
    public function refreshMasterData()
    {
        $this->colors = Color::where('is_active', true)->get();
        $this->specifications = Specification::where('is_active', true)->get();
        $this->plasticTypes = PlasticType::where('is_active', true)->get();
        $this->widths = Width::where('is_active', true)->get();

        // (Tùy chọn) Bạn có thể cho in log ra màn hình console để biết nó đang tự động chạy
        // $this->js("console.log('🔄 Đã tự động cập nhật danh mục mới nhất!');");
    }
    public function refreshdynamicProperties()
    {
        $this->dynamicProperties = ItemProperty::where('is_active', true)->get();
    }
}
