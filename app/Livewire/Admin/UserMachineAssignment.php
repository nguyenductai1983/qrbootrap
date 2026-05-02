<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Machine;

class UserMachineAssignment extends Component
{
    use WithPagination;

    public $selectedUserId = '';
    public $selectedMachineIds = []; // Máy đang được chọn để assign
    public $searchTerm = '';

    public $users = [];
    public $allMachines = [];

    public function mount()
    {
        $this->users = User::with('department')->orderBy('name')->get();
        $this->allMachines = Machine::with('department')->where('status', true)->orderBy('department_id')->orderBy('code')->get();
    }

    public function updatedSelectedUserId(mixed $userId)
    {
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                // Load các máy hiện tại của user
                $this->selectedMachineIds = $user->machines()->pluck('machines.id')->map(fn($id) => (string) $id)->toArray();
            }
        } else {
            $this->selectedMachineIds = [];
        }
    }

    public function saveAssignment()
    {
        $this->validate([
            'selectedUserId' => 'required|exists:users,id',
        ], [
            'selectedUserId.required' => 'Vui lòng chọn nhân viên.',
        ]);

        $user = User::find($this->selectedUserId);
        // sync() sẽ tự xử lý thêm/xóa trong pivot table
        $user->machines()->sync($this->selectedMachineIds);

        session()->flash('message', 'Đã cập nhật phân công máy cho ' . $user->name . '!');
    }

    public function render()
    {
        $userList = User::with(['department', 'machines'])
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.user-machine-assignment', [
            'userList' => $userList,
        ]);
    }
}
