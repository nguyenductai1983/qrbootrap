<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Product;
use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\Title;

#[Title('Nhập xuất Excel mã code')]
class ExcelManager extends Component
{
    use WithFileUploads;

    public $orders, $products;
    public $selectedOrderId = '';
    public $selectedProductId = '';
    public $fromDate = '';
    public $toDate = '';
    public $fileUpload; // Biến chứa file Excel import

    public function mount()
    {
        $this->orders = Order::all();
        $this->products = Product::all();
        $this->fromDate = date('Y-m-01');
        $this->toDate = date('Y-m-d');

        // Phục hồi lựa chọn từ Cache (Nhớ riêng cho từng User)
        /** @var \App\Models\User $user */
        $userId = Auth::id();
        $cachedProductId = Cache::get('user_' . $userId . '_excel_product');

        if ($cachedProductId && $this->products->contains('id', $cachedProductId)) {
            $this->selectedProductId = $cachedProductId;
        } else {
            // Mặc định chọn Sản Phẩm đầu tiên nếu Cache rỗng
            $this->selectedProductId = $this->products->first()->id ?? '';
        }
    }

    public function updatedSelectedProductId($value)
    {
        // Lưu lựa chọn mới vào Cache trong 30 ngày
        Cache::put('user_' . Auth::id() . '_excel_product', $value, now()->addDays(30));
    }

    // 1. XUẤT FILE EXCEL
    public function export()
    {
        $this->validate([
            'selectedProductId' => 'required'
        ], [
            'selectedProductId.required' => 'Vui lòng chọn Sản Phẩm để lấy mẫu dữ liệu.'
        ]);

        $fileName = 'SanXuat_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\ItemsTemplateExport($this->selectedOrderId, $this->selectedProductId, $this->fromDate, $this->toDate),
            $fileName
        );
    }

    // 2. NHẬP FILE EXCEL
    public function import()
    {
        $this->validate([
            'fileUpload' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new ItemsImport, $this->fileUpload);
            session()->flash('message', '✅ Đã cập nhật thông tin sản xuất thành công!');
            $this->fileUpload = null; // Reset file
        } catch (\Exception $e) {
            session()->flash('error', '❌ Lỗi Import: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->js("console.log('Nhập xuất Excel mã code')");
        return view('livewire.production.excel-manager');
    }
}
