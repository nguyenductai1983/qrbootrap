<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductModel;
use App\Models\Department;

class ProductModelManager extends Component
{
    use WithPagination;

    public $code, $name, $specs, $modelId;
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
        $this->modelId = null;
        $this->isEditMode = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:product_models,code',
            'name' => 'required',
            'selectedDepartments' => 'required|array|min:1', // Bắt buộc phải gán ít nhất 1 xưởng
        ], [
            'selectedDepartments.required' => 'Vui lòng chọn ít nhất 1 phân xưởng áp dụng.'
        ]);

        $model = ProductModel::create([
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'specs' => $this->specs
        ]);

        // Lưu quan hệ Many-to-Many
        $model->departments()->sync($this->selectedDepartments);

        session()->flash('message', 'Đã tạo Model mới thành công!');
        $this->resetInput();
        $this->dispatch('close-modal');
    }

    public function edit($id)
    {
        $model = ProductModel::with('departments')->find($id);
        if ($model) {
            $this->modelId = $model->id;
            $this->code = $model->code;
            $this->name = $model->name;
            $this->specs = $model->specs;
            // Lấy danh sách ID phân xưởng đã gán để check vào checkbox
            $this->selectedDepartments = $model->departments->pluck('id')->toArray();

            $this->isEditMode = true;
            $this->dispatch('open-modal');
        }
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:product_models,code,' . $this->modelId,
            'name' => 'required',
            'selectedDepartments' => 'required|array|min:1',
        ]);

        if ($this->modelId) {
            $model = ProductModel::find($this->modelId);
            $model->update([
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'specs' => $this->specs
            ]);

            // Cập nhật lại quan hệ
            $model->departments()->sync($this->selectedDepartments);

            session()->flash('message', 'Cập nhật Model thành công!');
            $this->resetInput();
            $this->dispatch('close-modal');
        }
    }

    public function delete($id)
    {
        ProductModel::find($id)->delete();
        session()->flash('message', 'Đã xóa Model!');
    }

    public function render()
    {
        $models = ProductModel::with('departments')
            ->where('code', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.product-model-manager', ['models' => $models]);
    }
}
