<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PrintStation;
use Livewire\WithPagination;

class PrintStationManager extends Component
{
    use WithPagination;

    public $name, $code, $status = true, $stationId;
    public $client_type = 'browser', $station_token, $template_name;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255|unique:print_stations,code',
        'status' => 'boolean',
        'client_type' => 'required|in:browser,app',
        'station_token' => 'required_if:client_type,app|nullable|string|max:255',
        'template_name' => 'nullable|string|max:255',
    ];

    public function render()
    {
        return view('livewire.admin.print-station-manager', [
            'stations' => PrintStation::paginate(10),
        ]);
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->code = '';
        $this->status = true;
        $this->client_type = 'browser';
        $this->station_token = '';
        $this->template_name = '';
        $this->stationId = null;
    }

    public function resetForm()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function store()
    {
        $rules = $this->rules;
        if ($this->stationId) {
            $rules['code'] = 'required|string|max:255|unique:print_stations,code,' . $this->stationId;
        }

        $this->validate($rules);

        PrintStation::updateOrCreate(['id' => $this->stationId], [
            'name' => $this->name,
            'code' => $this->code,
            'status' => $this->status,
            'client_type' => $this->client_type,
            'station_token' => $this->client_type === 'app' ? $this->station_token : null,
            'template_name' => $this->client_type === 'app' ? $this->template_name : null,
        ]);

        session()->flash('message', $this->stationId ? 'Cập nhật thành công!' : 'Tạo mới thành công!');
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $station = PrintStation::findOrFail($id);
        $this->stationId = $id;
        $this->name = $station->name;
        $this->code = $station->code;
        $this->status = (bool) $station->status;
        $this->client_type = $station->client_type ?: 'browser';
        $this->station_token = $station->station_token;
        $this->template_name = $station->template_name;
        
        $this->dispatch('show-modal');
        $this->showModal = true;
    }

    public function delete($id)
    {
        PrintStation::findOrFail($id)->delete();
        session()->flash('message', 'Xóa thành công!');
    }
}
