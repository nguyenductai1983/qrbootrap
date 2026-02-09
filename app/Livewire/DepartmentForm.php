<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Component;
use Illuminate\Validation\Rule;

class DepartmentForm extends Component
{
    public $department; // Biến để lưu trữ đối tượng Department khi chỉnh sửa
    public $name;

    public function mount($departmentId = null)
    {
        if ($departmentId) {
            $this->department = Department::findOrFail($departmentId);
            $this->name = $this->department->name;
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
                // Quy tắc unique name, bỏ qua tên của phòng ban hiện tại khi chỉnh sửa
                Rule::unique('departments')->ignore($this->department->id),
            ],
        ];
    }

    public function saveDepartment()
    {
        $this->validate();

        $this->department->name = $this->name;
        $this->department->save();

        session()->flash('success', 'Phòng ban đã được ' . ($this->department->wasRecentlyCreated ? 'tạo' : 'cập nhật') . ' thành công!');

        return redirect()->route('departments.index');
    }

    public function render()
    {
        $title = $this->department->exists ? 'Chỉnh sửa Phòng ban' : 'Tạo Phòng ban Mới';
        return view('livewire.admin.department-form', compact('title'));
    }
}
