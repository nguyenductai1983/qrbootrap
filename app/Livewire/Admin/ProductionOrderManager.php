<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductionOrder;
use App\Models\Order;
use Livewire\Attributes\Title;

#[Title('Quản lý Lệnh Sản Xuất')]
class ProductionOrderManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public mixed $selectedOrder = null;
    public $isEditModalOpen = false;

    // Các trường form
    public mixed $productionOrderId;
    public mixed $status;
    public mixed $start_date;
    public mixed $end_date;
    public mixed $notes;
    
    // Xem chi tiết các orders bên trong
    public $viewingOrders = [];
    public $isViewModalOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function editMode(mixed $id)
    {
        $po = ProductionOrder::findOrFail($id);
        $this->productionOrderId = $po->id;
        $this->status = $po->status->value;
        $this->start_date = $po->start_date ? $po->start_date->format('Y-m-d') : null;
        $this->end_date = $po->end_date ? $po->end_date->format('Y-m-d') : null;
        $this->notes = $po->notes;
        $this->isEditModalOpen = true;
    }

    public function viewOrders(mixed $id)
    {
        $po = ProductionOrder::findOrFail($id);
        $this->selectedOrder = $po;
        $this->viewingOrders = Order::where('production_order_id', $po->id)->get();
        $this->isViewModalOpen = true;
    }

    public function closeModals()
    {
        $this->isEditModalOpen = false;
        $this->isViewModalOpen = false;
        $this->reset(['productionOrderId', 'status', 'start_date', 'end_date', 'notes', 'selectedOrder', 'viewingOrders']);
    }

    public function save()
    {
        $this->validate([
            'status' => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $po = ProductionOrder::findOrFail($this->productionOrderId);
        $po->update([
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'notes' => $this->notes,
        ]);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Đã cập nhật Lệnh Sản Xuất']);
        $this->closeModals();
    }

    public function render()
    {
        $query = ProductionOrder::query()->withCount('orders');

        if ($this->search) {
            $query->where('code', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $productionOrders = $query->orderBy('id', 'desc')->paginate(15);

        return view('livewire.admin.production-order-manager', [
            'productionOrders' => $productionOrders,
            'statuses' => \App\Enums\ProductionOrderStatus::cases(),
        ]);
    }
}
