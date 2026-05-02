<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\PrintStation;

class UserPrintStationAssignment extends Component
{
    public mixed $users;
    public mixed $printStations;

    public mixed $selectedUser = null;
    public $selectedStations = [];

    public function mount()
    {
        $this->users = User::all();
        $this->printStations = PrintStation::where('status', true)->get();
    }

    public function selectUser(mixed $userId)
    {
        $this->selectedUser = User::find($userId);
        if ($this->selectedUser) {
            $this->selectedStations = $this->selectedUser->printStations->pluck('id')->toArray();
        } else {
            $this->selectedStations = [];
        }
    }

    public function assignStations()
    {
        if ($this->selectedUser) {
            $this->selectedUser->printStations()->sync($this->selectedStations);
            session()->flash('success', 'Đã lưu cấu hình trạm in thành công!');
        } else {
            session()->flash('error', 'Vui lòng chọn nhân viên trước!');
        }
    }

    public function render()
    {
        return view('livewire.admin.user-print-station-assignment');
    }
}
