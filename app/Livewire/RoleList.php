<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class RoleList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteRole($roleId)
    {
        // Kiểm tra quyền
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('roles delete')) {
            session()->flash('error', 'Bạn không có quyền xóa vai trò!');
            return;
        }

        $role = Role::find($roleId);
        if ($role) {
            // Không cho phép xóa vai trò 'admin' mặc định
            if ($role->name === 'admin') {
                session()->flash('error', 'Không thể xóa vai trò Admin mặc định.');
                return;
            }
            // Kiểm tra xem có người dùng nào đang giữ vai trò này không
            if ($role->users()->count() > 0) {
                session()->flash('error', 'Không thể xóa vai trò này vì có người dùng đang giữ nó.');
                return;
            }
            $role->delete();
            session()->flash('success', 'Vai trò đã được xóa thành công!');
        } else {
            session()->flash('error', 'Không tìm thấy vai trò để xóa.');
        }
    }

    public function render()
    {
        $roles = Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->withCount('users') // Đếm số lượng người dùng có vai trò này
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.role-list', [
            'roles' => $roles,
        ]);
    }
}
