<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\Item;
use App\Models\Product;
use App\Models\User;

#[Title('Danh Sách Nhập Kho')]
class WarehouseInboundList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $fromDate = '';
    public string $toDate = '';
    public string $selectedProductId = '';
    public string $selectedWarehouserId = '';

    public $products = [];
    public $warehousers = [];

    protected $queryString = [
        'search'              => ['except' => ''],
        'fromDate'            => ['except' => ''],
        'toDate'              => ['except' => ''],
        'selectedProductId'   => ['except' => ''],
        'selectedWarehouserId'=> ['except' => ''],
    ];

    public function mount(): void
    {
        $this->fromDate = date('Y-m-01');
        $this->toDate   = date('Y-m-d');

        $this->products = Product::orderBy('code')->get();

        // Lấy danh sách những người đã từng nhập kho
        $this->warehousers = User::whereIn(
            'id',
            Item::whereNotNull('warehoused_by')->distinct()->pluck('warehoused_by')
        )->orderBy('name')->get();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedProductId', 'selectedWarehouserId']);
        $this->fromDate = date('Y-m-01');
        $this->toDate   = date('Y-m-d');
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedProductId(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedWarehouserId(): void
    {
        $this->resetPage();
    }

    public function updatingFromDate(): void
    {
        $this->resetPage();
    }

    public function updatingToDate(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Item::with([
            'product',
            'color',
            'specification',
            'plasticType',
            'machine',
            'order',
            'location',
            'warehouser',
            'department',
        ])
        ->whereNotNull('warehoused_by');

        // Tìm kiếm theo mã barcode hoặc mã kho
        if ($this->search !== '') {
            $query->where(function($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('warehouse_code', 'like', '%' . $this->search . '%');
            });
        }

        // Lọc theo dòng sản phẩm
        if ($this->selectedProductId !== '') {
            $query->where('product_id', $this->selectedProductId);
        }

        // Lọc theo người nhập kho
        if ($this->selectedWarehouserId !== '') {
            $query->where('warehoused_by', $this->selectedWarehouserId);
        }

        // Lọc theo ngày nhập kho
        if ($this->fromDate !== '' && $this->toDate !== '') {
            $query->whereBetween('warehoused_at', [
                $this->fromDate . ' 00:00:00',
                $this->toDate   . ' 23:59:59',
            ]);
        }

        $items = $query->orderBy('warehoused_at', 'desc')->paginate(20);

        return view('livewire.warehouse.warehouse-inbound-list', compact('items'));
    }
}
