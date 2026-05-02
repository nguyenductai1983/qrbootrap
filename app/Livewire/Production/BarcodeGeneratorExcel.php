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
    public $type = 0;
    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemType> */
    public $itemTypes = [];
    public $availableProducts = [];
    // Dữ liệu nhập liệu
    public $itemData = [];
    public $excelData = ''; // Chứa dữ liệu paste từ Excel
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

        if (count($this->itemTypes) > 0) {
            $this->type = $this->itemTypes[0]->id;
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

    public function updatedItemDataProductId(mixed $value)
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
    private function resolveAttributeId(string $modelClass, string $code)
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

    public function updatedPrintFormat(mixed $value)
    {
        cache()->forever('excel_printFormat_' . Auth::id(), $value);
    }

    public function updatedPrintColumns(mixed $value)
    {
        cache()->forever('excel_printColumns_' . Auth::id(), $value);
    }

    public function updatedFontSize(mixed $value)
    {
        cache()->forever('excel_fontSize_' . Auth::id(), $value);
    }

    public function updatedRowsPerPage(mixed $value)
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

            $ordersToProcess = [];
            if ($orderCode) {
                // Tách đơn hàng
                $orderCodes = explode('+', $orderCode);
                $count = count($orderCodes);
                
                $baseQty = floor($quantity / $count);
                $remainder = $quantity % $count;

                $needsNewPo = false;
                $fetchedOrders = [];
                foreach ($orderCodes as $code) {
                    $code = trim($code);
                    if (empty($code)) continue;
                    
                    $order = Order::firstOrNew(
                        ['code' => $code],
                        ['status' => OrderStatus::RUNNING]
                    );
                    $fetchedOrders[$code] = $order;
                    
                    if (empty($order->production_order_id)) {
                        $needsNewPo = true;
                    }
                }

                $productionOrder = null;
                if ($needsNewPo) {
                    // Tự động cấp LSX
                    $year = date('y');
                    $prefix = "LSX{$year}-";
                    $lastPo = \App\Models\ProductionOrder::where('code', 'like', "{$prefix}%")
                                        ->orderBy('code', 'desc')
                                        ->first();
                    if ($lastPo && preg_match('/-(\d+)$/', $lastPo->code, $matches)) {
                        $nextNum = intval($matches[1]) + 1;
                    } else {
                        $nextNum = 1;
                    }
                    $poCode = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

                    // Tạo bảng ProductionOrder nếu chưa có
                    $productionOrder = \App\Models\ProductionOrder::firstOrCreate(
                        ['code' => $poCode],
                        [
                            'status' => \App\Enums\ProductionOrderStatus::RUNNING,
                            'start_date' => now(),
                        ]
                    );
                }

                foreach ($orderCodes as $index => $code) {
                    $code = trim($code);
                    if (empty($code)) continue;

                    $qtyForThis = $baseQty + ($index < $remainder ? 1 : 0);
                    
                    if ($qtyForThis > 0) {
                        $order = $fetchedOrders[$code];
                        // Chỉ ghi đè LSX nếu trước đó order chưa có LSX
                        if (empty($order->production_order_id) && $productionOrder) {
                            $order->production_order_id = $productionOrder->id;
                        }
                        $order->total = ($order->total ?? 0) + $qtyForThis;
                        $order->save();
                        
                        $ordersToProcess[] = [
                            'orderId' => $order->id,
                            'orderCode' => $code,
                            'quantity' => $qtyForThis
                        ];
                    }
                }
            } else {
                $ordersToProcess[] = [
                    'orderId' => null,
                    'orderCode' => '',
                    'quantity' => $quantity
                ];
            }

            $colorId   = $this->resolveAttributeId(Color::class, $colorCode);
            $specId    = $this->resolveAttributeId(Specification::class, $specCode);
            $plasticId = $this->resolveAttributeId(PlasticType::class, $plasticCode);
            //nếu col0Mode = sequence thì no = inputCol0 - 1, ngược lại thì no = 0
            $no = ($this->col0Mode === 'sequence') ? $inputCol0 - 1 : 0;

            foreach ($ordersToProcess as $orderProcess) {
                $oQty = $orderProcess['quantity'];
                $oId = $orderProcess['orderId'];
                $oCode = $orderProcess['orderCode'];

                for ($i = 0; $i < $oQty; $i++) {
                    $no++;
                    $propertiesToSave = [];
                    $realCode = '';
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

                        // 🌟 GỌI SERVICE ĐỂ SINH MÃ TẠI ĐÂY
                        $realCode = ItemCodeService::generateStandardCode(
                            $oCode,
                            $colorCode,
                            $specCode,
                            $widthCode,
                            $plasticCode,
                            $gsm,
                            $length,
                            $no
                        );

                        $numericLength = is_numeric($length) ? (float) $length : null;

                        /** @var \App\Models\User $currentUser */
                        $currentUser = Auth::user();

                        $itemRecord = Item::create([
                            'code'         => $realCode,
                            'type'         => (int) $this->type,
                            'properties'   => $propertiesToSave,
                            'created_by'   => Auth::id(),
                            'color_id'         => $colorId,
                            'specification_id' => $specId,
                            'plastic_type_id'  => $plasticId,
                            'width_original'   => is_numeric($widthCode) ? (float) $widthCode : null,
                            'width'            => is_numeric($widthCode) ? (float) $widthCode : null,
                            'order_id'         => $oId,
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
