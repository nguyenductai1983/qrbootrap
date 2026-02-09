<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\ProductModel;
use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelManager extends Component
{
    use WithFileUploads;

    public $orders, $models;
    public $selectedOrderId = '';
    public $selectedModelId = '';

    public $fileUpload; // Biến chứa file Excel import

    public function mount()
    {
        $this->orders = Order::all();
        $this->models = ProductModel::all();
    }

    // 1. XUẤT FILE EXCEL
    public function export()
    {
        $this->validate([
            'selectedOrderId' => 'required',
            'selectedModelId' => 'required'
        ]);

        $fileName = 'SanXuat_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new ItemsExport($this->selectedOrderId, $this->selectedModelId),
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
        return view('livewire.production.excel-manager');
    }
}
