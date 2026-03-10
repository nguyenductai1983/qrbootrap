<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class DepartmentList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteDepartment($departmentId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->can('departments delete')) {
            session()->flash('error', 'Bạn không có quyền xóa phòng ban!');
            return;
        }
        $department = Department::find($departmentId);

        if ($department) {
            // Kiểm tra xem có người dùng nào thuộc phòng ban này không
            if ($department->users()->count() > 0) {
                session()->flash('error', 'Không thể xóa phòng ban này vì có người dùng thuộc về nó.');
                return;
            }

            $department->delete();
            session()->flash('success', 'Phòng ban đã được xóa thành công!');
        } else {
            session()->flash('error', 'Không tìm thấy phòng ban để xóa.');
        }
    }

    public function render()
    {
        $departments = Department::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.department-list', [
            'departments' => $departments,
        ]);
    }
}
