<?php

namespace App\Livewire;

use Spatie\Permission\Models\Permission;
use Livewire\Component;
use Illuminate\Validation\Rule;

class PermissionForm extends Component
{
    public $permission;
    public $name;

    public function mount($permissionId = null)
    {
        if ($permissionId) {
            $this->permission = Permission::findOrFail($permissionId);
            $this->name = $this->permission->name;
        } else {
            $this->permission = new Permission();
        }
    }

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($this->permission->id),
            ],
        ];
    }

    public function savePermission()
    {
        $this->validate();

        $this->permission->name = $this->name;
        $this->permission->save();

        session()->flash('success', 'Quyền hạn đã được ' . ($this->permission->wasRecentlyCreated ? 'tạo' : 'cập nhật') . ' thành công!');

        return redirect()->route('permissions.index');
    }

    public function render()
    {
        $title = $this->permission->exists ? 'Chỉnh sửa Quyền hạn' : 'Tạo Quyền hạn Mới';
        return view('livewire.admin.permission-form', compact('title'));
    }
}
