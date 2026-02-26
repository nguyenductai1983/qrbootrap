<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;

class ItemManager extends Component
{
    use WithPagination;

    // --- CÁC BIẾN BỘ LỌC ---
    public $searchCode = '';
    public $filterOrderId = '';
    public $filterProductId = '';

    // --- CÁC BIẾN CHỈNH SỬA ---
    public $editItemId = null;
    public $editCode = '';
    public $editProperties = []; // Mảng chứa dữ liệu JSON để edit

    // Khai báo sẵn cho tương lai: public $current_location_id;

    // Reset trang khi thay đổi điều kiện lọc
    public function updatingSearchCode() { $this->resetPage(); }
    public function updatingFilterOrderId() { $this->resetPage(); }
    public function updatingFilterProductId() { $this->resetPage(); }

    public function edit($id)
    {
        $item = Item::find($id);
        if ($item) {
            $this->editItemId = $item->id;
            $this->editCode = $item->code;

            // Ép kiểu về mảng để xử lý an toàn
            $this->editProperties = is_array($item->properties) ? $item->properties : json_decode($item->properties, true) ?? [];

            // Chuẩn bị cho chức năng định vị sắp tới:
            // $this->current_location_id = $item->current_location_id;

            $this->dispatch('open-modal');
        }
    }

    public function update()
    {
        if ($this->editItemId) {
            $item = Item::find($this->editItemId);

            // Có thể thêm validation nếu cần thiết
            $item->update([
                'properties' => $this->editProperties,
                // 'current_location_id' => $this->current_location_id, // Mở ra khi bạn làm xong table Locations
            ]);

            session()->flash('message', 'Cập nhật chi tiết tem thành công!');
            $this->dispatch('close-modal');
        }
    }

    public function render()
    {
        // Query cơ bản kèm theo Relationship để tránh N+1 Query
        $query = Item::with(['order', 'product'])
            ->when($this->searchCode, function ($q) {
                $q->where('code', 'like', '%' . $this->searchCode . '%');
            })
            ->when($this->filterOrderId, function ($q) {
                $q->where('order_id', $this->filterOrderId);
            })
            ->when($this->filterProductId, function ($q) {
                $q->where('product_id', $this->filterProductId);
            });

        return view('livewire.production.item-manager', [
            'items' => $query->orderBy('id', 'desc')->paginate(15),
            'orders' => Order::orderBy('id', 'desc')->get(),
            'products' => Product::orderBy('name', 'asc')->get(),
        ]);
    }
}
