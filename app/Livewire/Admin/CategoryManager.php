<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Color;
use App\Models\Specification;
use App\Models\PlasticType;

class CategoryManager extends Component
{
    public $activeTab = 'color'; // Tab mặc định khi mở trang
    public $dataList = [];

    // Các biến cho Form Thêm/Sửa
    public $itemId = null;
    public $code = '';
    public $name = '';
    public $is_active = true;

    public function mount()
    {
        $this->loadData();
    }

    // Hàm chuyển Tab
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetForm();
        $this->loadData();
    }

    // Hàm lấy tên Model dựa trên Tab đang chọn
    private function getModelClass()
    {
        return match ($this->activeTab) {
            'color' => Color::class,
            'specification' => Specification::class,
            'plastic_type' => PlasticType::class,
        };
    }

    // Load danh sách dữ liệu
    public function loadData()
    {
        $model = $this->getModelClass();
        $this->dataList = $model::orderBy('id', 'desc')->get();
    }

    public function resetForm()
    {
        $this->itemId = null;
        $this->code = '';
        $this->name = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    // Bấm nút Sửa
    public function edit($id)
    {
        $model = $this->getModelClass();
        $record = $model::find($id);

        $this->itemId = $record->id;
        $this->code = $record->code;
        $this->name = $record->name;
        $this->is_active = $record->is_active;

        $this->dispatch('show-modal');
    }

    // Lưu dữ liệu (Dùng chung cho cả Thêm và Sửa)
    public function save()
    {
        $this->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        $modelClass = $this->getModelClass();

        // Kiểm tra trùng Mã (Code) trong cùng 1 bảng
        $exists = $modelClass::where('code', $this->code)->where('id', '!=', $this->itemId)->exists();
        if ($exists) {
            $this->addError('code', 'Mã này đã tồn tại trong danh mục!');
            return;
        }

        $modelClass::updateOrCreate(
            ['id' => $this->itemId],
            [
                'code' => strtoupper($this->code), // Tự động in hoa mã
                'name' => $this->name,
                'is_active' => $this->is_active
            ]
        );

        $this->loadData();
        $this->dispatch('hide-modal');
        session()->flash('success', 'Lưu dữ liệu thành công!');
        $this->resetForm();
    }

    // Bật/Tắt trạng thái nhanh
    public function toggleActive($id)
    {
        $modelClass = $this->getModelClass();
        $record = $modelClass::find($id);
        $record->is_active = !$record->is_active;
        $record->save();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.category-manager');
    }
}
