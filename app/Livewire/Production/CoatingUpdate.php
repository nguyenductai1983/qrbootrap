<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Item;
use App\Models\Order;
use App\Models\ItemHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;

#[Title('Cập Nhật Thông Tin Tráng')]
class CoatingUpdate extends Component
{
    public $codeInput = '';
    public $item = null;
    public $gsmlami = '';
    public $selectedOrderId = '';
    public $availableOrders = [];
    public $filterOrderSearch = '';

    public function mount()
    {
        $this->loadAvailableOrders();
    }

    public function loadAvailableOrders()
    {
        $this->availableOrders = Order::where('status', '!=', \App\Enums\OrderStatus::COMPLETED)
            ->orderBy('code')
            ->get();
    }

    public function searchByCode()
    {
        $item = Item::where('code', trim($this->codeInput))->first();

        if (!$item) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Không tìm thấy mã tem này!']);
            return;
        }

        // Kiểm tra xem có phải tem tráng không (tùy chọn)
        // if ($item->type != 2 && is_null($item->gsmlami)) {
        //     $this->dispatch('alert', ['type' => 'error', 'message' => 'Mã này không phải là tem tráng!']);
        //     return;
        // }

        $this->selectItem($item->id);
        $this->codeInput = '';
    }

    public function selectItem($itemId)
    {
        $this->item = Item::with('order')->find($itemId);
        if ($this->item) {
            $this->gsmlami = $this->item->gsmlami;
            $this->selectedOrderId = $this->item->order_id;
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Đã chọn mã: ' . $this->item->code]);
        }
    }

    public function updateInfo()
    {
        if (!$this->item) return;

        $this->validate([
            'gsmlami' => 'required|numeric|min:0',
            'selectedOrderId' => 'required|exists:orders,id',
        ]);

        DB::beginTransaction();
        try {
            // Lấy lại instance mới nhất để tránh conflict
            $currentItem = Item::find($this->item->id);

            // 1. Kiểm tra GSMLAMI & LAMI
            if ($currentItem->gsmlami != $this->gsmlami) {
                $oldGsmLami = $currentItem->gsmlami;
                $oldLami = $currentItem->lami;
                
                $newGsmLami = (float) $this->gsmlami;
                $newLami = $newGsmLami - (float) $currentItem->gsm;

                $currentItem->gsmlami = $newGsmLami;
                $currentItem->lami = $newLami;

                ItemHistory::create([
                    'item_id' => $currentItem->id,
                    'user_id' => Auth::id(),
                    'field_name' => 'gsmlami',
                    'old_value' => $oldGsmLami,
                    'new_value' => $newGsmLami,
                ]);

                ItemHistory::create([
                    'item_id' => $currentItem->id,
                    'user_id' => Auth::id(),
                    'field_name' => 'lami',
                    'old_value' => $oldLami,
                    'new_value' => $newLami,
                ]);
            }

            // 2. Kiểm tra Order ID
            if ($currentItem->order_id != $this->selectedOrderId) {
                $oldOrderId = $currentItem->order_id;
                $oldOrder = Order::find($oldOrderId);
                $oldOrderCode = $oldOrder ? $oldOrder->code : 'N/A';
                
                $newOrder = Order::find($this->selectedOrderId);
                $newOrderCode = $newOrder ? $newOrder->code : 'N/A';

                $currentItem->order_id = $this->selectedOrderId;

                ItemHistory::create([
                    'item_id' => $currentItem->id,
                    'user_id' => Auth::id(),
                    'field_name' => 'order_id',
                    'old_value' => $oldOrderCode,
                    'new_value' => $newOrderCode,
                ]);
            }

            if ($currentItem->isDirty()) {
                $currentItem->save();
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Cập nhật thông tin thành công!']);
                $this->item = null;
                $this->gsmlami = '';
                $this->selectedOrderId = '';
            } else {
                $this->dispatch('alert', ['type' => 'info', 'message' => 'Không có thay đổi nào.']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    public function cancelEdit()
    {
        $this->item = null;
        $this->gsmlami = '';
        $this->selectedOrderId = '';
    }

    public function render()
    {
        $recentItems = Item::whereNotNull('gsmlami')
            ->when($this->filterOrderSearch, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('order', function ($o) {
                        $o->where('code', 'like', '%' . $this->filterOrderSearch . '%');
                    })
                    ->orWhereHas('order.productionOrder', function ($po) {
                        $po->where('code', 'like', '%' . $this->filterOrderSearch . '%');
                    })
                    ->orWhere('code', 'like', '%' . $this->filterOrderSearch . '%');
                });
            })
            ->with(['order', 'order.productionOrder'])
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();

        return view('livewire.production.coating-update', [
            'recentItems' => $recentItems
        ]);
    }
}
