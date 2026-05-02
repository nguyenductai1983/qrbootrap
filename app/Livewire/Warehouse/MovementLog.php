<?php

namespace App\Livewire\Warehouse;

use App\Models\ItemMovement;
use App\Models\User;
use App\Enums\MovementAction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Nhật Ký Luân Chuyển Kho')]
class MovementLog extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $searchCode = '';
    public $actionType = '';
    public $userId = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function updating(string $field)
    {
        // Reset về trang 1 khi gõ tìm kiếm hoặc đổi filter
        if (in_array($field, ['searchCode', 'actionType', 'userId', 'dateFrom', 'dateTo'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset(['searchCode', 'actionType', 'userId', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render()
    {
        $query = ItemMovement::with(['item', 'user', 'fromLocation', 'toLocation'])
            ->orderBy('created_at', 'desc');

        // Lọc theo Mã cây vải hoặc Ghi chú
        if (!empty($this->searchCode)) {
            $query->where(function ($q) {
                $q->whereHas('item', function ($sub) {
                    $sub->where('code', 'like', '%' . $this->searchCode . '%');
                })
                ->orWhere('note', 'like', '%' . $this->searchCode . '%');
            });
        }

        // Lọc theo Loại hành động
        if ($this->actionType !== '') {
            $query->where('action_type', $this->actionType);
        }

        // Lọc theo User
        if ($this->userId !== '') {
            if ($this->userId === 'system_null') {
                $query->whereNull('user_id');
            } else {
                $query->where('user_id', $this->userId);
            }
        }

        // Lọc theo Ngày tháng
        if (!empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if (!empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return view('livewire.warehouse.movement-log', [
            'logs' => $query->paginate(20),
            'actionEnum' => MovementAction::cases(),
            'users' => User::orderBy('name')->get()
        ]);
    }
}
