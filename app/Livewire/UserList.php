<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UserList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap'; // Sử dụng Bootstrap cho phân trang
    public $search = '';
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteUser($userId)
    {
        // Kiểm tra quyền
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->can('delete users')) {
            session()->flash('error', 'Bạn không có quyền xóa người dùng!');
            return;
        }
        if ($user->id == $userId) {
            session()->flash('error', 'Bạn không thể xóa tài khoản của chính mình!');
            return;
        }

        $user = User::find($userId);
        if ($user) {
            $user->delete();
            session()->flash('success', 'Người dùng đã được xóa thành công!');
        } else {
            session()->flash('error', 'Không tìm thấy người dùng để xóa.');
        }
    }

    public function render()
    {
        $users = User::query()
            ->with('department', 'roles') // <-- Eager load mối quan hệ department và roles
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('roles', function ($q) { // <-- Tìm kiếm theo vai trò
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.user-list', [
            'users' => $users,
        ]);
    }
}
