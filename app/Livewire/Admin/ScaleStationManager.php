<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ScaleStation;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ScaleStationManager extends Component
{
    use WithPagination;

    public mixed $name, $code, $status = true, $stationId;
    public mixed $station_token, $notes;
    public $showModal = false;

    protected function rules()
    {
        return [
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:255|unique:scale_stations,code' . ($this->stationId ? ',' . $this->stationId : ''),
            'station_token' => 'required|string|max:64|unique:scale_stations,station_token' . ($this->stationId ? ',' . $this->stationId : ''),
            'status'        => 'boolean',
            'notes'         => 'nullable|string',
        ];
    }

    protected $messages = [
        'name.required'          => 'Tên trạm cân không được để trống.',
        'code.required'          => 'Mã trạm cân không được để trống.',
        'code.unique'            => 'Mã trạm cân này đã tồn tại.',
        'station_token.required' => 'Token xác thực không được để trống.',
        'station_token.unique'   => 'Token này đã tồn tại, vui lòng tạo mới.',
    ];

    public function render()
    {
        return view('livewire.admin.scale-station-manager', [
            'stations' => ScaleStation::latest()->paginate(10),
        ]);
    }

    private function resetInputFields()
    {
        $this->name          = '';
        $this->code          = '';
        $this->station_token = '';
        $this->notes         = '';
        $this->status        = true;
        $this->stationId     = null;
        $this->resetErrorBag();
    }

    public function openCreate()
    {
        $this->resetInputFields();
        $this->station_token = strtoupper(Str::random(32));
        $this->showModal = true;
    }

    public function regenerateToken()
    {
        $this->station_token = strtoupper(Str::random(32));
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function store()
    {
        $this->validate();

        ScaleStation::updateOrCreate(['id' => $this->stationId], [
            'name'          => $this->name,
            'code'          => $this->code,
            'station_token' => $this->station_token,
            'status'        => $this->status,
            'notes'         => $this->notes,
        ]);

        session()->flash('message', $this->stationId ? 'Cập nhật trạm cân thành công!' : 'Thêm trạm cân mới thành công!');
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function edit(mixed $id)
    {
        $station = ScaleStation::findOrFail($id);
        $this->stationId     = $id;
        $this->name          = $station->name;
        $this->code          = $station->code;
        $this->station_token = $station->station_token;
        $this->status        = (bool) $station->status;
        $this->notes         = $station->notes;
        $this->showModal = true;
    }

    public function delete(mixed $id)
    {
        ScaleStation::findOrFail($id)->delete();
        session()->flash('message', 'Đã xóa trạm cân thành công!');
    }
}
