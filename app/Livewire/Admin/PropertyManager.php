<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ItemProperty;
use App\Models\Product; // Thêm use model Product
class PropertyManager extends Component
{
    use WithPagination;

    public $code, $name, $type = 'text', $options = '', $sort_order = 0;
    public $is_required = false, $is_active = true, $propertyId;

    public $isEditMode = false;
    public $searchTerm = '';
    public $is_global = true; // Thêm biến này
    public $selectedProducts = []; // Mảng lưu các model được chọn
    public $allProducts = []; // Danh sách tất cả model để hiển thị
    public function mount()
    {
        // Gợi ý thứ tự (sort_order) tự động tăng cho thuộc tính mới
        $this->sort_order = ItemProperty::max('sort_order') + 1;
        $this->allProducts = Product::all(); // Lấy tất cả model để hiển thị trong form
    }

    public function resetInput()
    {
        $this->code = '';
        $this->name = '';
        $this->type = 'text';
        $this->options = '';
        $this->sort_order = ItemProperty::max('sort_order') + 1;
        $this->is_required = false;
        $this->is_active = true;
        $this->propertyId = null;
        $this->isEditMode = false;
        $this->is_global = true;
        $this->selectedProducts = [];
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:item_properties,code',
            'name' => 'required',
            'type' => 'required|in:text,number,select',
        ]);

        // Xử lý chuyển chuỗi tùy chọn (phân cách bằng dấu phẩy) thành Mảng (Array)
        $optionsArray = null;
        if ($this->type === 'select' && !empty($this->options)) {
            $optionsArray = array_map('trim', explode(',', $this->options));
        }

        $prop =  ItemProperty::create([
            'code' => strtoupper(str_replace(' ', '_', trim($this->code))), // Tự động in hoa và đổi dấu cách thành _
            'name' => $this->name,
            'type' => $this->type,
            'options' => $optionsArray,
            'is_required' => $this->is_required,
            'sort_order' => $this->sort_order ?: 0,
            'is_active' => $this->is_active,
            'is_global' => $this->is_global,
        ]);
        // Nếu không phải thuộc tính chung, lưu quan hệ với Product
        if (!$this->is_global) {
            $prop->products()->sync($this->selectedProducts);
        }
        session()->flash('message', 'Đã thêm cấu hình thuộc tính mới!');
        $this->resetInput();
        $this->dispatch('close-modal');
    }

    public function edit($id)
    {
        $property = ItemProperty::with('products')->find($id);
        if ($property) {
            $this->propertyId = $property->id;
            $this->code = $property->code;
            $this->name = $property->name;
            $this->type = $property->type;
            // Chuyển mảng về lại chuỗi để hiển thị trên input
            $this->options = is_array($property->options) ? implode(', ', $property->options) : '';
            $this->is_required = $property->is_required;
            $this->sort_order = $property->sort_order;
            $this->is_active = $property->is_active;

            $this->is_global = $property->is_global;
            $this->selectedProducts = $property->products->pluck('id')->toArray();

            $this->isEditMode = true;
            $this->dispatch('open-modal');
        }
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:item_properties,code,' . $this->propertyId,
            'name' => 'required',
            'type' => 'required|in:text,number,select',
        ]);

        if ($this->propertyId) {
            $property = ItemProperty::find($this->propertyId);

            $optionsArray = null;
            if ($this->type === 'select' && !empty($this->options)) {
                $optionsArray = array_map('trim', explode(',', $this->options));
            }

            $property->update([
                'code' => strtoupper(str_replace(' ', '_', trim($this->code))),
                'name' => $this->name,
                'type' => $this->type,
                'options' => $optionsArray,
                'is_required' => $this->is_required,
                'sort_order' => $this->sort_order ?: 0,
                'is_active' => $this->is_active,
                'is_global' => $this->is_global,
            ]);
            if (!$this->is_global) {
                $property->products()->sync($this->selectedProducts);
            } else {
                $property->products()->sync([]); // Xóa hết nếu là thuộc tính chung
            }
            session()->flash('message', 'Cập nhật thành công!');
            $this->resetInput();
            $this->dispatch('close-modal');
        }
    }

    public function delete($id)
    {
        ItemProperty::find($id)->delete();
        session()->flash('message', 'Đã xóa thuộc tính!');
    }

    public function render()
    {
        $properties = ItemProperty::where('code', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('sort_order', 'asc') // Ưu tiên sắp xếp theo thứ tự hiển thị
            ->paginate(10);

        return view('livewire.admin.property-manager', ['properties' => $properties]);
    }
}
