<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class OrderManager extends Component
{
    use WithPagination;

    public $code, $customer_name, $status = 'RUNNING', $orderId;
    public $isEditMode = false;
    public $searchTerm = '';

    // Reset form khi đóng modal hoặc hủy
    public function resetInput()
    {
        $this->code = '';
        $this->customer_name = '';
        $this->status = 'RUNNING';
        $this->orderId = null;
        $this->isEditMode = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:orders,code',
            'customer_name' => 'required',
        ]);

        Order::create([
            'code' => strtoupper($this->code),
            'customer_name' => $this->customer_name,
            'status' => $this->status
        ]);

        session()->flash('message', 'Đã thêm đơn hàng mới thành công!');
        $this->resetInput();
        $this->dispatch('close-modal'); // Đóng modal bằng JS
    }

    public function edit($id)
    {
        $order = Order::find($id);
        if ($order) {
            $this->orderId = $order->id;
            $this->code = $order->code;
            $this->customer_name = $order->customer_name;
            $this->status = $order->status;
            $this->isEditMode = true;
            $this->dispatch('open-modal'); // Mở modal bằng JS
        }
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:orders,code,' . $this->orderId,
            'customer_name' => 'required',
        ]);

        if ($this->orderId) {
            $order = Order::find($this->orderId);
            $order->update([
                'code' => strtoupper($this->code),
                'customer_name' => $this->customer_name,
                'status' => $this->status
            ]);
            session()->flash('message', 'Cập nhật thành công!');
            $this->resetInput();
            $this->dispatch('close-modal');
        }
    }

    public function delete($id)
    {
        Order::find($id)->delete();
        session()->flash('message', 'Đã xóa đơn hàng!');
    }

    public function render()
    {
        $orders = Order::where('code', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('customer_name', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.order-manager', ['orders' => $orders]);
    }
}
