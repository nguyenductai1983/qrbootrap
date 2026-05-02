<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\ScaleStation;

class UserScaleStationAssignment extends Component
{
    public mixed $users;
    public mixed $scaleStations;

    public mixed $selectedUser    = null;
    public $selectedStations = [];

    public function mount()
    {
        $this->users         = User::orderBy('name')->get();
        $this->scaleStations = ScaleStation::where('status', true)->orderBy('name')->get();
    }

    public function selectUser(mixed $userId)
    {
        $this->selectedUser = User::find($userId);
        if ($this->selectedUser) {
            $this->selectedStations = $this->selectedUser->scaleStations->pluck('id')->toArray();
        } else {
            $this->selectedStations = [];
        }
    }

    public function assignStations()
    {
        if ($this->selectedUser) {
            $this->selectedUser->scaleStations()->sync($this->selectedStations);
            session()->flash('success', 'Đã lưu phân công trạm cân thành công!');
        } else {
            session()->flash('error', 'Vui lòng chọn nhân viên trước!');
        }
    }

    public function render()
    {
        return view('livewire.admin.user-scale-station-assignment');
    }
}
