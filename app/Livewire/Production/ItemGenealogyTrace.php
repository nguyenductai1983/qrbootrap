<?php

namespace App\Livewire\Production;

use App\Models\Item;
use Livewire\Component;
use Livewire\Attributes\Title;
use App\Livewire\Traits\WithReprinting;

#[Title('Phân tích Phả hệ Sản phẩm')]
class ItemGenealogyTrace extends Component
{
    use WithReprinting;

    public mixed $itemId = null;
    public mixed $rootItem = null;

    public function mount(mixed $id)
    {
        $this->itemId = $id;
        $this->loadData();
    }

    public function loadData()
    {
        // Tải đệ quy tất cả cây cha và cây con cùng với các thông số liên quan
        $this->rootItem = Item::with(['allParents', 'allChildren', 'product', 'department', 'color', 'creator', 'machine', 'order'])
            ->findOrFail($this->itemId);
    }

    public function traceItem(mixed $id)
    {
        return redirect()->route('items.genealogy', $id);
    }

    public function render()
    {
        return view('livewire.production.item-genealogy-trace');
    }
}
