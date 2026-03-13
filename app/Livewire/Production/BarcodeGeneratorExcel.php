<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
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

class BarcodeGeneratorExcel extends Component
{
    use WithPagination;

    // Cấu hình
    public $type = '';
    public $itemTypes = [];
    public $departments = [];
    public $selectedDeptCode = '';
    public $availableProducts = [];
    // Dữ liệu nhập liệu
    public $itemData = [];
    public $excelData; // Chứa dữ liệu paste từ Excel

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

        // LOGIC LẤY BỘ PHẬN:
        if ($user->hasRole('admin')) {
            $this->departments = Department::whereNotNull('code')->get();
        } else {
            $this->departments = $user->departments;
        }

        // Lấy danh sách Loại tem đang Active
        $this->itemTypes = ItemType::where('is_active', true)->get();

        if (count($this->itemTypes) > 0) {
            $this->type = $this->itemTypes[0]->code;
        }

        $this->itemData['PRODUCT_ID'] = '';
        $this->itemData['PRODUCT_NAME'] = '';

        if (count($this->departments) > 0) {
            $this->selectedDeptCode = $this->departments[0]->code;
            $this->updatedSelectedDeptCode();
        }
    }

    public function updatedItemDataProductId($value)
    {
        $product = Product::find($value);
        if ($product) {
            $this->itemData['PRODUCT'] = $product->code;
            $this->itemData['PRODUCT_NAME'] = $product->name;
        }
    }

    public function updatedSelectedDeptCode()
    {
        //Tìm Department theo Code đang chọn
        $dept = Department::where('code', $this->selectedDeptCode)->first();

        if ($dept) {
            $this->availableProducts = $dept->products;
        } else {
            $this->availableProducts = [];
        }

        if (count($this->availableProducts) > 0) {
            $firstProduct = collect($this->availableProducts)->first();

            $productId = is_array($firstProduct) ? $firstProduct['id'] : $firstProduct->id;
            $productCode = is_array($firstProduct) ? $firstProduct['code'] : $firstProduct->code;
            $productName = is_array($firstProduct) ? $firstProduct['name'] : $firstProduct->name;

            $this->itemData['PRODUCT_ID'] = $productId;
            $this->itemData['PRODUCT'] = $productCode;
            $this->itemData['PRODUCT_NAME'] = $productName;
        } else {
            $this->itemData['PRODUCT_ID'] = '';
            $this->itemData['PRODUCT'] = '';
            $this->itemData['PRODUCT_NAME'] = '';
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

    public function generate()
    {
        $this->validate([
            'selectedDeptCode' => 'required',
            'itemData.PRODUCT_ID' => 'required',
            'excelData' => 'required',
        ], [
            'selectedDeptCode.required' => 'Vui lòng chọn Phân xưởng.',
            'itemData.PRODUCT_ID.required' => 'Vui lòng chọn Mã Hàng.',
            'excelData.required' => 'Vui lòng dán dữ liệu từ Excel.',
        ]);

        $this->generatedItems = [];
        $this->selectedHistoryIds = [];

        $lines = explode("\n", trim($this->excelData));

        foreach ($lines as $line) {
            if (trim($line) === '') continue;

            // $cols = explode("\t", trim($line));
            $cols = preg_split('/\s+/', trim($line));
            // 15	H212NDS	we	D8	1780	PP	150	1000	02-17
            $cols = array_pad($cols, 9, '');

            $quantity     = (int) trim($cols[0]);
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

            $prefix = strtoupper(trim("$orderCode $colorCode $specCode $widthCode $plasticCode"));

            $propParts = array_filter([$gsm, $length]);
            $code_properties = '';
            if (count($propParts) > 0) {
                $code_properties = ' ' . implode(' ', $propParts) . ' ';
            } else {
                $code_properties = ' ';
            }
            $no = 0;
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
                try {
                    $realCode = strtoupper($prefix . $code_properties . str_pad($no, 3, '0', STR_PAD_LEFT));
                    Item::create([
                        'code' => $realCode,
                        'type' => $this->type,
                        'status' => 1,
                        'properties' => $propertiesToSave,
                        'created_by' => Auth::id(),
                        'color_id'         => $colorId,
                        'specification_id' => $specId,
                        'plastic_type_id'  => $plasticId,
                        'width_id'         => $widthId,
                        'order_id' => $orderId,
                        'product_id' => $this->itemData['PRODUCT_ID'] ?? null,
                    ]);
                } catch (QueryException $e) {
                    // Kiểm tra xem có phải mã lỗi 1062 (Duplicate) của MySQL không
                    if ($e->errorInfo[1] == 1062) {
                        // Dịch lỗi SQL thành câu báo lỗi thân thiện cho người dùng
                        session()->flash('error', "Lỗi: Mã tem '{$realCode}' đã tồn tại trong hệ thống! Vui lòng kiểm tra lại số thứ tự hoặc dữ liệu Excel.");
                        return; // 🌟 Dừng ngay quá trình sinh tem lại
                    }
                    // Nếu là lỗi Database khác thì cứ báo lỗi chung chung
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
