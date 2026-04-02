<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Product;
use App\Exports\WarehouseItemsExport;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\Title;

#[Title('Báo Cáo Tồn Kho')]
class ReportManager extends Component
{
    public $models;
    public $selectedModelId = '';
    public $fromDate = '';
    public $toDate = '';

    public function mount()
    {
        $this->models = Product::all();
        $this->fromDate = date('Y-m-01');
        $this->toDate = date('Y-m-d');
    }

    public function export()
    {
        $fileName = 'TonKho_' . date('Ymd_His') . '.xlsx';

        // Tương lai bạn có thể sử dụng các biến $this->selectedModelId, $this->fromDate, $this->toDate 
        // để truyền vào trong hàm dựng (constructor) của WarehouseItemsExport nếu bạn muốn nâng cấp bộ lọc.
        // Hiện tại: Tải xuống tất cả như bạn đã yêu cầu lúc trước.
        return Excel::download(
            new WarehouseItemsExport($this->selectedModelId, $this->fromDate, $this->toDate),
            $fileName
        );
    }

    public function render()
    {
        return view('livewire.warehouse.report-manager');
    }
}
