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

class BarcodeGeneratorExcel extends Component
{
    use WithPagination;

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

        // Tự động lấy danh sách sản phẩm theo phòng ban của User
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

        $this->generatedItems = [];
        $this->selectedHistoryIds = [];
        cache()->forever('col0Mode' . Auth::id(), $this->col0Mode);
        $lines = explode("\n", trim($this->excelData));

        foreach ($lines as $line) {
            if (trim($line) === '') continue;

            $cols = preg_split('/\s+/', trim($line));
            $cols = array_pad($cols, 9, '');

            $inputCol0    = (int) trim($cols[0]);
            $quantity     = ($this->col0Mode === 'sequence') ? 1 : $inputCol0;
            $orderCode    = strtoupper(trim($cols[1]));
            $colorCode    = strtoupper(trim($cols[2]));
            $specCode     = strtoupper(trim($cols[3]));
            $widthCode    = strtoupper(trim($cols[4]));
            $plasticCode  = strtoupper(trim($cols[5]));
            $gsm          = trim($cols[6]);
            $length       = trim($cols[7]);
            $machineNum   = trim($cols[8]);

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

            $no = ($this->col0Mode === 'sequence') ? $inputCol0 - 1 : 0;
            for ($i = 0; $i < $quantity; $i++) {
                $no++;
                $propertiesToSave = [
                    'ORDER_ID' => $orderId,
                    'PRODUCT_ID' => $this->itemData['PRODUCT_ID'] ?? '',
                    'PRODUCT_NAME' => $this->itemData['PRODUCT_NAME'] ?? '',
                    'NHUA' => $plasticCode,
                    'GSM' => $gsm,
                    'DAI' => $length,
                    'MAY' => $machineNum,
                ];
                $propParts = array_filter([$gsm, $length]);
                try {
                    // 🌟 Tìm hoặc tạo Machine theo mã máy từ Excel
                    $machineId = null;
                    if (!empty($machineNum)) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        $userDeptId = $user->department_id;

                        $machine = Machine::firstOrCreate(
                            ['code' => strtoupper($machineNum)],
                            [
                                'name'          => 'Máy ' . strtoupper($machineNum),
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

                    Item::create([
                        'code'         => $realCode,
                        'type'         => $this->type,
                        'status'       => 1,
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
                    ]);
                } catch (QueryException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        session()->flash('error', "Lỗi: Mã tem '{$realCode}' đã tồn tại trong hệ thống! Vui lòng kiểm tra lại số thứ tự hoặc dữ liệu Excel.");
                        return;
                    }
                    session()->flash('error', 'Lỗi hệ thống Database: ' . $e->getMessage());
                    return;
                }

                $printInfo = $propertiesToSave;
                $printInfo['type'] = $this->type;
                $printInfo['PO'] = $orderCode;
                $printInfo['COLOR_NAME'] = $colorCode;

                $this->generatedItems[] = [
                    'code' => $realCode,
                    'info' => $printInfo
                ];
            }
        }

        session()->flash('message', 'Đã tạo thành công ' . count($this->generatedItems) . ' tem.');
        $this->excelData = '';
        $this->dispatch('trigger-print');
    }
    public function reprintSelected()
    {
        if (empty($this->selectedHistoryIds)) {
            return;
        }

        $items = Item::with(['order', 'product'])
            ->whereIn('id', $this->selectedHistoryIds)
            ->get();

        $this->generatedItems = [];

        foreach ($items as $item) {
            $this->generatedItems[] = [
                'code' => $item->code,
            ];
        }

        $this->selectedHistoryIds = [];
        $this->dispatch('trigger-print');
    }

    public function render()
    {
        $historyItems = Item::orderBy('id', 'desc')->paginate(20);
        return view('livewire.production.barcode-generator-excel', [
            'historyItems' => $historyItems
        ]);
    }
}
