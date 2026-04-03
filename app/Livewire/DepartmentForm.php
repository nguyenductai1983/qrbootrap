<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Product; // Import Product model
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;

#[Title('Quản lý Bộ phận')]
class DepartmentForm extends Component
{
    public $department; // Biến để lưu trữ đối tượng Department khi chỉnh sửa
    public $name;
    public $code;
    public $selectedProducts = []; // Biến lưu các sản phẩm được trọn
    public $allProducts; // Danh sách tất cả sản phẩm

    public function mount($departmentId = null)
    {
        $this->allProducts = Product::orderBy('name')->get();

        if ($departmentId) {
            $this->department = Department::findOrFail($departmentId);
            $this->name = $this->department->name;
            $this->code = $this->department->code;
            $this->selectedProducts = $this->department->products->pluck('id')->toArray();
        } else {
            $this->department = new Department();
        }
    }

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Quy tắc unique name, bỏ qua tên của Bộ phận hiện tại khi chỉnh sửa
                Rule::unique('departments')->ignore($this->department->id),
            ],
            'selectedProducts' => ['nullable', 'array'],
            'selectedProducts.*' => ['exists:products,id'],
        ];
    }

    public function saveDepartment()
    {
        $this->validate();

        $this->department->name = $this->name;
        $this->department->code = $this->code; // Lưu mã Bộ phận
        $this->department->save();

        // Đồng bộ danh sách sản phẩm
        $this->department->products()->sync($this->selectedProducts);

        session()->flash('success', 'Bộ phận đã được ' . ($this->department->wasRecentlyCreated ? 'tạo' : 'cập nhật') . ' thành công!');

        return redirect()->route('departments.index');
    }

    public function render()
    {
        $title = $this->department->exists ? 'Chỉnh sửa Bộ phận' : 'Tạo Bộ phận Mới';
        return view('livewire.admin.department-form', compact('title'));
    }
}
