<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Department;

class ProductManager extends Component
{
    use WithPagination;

    public $code, $name, $specs, $productId;
    public $selectedDepartments = []; // Mảng chứa ID các phân xưởng được chọn
    public $departments = []; // Danh sách tất cả phân xưởng để hiển thị checkbox

    public $isEditMode = false;
    public $searchTerm = '';

    public function mount()
    {
        // Lấy danh sách phân xưởng để hiển thị trong Form
        $this->departments = Department::all();
    }

    public function resetInput()
    {
        $this->code = '';
        $this->name = '';
        $this->specs = '';
        $this->selectedDepartments = [];
        $this->productId = null;
        $this->isEditMode = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:products,code',
            'name' => 'required',
            'selectedDepartments' => 'required|array|min:1', // Bắt buộc phải gán ít nhất 1 xưởng
        ], [
            'selectedDepartments.required' => 'Vui lòng chọn ít nhất 1 phân xưởng áp dụng.'
        ]);

        $product = Product::create([
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'specs' => $this->specs
        ]);

        // Lưu quan hệ Many-to-Many
        $product->departments()->sync($this->selectedDepartments);

        session()->flash('message', 'Đã tạo sản phẩm mới thành công!');
        $this->resetInput();
        $this->dispatch('product-list-changed'); // Thêm dòng này để thông báo cho component khác nếu cần
        $this->dispatch('close-modal');
    }

    public function edit($id)
    {
        $product = Product::with('departments')->find($id);
        if ($product) {
            $this->productId = $product->id;
            $this->code = $product->code;
            $this->name = $product->name;
            $this->specs = $product->specs;
            // Lấy danh sách ID phân xưởng đã gán để check vào checkbox
            $this->selectedDepartments = $product->departments->pluck('id')->toArray();

            $this->isEditMode = true;
            $this->dispatch('open-modal');
        }
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:products,code,' . $this->productId,
            'name' => 'required',
            'selectedDepartments' => 'required|array|min:1',
        ]);

        if ($this->productId) {
            $product = Product::find($this->productId);
            $product->update([
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'specs' => $this->specs
            ]);

            // Cập nhật lại quan hệ
            $product->departments()->sync($this->selectedDepartments);

            session()->flash('message', 'Cập nhật sản phẩm thành công!');
            $this->resetInput();
            $this->dispatch('product-list-changed');
            $this->dispatch('close-modal');
        }
    }

    public function delete($id)
    {
        Product::find($id)->delete();
        session()->flash('message', 'Đã xóa sản phẩm!');
    }

    public function render()
    {
        $products = Product::with('departments')
            ->where('code', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.product-manager', ['products' => $products]);
    }
}
