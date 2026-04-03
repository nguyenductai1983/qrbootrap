<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Machine;
use Livewire\WithPagination;
use App\Models\Department;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ItemType;
use App\Enums\OrderStatus;
use App\Models\Color;
use App\Models\Specification;
use App\Models\Width;
use App\Models\PlasticType;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Renderless;
use App\Services\ItemCodeService;
use Livewire\Attributes\Title;

#[Title('Phát hành Tem & Barcode Cây Vải')]
class BarcodeGeneratorExcel extends Component
{
    use WithPagination;
    use \App\Livewire\Traits\WithReprinting;

    // Cấu hình
    public $type = '';
    public $itemTypes = [];
    public $availableProducts = [];
    // Dữ liệu nhập liệu
    public $itemData = [];
    public $excelData; // Chứa dữ liệu paste từ Excel
    public $col0Mode = 'quantity'; // 'quantity' hoặc 'sequence'

    public $generatedItems = []; // Danh sách tem CHỜ IN (Hiện tại)
    public $selectedHistoryIds = [];
    public $printFormat = 'QR';
    public $printColumns = 2;
    public $fontSize = 7;
    public $rowsPerPage = 2;
    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->printFormat = cache()->get('excel_printFormat_' . $user->id, 'QR');
        $this->printColumns = cache()->get('excel_printColumns_' . $user->id, 2);
        $this->fontSize = cache()->get('excel_fontSize_' . $user->id, 7);
        $this->rowsPerPage = cache()->get('excel_rowsPerPage_' . $user->id, 2);

        // Lấy danh sách Loại tem đang Active
        $this->itemTypes = ItemType::where('is_active', true)->get();

        if (count($this->itemTypes) > 1) {
            $this->type = $this->itemTypes[1]->code;
        }

        $this->itemData['PRODUCT_ID'] = '';
        $this->itemData['PRODUCT'] = '';
        $this->itemData['PRODUCT_NAME'] = '';

