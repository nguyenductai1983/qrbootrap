<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Location;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Quản lý Vị Trí Kho (Location)')]
class LocationManager extends Component
{
    use WithPagination;

    public $search = '';
    public $locationId, $code, $name, $type = 'warehouse';
    public $isEditMode = false;
    public $showModal = false;

    // Phục vụ in mã QR
    public $selectedLocations = [];
    public $printFormat = 'QR';
    public $printColumns = 2;
    public $fontSize = 7;
    public $rowsPerPage = 2;

    // Phục vụ in nhãn code text
    public $codeColumns  = 3;
    public $codeRows     = 8;
    public $codeFontSize = 12;

    public function mount()
    {
        $uid = Auth::id();
        $this->printFormat  = cache()->get('loc_printFormat_'  . $uid, 'QR');
        $this->printColumns = cache()->get('loc_printColumns_' . $uid, 2);
        $this->fontSize     = cache()->get('loc_fontSize_'     . $uid, 7);
        $this->rowsPerPage  = cache()->get('loc_rowsPerPage_'  . $uid, 2);
        $this->codeColumns  = cache()->get('loc_codeColumns_'  . $uid, 3);
        $this->codeRows     = cache()->get('loc_codeRows_'     . $uid, 8);
        $this->codeFontSize = cache()->get('loc_codeFontSize_' . $uid, 12);
    }

    public function updatedPrintFormat($v)
    {
        cache()->forever('loc_printFormat_'  . Auth::id(), $v);
    }
    public function updatedPrintColumns($v)
    {
        cache()->forever('loc_printColumns_' . Auth::id(), $v);
    }
    public function updatedFontSize($v)
    {
        cache()->forever('loc_fontSize_'     . Auth::id(), $v);
    }
    public function updatedRowsPerPage($v)
    {
        cache()->forever('loc_rowsPerPage_'  . Auth::id(), $v);
    }
    public function updatedCodeColumns($v)
    {
        cache()->forever('loc_codeColumns_'  . Auth::id(), $v);
    }
    public function updatedCodeRows($v)
    {
        cache()->forever('loc_codeRows_'     . Auth::id(), $v);
    }
    public function updatedCodeFontSize($v)
    {
        cache()->forever('loc_codeFontSize_' . Auth::id(), $v);
    }

    protected function rules()
    {
        return [
            'code' => 'required|unique:locations,code,' . $this->locationId,
            'name' => 'required',
            'type' => 'nullable|string',
        ];
    }

    protected $messages = [
        'code.required' => 'Vui lòng nhập Mã Vị trí.',
        'code.unique'   => 'Mã Vị trí này đã tồn tại.',
        'name.required' => 'Vui lòng nhập Tên Vị trí.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    private function resetInputFields()
    {
        $this->locationId = null;
        $this->code = '';
        $this->name = '';
        $this->type = 'warehouse';
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        Location::updateOrCreate(
            ['id' => $this->locationId],
            [
                'code' => strtoupper(trim($this->code)),
                'name' => $this->name,
                'type' => $this->type ?? 'warehouse',
            ]
        );

        session()->flash('success', $this->locationId ? 'Cập nhật vị trí thành công.' : 'Tạo mới vị trí thành công.');
        $this->closeModal();
        $this->resetPage();
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        $this->locationId = $location->id;
        $this->code = $location->code;
        $this->name = $location->name;
        $this->type = $location->type;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $location = Location::find($id);
        if ($location) {
            if ($location->items()->count() > 0) {
                session()->flash('error', 'Không thể xóa vị trí này vì còn cây vải đang được lưu tại đây.');
                return;
            }
            $location->delete();
            session()->flash('success', 'Xóa vị trí thành công.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function toggleSelectAll($pageIds)
    {
        // Kiểm tra xem tất cả ID trên trang hiện tại đã được chọn chưa
        $allSelected = empty(array_diff($pageIds, $this->selectedLocations));

        if ($allSelected) {
            // Bỏ chọn tất cả các ID trên trang hiện tại
            $this->selectedLocations = array_values(array_diff($this->selectedLocations, $pageIds));
        } else {
            // Chọn tất cả các ID trên trang hiện tại
            $this->selectedLocations = array_unique(array_merge($this->selectedLocations, $pageIds));
        }
    }

    public function clearSelection()
    {
        $this->selectedLocations = [];
    }

    public function printQR()
    {
        if (empty($this->selectedLocations)) {
            session()->flash('error', 'Vui lòng chọn ít nhất 1 vị trí để in.');
            return;
        }

        $ids = implode(',', $this->selectedLocations);
        $url = route('locations.print', [
            'ids'      => $ids,
            'format'   => $this->printFormat,
            'cols'     => $this->printColumns,
            'rows'     => $this->rowsPerPage,
            'fontSize' => $this->fontSize,
        ]);

        $this->dispatch('open-print-tab', url: $url);
        $this->selectedLocations = [];
    }

    public function printCode()
    {
        if (empty($this->selectedLocations)) {
            session()->flash('error', 'Vui lòng chọn ít nhất 1 vị trí để in.');
            return;
        }

        $ids = implode(',', $this->selectedLocations);
        $url = route('locations.print-code', [
            'ids'      => $ids,
            'cols'     => $this->codeColumns,
            'rows'     => $this->codeRows,
            'fontSize' => $this->codeFontSize,
        ]);

        $this->dispatch('open-print-tab', url: $url);
        $this->selectedLocations = [];
    }

    public function printQRSingle($id)
    {
        $url = route('locations.print', [
            'ids'      => $id,
            'format'   => $this->printFormat,
            'cols'     => $this->printColumns,
            'rows'     => $this->rowsPerPage,
            'fontSize' => $this->fontSize,
        ]);

        $this->dispatch('open-print-tab', url: $url);
    }

    public function printCodeSingle($id)
    {
        $url = route('locations.print-code', [
            'ids'      => $id,
            'cols'     => $this->codeColumns,
            'rows'     => $this->codeRows,
            'fontSize' => $this->codeFontSize,
        ]);

        $this->dispatch('open-print-tab', url: $url);
    }

    public function exportInventory()
    {
        $fileName = 'TonKho_' . date('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\WarehouseItemsExport, $fileName);
    }

    public function render()
    {
        $locations = Location::withCount('items')
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('livewire.warehouse.location-manager', [
            'locations' => $locations,
        ]);
    }
}
