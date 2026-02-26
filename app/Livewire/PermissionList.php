<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;

class PermissionList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deletePermission($permissionId)
    {
        // Kiểm tra quyền
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->can('permissions delete')) {
            session()->flash('error', 'Bạn không có quyền xóa quyền hạn!');
            return;
        }

        $permission = Permission::find($permissionId);
        if ($permission) {
            // Kiểm tra xem có vai trò nào đang giữ quyền này không
            if ($permission->roles()->count() > 0) {
                session()->flash('error', 'Không thể xóa quyền hạn này vì có vai trò đang giữ nó.');
                return;
            }
            $permission->delete();
            session()->flash('success', 'Quyền hạn đã được xóa thành công!');
        } else {
            session()->flash('error', 'Không tìm thấy quyền hạn để xóa.');
        }
    }

    public function render()
    {
        $permissions = Permission::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->withCount('roles') // Đếm số lượng vai trò có quyền này
            ->orderBy('id')
            ->paginate(20);

        return view('livewire.admin.permission-list', [
            'permissions' => $permissions,
        ]);
    }
}