        // Tự động lấy danh sách sản phẩm theo Bộ phận của User
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
            }
        }
        $this->col0Mode = cache()->get('col0Mode' . Auth::id(), 'quantity');
    }

    public function updatedItemDataProductId($value)
    {
        $product = Product::find($value);
        if ($product) {
            $this->itemData['PRODUCT'] = $product->code;
            $this->itemData['PRODUCT_NAME'] = $product->name;
        }
    }


    /**
     * Hàm dùng chung để tìm hoặc tạo mới các thuộc tính (Màu, Khổ, Nhựa, Quy cách...)
     */
    private function resolveAttributeId($modelClass, $code)
    {
        if (empty($code)) {
            return null;
        }

        // Đã gộp mảng name và is_active lại làm 1 để fix lỗi của firstOrNew
        $record = $modelClass::firstOrNew(
            ['code' => $code],
            [
                'name' => $code,
                'is_active' => true
            ]
        );
        $record->save();

        return $record->id;
    }

    public function updatedPrintFormat($value)
    {
        cache()->forever('excel_printFormat_' . Auth::id(), $value);
    }

    public function updatedPrintColumns($value)
    {
        cache()->forever('excel_printColumns_' . Auth::id(), $value);
    }

    public function updatedFontSize($value)
    {
        cache()->forever('excel_fontSize_' . Auth::id(), $value);
    }

    public function updatedRowsPerPage($value)
    {
        cache()->forever('excel_rowsPerPage_' . Auth::id(), $value);
    }

    // 🌟 THÊM KHAI BÁO SERVICE VÀO THAM SỐ CỦA HÀM
    public function generate()
    {
        $this->validate([
            'itemData.PRODUCT_ID' => 'required',
            'excelData' => 'required',
        ], [
            'itemData.PRODUCT_ID.required' => 'Vui lòng chọn Mã Hàng.',
            'excelData.required' => 'Vui lòng dán dữ liệu từ Excel.',
        ]);

        $newItemIds = [];
        $this->selectedHistoryIds = [];
        cache()->forever('col0Mode' . Auth::id(), $this->col0Mode);
        $lines = explode("\n", trim($this->excelData));

        foreach ($lines as $line) {
            if (trim($line) === '') continue;

            $cols = preg_split('/\s+/', trim($line));
            $cols = array_pad($cols, 11, '');

            $inputCol0    = (int) trim($cols[0]); // số thứ tự
            $quantity     = ($this->col0Mode === 'sequence') ? 1 : $inputCol0; // số lượng
            $orderCode    = strtoupper(trim($cols[1])); // mã đơn hàng
            $colorCode    = strtoupper(trim($cols[2])); // mã màu
            $specCode     = strtoupper(trim($cols[3])); // mã quy cách
            $widthCode    = strtoupper(trim($cols[4])); // mã khổ
            $plasticCode  = strtoupper(trim($cols[5])); // mã nhựa
            $gsm          = trim($cols[6]); // định lượng
            $length       = trim($cols[7]); // chiều dài
            $machineNum   = trim($cols[8]); // mã máy
            $weight       = trim($cols[9]); // trọng lượng

            // Xử lý ghi chú có thể chứa khoảng trắng (từ cột 10 trở đi)
            $notes = '';
            if (count($cols) > 10) {
                $notes = trim(implode(' ', array_slice($cols, 10)));
            }

            //nếu không có số lượng thì số lượng = 1
            if ($quantity < 1) $quantity = 1;

            $orderId = null;
            if ($orderCode) {
                $order = Order::firstOrNew(
                    ['code' => $orderCode],
                    ['status' => OrderStatus::RUNNING]
                );
                $order->total = ($order->total ?? 0) + $quantity;
                $order->save();
                $orderId = $order->id;
            }

            $colorId   = $this->resolveAttributeId(Color::class, $colorCode);
            $specId    = $this->resolveAttributeId(Specification::class, $specCode);
            $widthId   = $this->resolveAttributeId(Width::class, $widthCode);
            $plasticId = $this->resolveAttributeId(PlasticType::class, $plasticCode);
            //nếu col0Mode = sequence thì no = inputCol0 - 1, ngược lại thì no = 0
            $no = ($this->col0Mode === 'sequence') ? $inputCol0 - 1 : 0;
            for ($i = 0; $i < $quantity; $i++) {
                $no++;
                $propertiesToSave = [
                    'GSM' => $gsm,
                ];
                $propParts = array_filter([$gsm, $length]);
                try {
                    // 🌟 Tìm hoặc tạo Machine theo mã máy từ Excel
                    $machineId = null;
                    if (!empty($machineNum)) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        $userDeptId = $user->department_id;
                        $userDeptCode = $user->department->code;
                        $machine = Machine::firstOrCreate(
                            ['code' => strtoupper($machineNum)],
                            [
                                'name'          => $userDeptCode . strtoupper($machineNum),
                                'department_id' => $userDeptId,
                                'status'        => true,
                            ]
                        );
                        $machineId = $machine->id;
                    }

                    // 🌟 GỌI SERVICE ĐỂ SINH MÃ TẠI ĐÂY (Code ngắn gọn và sạch sẽ hơn hẳn)
                    $realCode = ItemCodeService::generateStandardCode(
                        $orderCode,
                        $colorCode,
                        $specCode,
                        $widthCode,
                        $plasticCode,
                        $propParts, // <--- Lúc trước bạn truyền riêng lẻ $gsm, $length, giờ bạn truyền nguyên mảng $propParts mà vòng lặp for phía trên của bạn đã tạo ra.
                        $no
                    );

                    $numericLength = is_numeric($length) ? (float) $length : null;

                    /** @var \App\Models\User $currentUser */
                    $currentUser = Auth::user();

                    $itemRecord = Item::create([
                        'code'         => $realCode,
                        'type'         => $this->type,
                        'properties'   => $propertiesToSave,
                        'created_by'   => Auth::id(),
                        'color_id'         => $colorId,
                        'specification_id' => $specId,
                        'plastic_type_id'  => $plasticId,
                        'width_id'         => $widthId,
                        'order_id'         => $orderId,
                        'product_id'       => $this->itemData['PRODUCT_ID'] ?? null,
                        'original_length'  => $numericLength,
                        'length'           => $numericLength,
                        'department_id'    => $currentUser->department_id,
                        'machine_id'       => $machineId,
                        'gsm'              => is_numeric($gsm) ? (float) $gsm : null,
                        'weight'           => is_numeric($weight) ? (float) $weight : null,
                        'notes'            => $notes,
                    ]);

                    $newItemIds[] = $itemRecord->id;
                } catch (QueryException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        session()->flash('error', "Lỗi: Mã tem '{$realCode}' đã tồn tại trong hệ thống! Vui lòng kiểm tra lại số thứ tự hoặc dữ liệu Excel.");
                        return;
                    }
                    session()->flash('error', 'Lỗi hệ thống Database: ' . $e->getMessage());
                    return;
                }
            }
        }

        session()->flash('message', 'Đã tạo thành công ' . count($newItemIds) . ' tem.');
        $this->excelData = '';

        if (count($newItemIds) > 0) {
            $this->reprintItems(
                $newItemIds,
                $this->printFormat,
                $this->printColumns,
                $this->rowsPerPage,
                $this->fontSize
            );
        }
    }
    public function reprintSelected()
    {
        if (empty($this->selectedHistoryIds)) {
            return;
        }

        $this->reprintItems(
            $this->selectedHistoryIds,
            $this->printFormat,
            $this->printColumns,
            $this->rowsPerPage,
            $this->fontSize
        );

        $this->selectedHistoryIds = [];
    }

    public function render()
    {
        $historyItems = Item::orderBy('id', 'desc')->paginate(20);
        return view('livewire.production.barcode-generator-excel', [
            'historyItems' => $historyItems
        ]);
    }
}
