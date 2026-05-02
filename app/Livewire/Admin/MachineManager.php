<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Machine;
use App\Models\Department;

class MachineManager extends Component
{
    use WithPagination;

    public mixed $name, $code, $department_id, $status = true, $machineId;
    public $departments = [];
    public $isEditMode = false;
    public $searchTerm = '';

    public function mount()
    {
        $this->departments = Department::all();
    }

    public function resetInput()
    {
        $this->name = '';
        $this->code = '';
        $this->department_id = '';
        $this->status = true;
        $this->machineId = null;
        $this->isEditMode = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:50|unique:machines,code',
            'department_id' => 'required|exists:departments,id',
        ], [
            'name.required'          => 'Tên máy là bắt buộc.',
            'code.required'          => 'Mã máy là bắt buộc.',
            'code.unique'            => 'Mã máy này đã tồn tại.',
            'department_id.required' => 'Vui lòng chọn phân xưởng.',
        ]);

        Machine::create([
            'name'          => $this->name,
            'code'          => strtoupper($this->code),
            'department_id' => $this->department_id,
            'status'        => $this->status,
        ]);

        session()->flash('message', 'Đã thêm máy mới thành công!');
        $this->resetInput();
        $this->dispatch('close-modal');
    }

    public function edit(mixed $id)
    {
        $machine = Machine::find($id);
        if ($machine) {
            $this->machineId     = $machine->id;
            $this->name          = $machine->name;
            $this->code          = $machine->code;
            $this->department_id = $machine->department_id;
            $this->status        = $machine->status;
            $this->isEditMode    = true;
            $this->dispatch('open-modal');
        }
    }

    public function update()
    {
        $this->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:50|unique:machines,code,' . $this->machineId,
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($this->machineId) {
            $machine = Machine::find($this->machineId);
            $machine->update([
                'name'          => $this->name,
                'code'          => strtoupper($this->code),
                'department_id' => $this->department_id,
                'status'        => $this->status,
            ]);

            session()->flash('message', 'Cập nhật máy thành công!');
            $this->resetInput();
            $this->dispatch('close-modal');
        }
    }

    public function delete(mixed $id)
    {
        Machine::find($id)->delete();
        session()->flash('message', 'Đã xóa máy!');
    }

    public function render()
    {
        $machines = Machine::with('department')
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->orderBy('department_id')
            ->orderBy('code')
            ->paginate(15);

        return view('livewire.admin.machine-manager', ['machines' => $machines]);
    }
}
