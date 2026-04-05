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
use Livewire\Attributes\Title;
use App\Services\ItemCodeService;
use Illuminate\Database\QueryException;

#[Title('Phát hành Tem & Barcode Cây Vải')]
class BarcodeGenerator extends Component
{
    use WithPagination;
    // Cấu hình
    public $type = ''; // Bỏ giá trị 'RM' mặc định đi
    public $itemTypes = []; // Danh sách loại tem để hiện lên select box
    public $quantity = 1;
    public $orders = [];
    public $availableProducts = []; // Danh sách model thay đổi theo xưởng
    // Dữ liệu nhập liệu
    public $itemData = [];
    public $dynamicProperties = []; // Biến lưu danh sách thuộc tính động

    public $generatedItems = []; // Danh sách tem CHỜ IN (Hiện tại)

    // --- MỚI: Biến lưu danh sách các ID được chọn để in lại ---
    public $selectedHistoryIds = [];
    public $printFormat = 'QR';
    public $printColumns = 4;
    public $fontSize = 7;
    public $rowsPerPage = 4; // Bổ sung cấu hình số hàng
    public $colors, $specifications, $plasticTypes, $widths;
    public $selectedColor, $selectedSpec, $selectedPlastic, $selectedWidth;
    // --- BIẾN CHO TẠO NHANH ĐƠN HÀNG ---
    public $newOrderType = 'C'; // Mặc định là loại C
    public $newOrderCustomer = '';
    public $newOrderTotal = 1;
    public $length;
    public $gsm;
    public $weight;
    public $notes;
    public function mount()
    {
        /** @var \App\Models\User $user */ // <-- Đã thêm dòng fix lỗi IDE
        $user = Auth::user();

        // 🌟 Lấy cấu hình in từ Cache giống Excel
        $this->printFormat = cache()->get('normal_printFormat_' . $user->id, 'QR');
        $this->printColumns = cache()->get('normal_printColumns_' . $user->id, 4);
        $this->fontSize = cache()->get('normal_fontSize_' . $user->id, 7);
        $this->rowsPerPage = cache()->get('normal_rowsPerPage_' . $user->id, 4);

        $this->colors = Color::where('is_active', true)->get();
        $this->specifications = Specification::where('is_active', true)->get();
        $this->plasticTypes = PlasticType::where('is_active', true)->get();
        $this->widths = Width::where('is_active', true)->get();

        // Lấy danh sách Loại tem đang Active
        $this->itemTypes = ItemType::where('is_active', true)->get();

        if (count($this->itemTypes) > 1) {
            $this->type = $this->itemTypes[0]->id;
        }

        // Lấy danh sách Đơn hàng đang chạy
        $this->orders = Order::whereIn('status', [OrderStatus::RUNNING->value, OrderStatus::PENDING->value])->orderBy('id', 'desc')->get();

        // 🌟 BƯỚC 1: KHỞI TẠO DỮ LIỆU RỖNG TRƯỚC 🌟
        $this->itemData['ORDER_ID'] = '';
        $this->itemData['PRODUCT_ID'] = '';
        $this->itemData['PRODUCT'] = '';
        $this->itemData['PRODUCT_NAME'] = '';

        // 🌟 BƯỚC 2: TỰ ĐỘNG TẢI SẢN PHẨM TỪ XƯỞNG CỦA USER 🌟
        if ($user->department) {
            $this->availableProducts = $user->department->products;

            if (count($this->availableProducts) > 0) {
                $firstProduct = collect($this->availableProducts)->first();

                $productId = is_array($firstProduct) ? $firstProduct['id'] : $firstProduct->id;
                $productCode = is_array($firstProduct) ? $firstProduct['code'] : $firstProduct->code;
                $productName = is_array($firstProduct) ? $firstProduct['name'] : $firstProduct->name;

                $this->itemData['PRODUCT_ID'] = $productId;
                $this->itemData['PRODUCT'] = $productCode;
                $this->itemData['PRODUCT_NAME'] = $productName;

                // Load thuộc tính động
                $this->loadDynamicProperties($productId);
            } else {
                $this->loadDynamicProperties(null);
            }
        } else {
            // Nếu User không có xưởng nào, chỉ load các thuộc tính chung chung
            $this->loadDynamicProperties(null);
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
    //     // Chạy lại logic load sản phẩm theo Bộ phận đang chọn
    //     $this->loadProductsByDepartment();

    //     // Hoặc bạn có thể thêm hiệu ứng thông báo Toast ở đây
    //     // $this->dispatch('show-toast', title: 'Danh sách sản phẩm vừa được làm mới!');
    // }

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

    // --- LƯU CẤU HÌNH IN VÀO CACHE TỰ ĐỘNG ---
    public function updatedPrintFormat($value)
    {
        cache()->forever('normal_printFormat_' . Auth::id(), $value);
    }

    public function updatedPrintColumns($value)
    {
        cache()->forever('normal_printColumns_' . Auth::id(), $value);
    }

    public function updatedFontSize($value)
    {
        cache()->forever('normal_fontSize_' . Auth::id(), $value);
    }

    public function updatedRowsPerPage($value)
    {
        cache()->forever('normal_rowsPerPage_' . Auth::id(), $value);
    }

    public function generate()
    {
        // 1. Định nghĩa các quy tắc kiểm tra cố định ban đầu
        $rules = [
            'itemData.PRODUCT_ID' => 'required',
            'quantity' => 'required|integer|min:1',
            'selectedWidth' => 'required',
            'selectedColor' => 'required',
            'selectedSpec' => 'required',
            'selectedPlastic' => 'required',
            'length' => 'required|numeric|min:0.1',
            'gsm' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ];

        // Định nghĩa các câu báo lỗi bằng tiếng Việt cho các trường cố định
        $messages = [
            'itemData.PRODUCT_ID.required' => 'Vui lòng chọn Mã Hàng.',
            'selectedWidth.required' => 'Vui lòng chọn Khổ.',
            'selectedColor.required' => 'Vui lòng chọn Màu.',
            'selectedSpec.required' => 'Vui lòng chọn Quy cách.',
            'selectedPlastic.required' => 'Vui lòng chọn Loại nhựa.',
            'quantity.min' => 'Số lượng ít nhất phải là 1.',
            'length.required' => 'Vui lòng nhập chiều dài.',
            'length.numeric' => 'Chiều dài phải là số.',
            'length.min' => 'Chiều dài tối thiểu là 0.1.',
            'gsm.numeric' => 'Định lượng (gsm) phải là số.',
            'weight.numeric' => 'Trọng lượng phải là số.',
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
        // 4. Nếu vượt qua kiểm tra -> Bắt đầu logic tạo tem
        $orderCode = $this->itemData['ORDER_CODE'] ?? null;
        $orderId = null;
        $quantity = $this->quantity;
        $start = 1;
        $endnumber = $quantity;
        if ($orderCode) {
            // 1. Dùng firstOrNew thay vì firstOrCreate
            $order = Order::firstOrNew(
                ['code' => $orderCode], // Mảng 1: Điều kiện tìm kiếm
                ['status' => OrderStatus::RUNNING] // Mảng 2: Thuộc tính gán sẵn nếu là tạo mới
            );

            // 2. Xử lý logic cộng dồn
            // Nếu là Order mới, $order->total sẽ là null, ta dùng toán tử ?? 0 để ép nó về 0 rồi cộng với $quantity.
            // Nếu là Order cũ, nó lấy total cũ cộng thêm $quantity.
            $start = $order->total + 1;
            $endnumber = $start + $quantity;
            $order->total = ($order->total ?? 0) + $quantity;

            // 3. Tiến hành lưu vào Database (Lúc này mới thực sự chạy lệnh INSERT hoặc UPDATE)
            $order->save();

            // 4. Bất kể là tìm thấy hay tạo mới, ta luôn có đối tượng $order và lấy được ID
            $orderId = $order->id;
        }
        $this->itemData['ORDER_ID'] = (string) $orderId; // Ép kiểu chuỗi để lưu vào properties

        $this->generatedItems = [];
        $this->selectedHistoryIds = [];
        // Prefix chung (Ví dụ: RMKHO1)
        $widthCode = Width::find($this->selectedWidth)?->code ?? '';
        $colorCode = Color::find($this->selectedColor)?->code ?? '';
        $specCode = Specification::find($this->selectedSpec)?->code ?? '';
        $plasticCode = PlasticType::find($this->selectedPlastic)?->code ?? '';
        $gsm = $this->gsm;
        $length = $this->length;
        // Mã đơn hang + Mã khổ + Mã màu + Mã quy cách + Mã loại nhựa
        // Các thuộc tính động: GSM, Độ dài, trọng lượng, v.v... và sẽ  ID được tự động ghép vào cuối cùng để đảm bảo tính duy nhất

        for ($i = $start; $i < $endnumber; $i++) {

            // CHỈ lưu các trường thuộc tính động thực sự vào cột properties, loại bỏ các biến hệ thống (như PRODUCT_ID, PRODUCT_NAME)
            $propertiesToSave = [];
            foreach ($this->dynamicProperties as $prop) {
                if (isset($this->itemData[$prop->code])) {
                    $propertiesToSave[$prop->code] = $this->itemData[$prop->code];
                }
            }
            try {
                $realCode = ItemCodeService::generateStandardCode(
                    $orderCode,
                    $colorCode,
                    $specCode,
                    $widthCode,
                    $plasticCode,
                    $gsm,
                    $length,
                    $i
                );
                // 1. TẠO ITEM VỚI MÃ TẠM (Để lấy được ID từ Database)
                $item = Item::create([
                    'code' => $realCode, // Mã tạm ngẫu nhiên để không bị lỗi trùng
                    'type' => $this->type,
                    'status' => 1,
                    'properties' => $propertiesToSave,
                    'created_by' => Auth::id(),
                    'color_id'         => $this->selectedColor ?: null,
                    'specification_id' => $this->selectedSpec ?: null,
                    'plastic_type_id'  => $this->selectedPlastic ?: null,
                    'width_id'         => $this->selectedWidth ?: null,
                    // Map thêm các cột khóa ngoại nếu bạn đã tạo trong DB
                    'order_id' => !empty($this->itemData['ORDER_ID']) ? $this->itemData['ORDER_ID'] : null,
                    'product_id' => !empty($this->itemData['PRODUCT_ID']) ? $this->itemData['PRODUCT_ID'] : null,
                    'original_length' => $this->length ? (float) $this->length : null,
                    'length'          => $this->length ? (float) $this->length : null,
                    'gsm'             => is_numeric($this->gsm) ? (float) $this->gsm : null,
                    'weight'          => is_numeric($this->weight) ? (float) $this->weight : null,
                    'notes'           => $this->notes ? trim($this->notes) : null,
                ]);
                // 2. SINH MÃ CHÍNH THỨC DỰA TRÊN ID VỪA CÓ
                // Sử dụng str_pad 6 số để mã đẹp và đều (VD: ID 5 -> ...000005)
                // Nếu ID của bạn lớn, nó sẽ tự giãn ra, không bị cắt
                // Thêm thuộc tính động vào item
                // 1. Tạo một mảng trống để chứa các cụm thuộc tính
                $propParts = [];

                foreach ($this->dynamicProperties as $prop) {
                    if (isset($this->itemData[$prop->code]) && $this->itemData[$prop->code] !== '') {
                        if ($prop->is_code) {
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
                }

                // 2. Dùng implode để ghép mảng lại bằng khoảng trắng
                // Lệnh này tự động ráp: "cụm 1" + " " + "cụm 2" + " " + "cụm 3" (Không bị dư ở cuối)
                // 4. Đưa vào danh sách in
                $printInfo = $this->itemData;
                $printInfo['type'] = $this->type; // <-- Bổ sung thêm type vào thông tin in mới
                $printInfo['PO'] = $orderCode ?? ''; // <-- Bổ sung thêm PO vào thông tin in mới
                $printInfo['PRODUCT_NAME'] = $this->itemData['PRODUCT_NAME'] ?? '';
                $printInfo['COLOR_NAME'] = Color::find($this->selectedColor)->name ?? '';
                $printInfo['LENGTH'] = $this->length;
                $printInfo['GSM'] = $this->gsm;
                $printInfo['WEIGHT'] = $this->weight;
                $printInfo['NOTES'] = $this->notes;
                $this->generatedItems[] = [
                    'code' => $realCode,
                    'info' => $printInfo
                ];
            } catch (QueryException $e) {
                if ($e->errorInfo[1] == 1062) {
                    session()->flash('error', "Lỗi: Mã tem '{$realCode}' đã tồn tại trong hệ thống! Vui lòng kiểm tra lại dữ liệu đầu vào.");
                    return;
                }
                session()->flash('error', 'Lỗi hệ thống Database: ' . $e->getMessage());
                return;
            }
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
        // 🌟 TỐI ƯU TỐC ĐỘ BẰNG EAGER LOADING (Thêm hàm with) 🌟
        // Lấy tất cả tem, gom luôn quan hệ order và product trong 1 lần query duy nhất
        $items = Item::with(['order', 'product'])
            ->whereIn('id', $this->selectedHistoryIds)
            ->get();

        $this->generatedItems = [];

        foreach ($items as $item) {
            $this->generatedItems[] = [
                'code' => $item->code,
            ];
        }

        // Bỏ chọn các checkbox sau khi đã lấy xong dữ liệu để dọn dẹp giao diện
        $this->selectedHistoryIds = [];

        $this->dispatch('trigger-print');
    }

    public function render()
    {
        $this->js("console.log('Tạo mã code')");
        $historyItems = Item::orderBy('id', 'desc')->paginate(20);
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
    public function refreshDynamicProperties()
    {
        // Lấy ID Sản phẩm đang được chọn trong Form (nếu có)
        $currentProductId = $this->itemData['PRODUCT_ID'] ?? null;

        // Kích hoạt lại hàm chuyên trách tải thuộc tính động
        $this->loadDynamicProperties($currentProductId);

        // (Tùy chọn) Báo cho Console biết
        $this->js("console.log('🔄 Đã tự động cập nhật danh sách Thuộc tính động!');");
    }
}
