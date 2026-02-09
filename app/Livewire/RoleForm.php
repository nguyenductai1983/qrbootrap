<?php

namespace App\Livewire;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Import Permission model
use Livewire\Component;
use Illuminate\Validation\Rule;

class RoleForm extends Component
{
    public $role;
    public $name;
    public $selectedPermissions = []; // Mảng để lưu các quyền được chọn

    public $allPermissions; // Biến để lưu tất cả các quyền

    public function mount($roleId = null)
    {
        $this->allPermissions = Permission::orderBy('name')->get(); // Lấy tất cả quyền

        if ($roleId) {
            $this->role = Role::findOrFail($roleId);
            $this->name = $this->role->name;
            $this->selectedPermissions = $this->role->permissions->pluck('name')->toArray(); // Lấy các quyền hiện có
        } else {
            $this->role = new Role();
        }
    }

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($this->role->id),
            ],
            'selectedPermissions' => ['nullable', 'array'],
            'selectedPermissions.*' => ['exists:permissions,name'], // Mỗi quyền phải tồn tại
        ];
    }

    public function saveRole()
    {
        $this->validate();

        $this->role->name = $this->name;
        $this->role->save();

        // Đồng bộ quyền hạn cho vai trò
        $this->role->syncPermissions($this->selectedPermissions);

        session()->flash('success', 'Vai trò đã được ' . ($this->role->wasRecentlyCreated ? 'tạo' : 'cập nhật') . ' thành công!');

        return redirect()->route('roles.index');
    }

    public function render()
    {
        $title = $this->role->exists ? 'Chỉnh sửa Vai trò' : 'Tạo Vai trò Mới';
        return view('livewire.admin.role-form', compact('title'));
    }
}
