<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ItemType;

class ItemTypeManager extends Component
{
    use WithPagination;

    public $code, $name, $description, $is_active = true, $typeId;
    public $isEditMode = false;
    public $searchTerm = '';

    public function resetInput()
    {
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->is_active = true;
        $this->typeId = null;
        $this->isEditMode = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:item_types,code',
            'name' => 'required',
        ]);

        ItemType::create([
            'code' => strtoupper(trim($this->code)),
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Đã thêm Loại Tem mới!');
        $this->resetInput();
        $this->dispatch('close-modal');
    }

    public function edit($id)
    {
        $type = ItemType::find($id);
        if ($type) {
            $this->typeId = $type->id;
            $this->code = $type->code;
            $this->name = $type->name;
            $this->description = $type->description;
            $this->is_active = $type->is_active;

            $this->isEditMode = true;
            $this->dispatch('open-modal');
        }
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:item_types,code,' . $this->typeId,
            'name' => 'required',
        ]);

        if ($this->typeId) {
            $type = ItemType::find($this->typeId);
            $type->update([
                'code' => strtoupper(trim($this->code)),
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);

            session()->flash('message', 'Cập nhật thành công!');
            $this->resetInput();
            $this->dispatch('close-modal');
        }
    }

    public function delete($id)
    {
        ItemType::find($id)->delete();
        session()->flash('message', 'Đã xóa Loại Tem!');
    }

    public function render()
    {
        $types = ItemType::where('code', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.item-type-manager', ['types' => $types]);
    }
}
