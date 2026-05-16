<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Item;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

#[Title('Lịch sử ca làm việc')]
class ScanHistory extends Component
{
    use WithPagination;
    use \App\Livewire\Traits\WithReprinting; // Để sử dụng tính năng in lại

    // Các bộ lọc
    /**
     * @var string
     */
    public $startDate;
    /**
     * @var string
     */
    public $endDate;
    /**
     * @var string
     */
    public $filterUserId = '';
    /**
     * @var string
     */
    public $filterShiftId = '';

    // Dữ liệu tham khảo cho bộ lọc
    public $users = [];
    public $shifts = [];

    public function mount()
    {
        // Mặc định lấy dữ liệu 2 ngày gần nhất
        $this->startDate = Carbon::now()->subDays(1)->startOfDay()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfDay()->format('Y-m-d');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Load danh sách Users & Shifts cho bộ lọc (Nếu là quản lý)
        if ($this->canViewAllShift()) {
            $this->shifts = Shift::all();

            // Lấy danh sách nhân viên để lọc (trong cùng ca hoặc tất cả)
            if ($user->isAdmin() || $user->hasRole('admin') || $user->hasRole('manager')) {
                $this->users = User::all();
            } else {
                // Quản lý ca: chỉ lấy user cùng ca
                $this->users = User::where('shift_id', $user->shift_id)->get();
                $this->filterShiftId = $user->shift_id;
            }
        }
    }

    public function canViewAllShift()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Kiểm tra xem user có phải admin/manager hoặc có quyền xem toàn bộ ca
        // Bạn có thể đổi tên permission 'view_shift_history' theo đúng hệ thống của bạn
        return $user->isAdmin() || $user->hasRole(['admin', 'manager', 'leader']) || $user->can('view_shift_history');
    }

    public function resetFilters()
    {
        $this->startDate = Carbon::now()->subDays(30)->startOfDay()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfDay()->format('Y-m-d');
        $this->filterUserId = '';

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasRole(['admin', 'manager'])) {
            $this->filterShiftId = $user->shift_id;
        } else {
            $this->filterShiftId = '';
        }
        $this->resetPage();
    }

    public function updating(string $property)
    {
        if (in_array($property, ['startDate', 'endDate', 'filterUserId', 'filterShiftId'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Item::with(['product', 'color', 'order', 'verifier', 'machine'])
            ->whereNotNull('verified_at'); // Chỉ lấy những tem đã quét

        // 1. Lọc theo thời gian (Bắt buộc)
        if ($this->startDate) {
            $query->where('verified_at', '>=', Carbon::parse($this->startDate)->startOfDay());
        }
        if ($this->endDate) {
            $query->where('verified_at', '<=', Carbon::parse($this->endDate)->endOfDay());
        }

        // 2. Phân quyền hiển thị
        if ($this->canViewAllShift()) {
            // Là quản lý -> Có thể lọc theo user cụ thể
            if ($this->filterUserId) {
                $query->where('verified_by', $this->filterUserId);
            }

            // Hoặc lọc theo ca
            if ($this->filterShiftId) {
                $query->whereHas('verifier', function ($q) {
                    $q->where('shift_id', $this->filterShiftId);
                });
            } else if (!$user->isAdmin() && !$user->hasRole(['admin', 'manager'])) {
                // Nếu là Tổ trưởng (Leader) nhưng ko chọn ca, mặc định lấy ca của mình
                $query->whereHas('verifier', function ($q) use ($user) {
                    $q->where('shift_id', $user->shift_id);
                });
            }
        } else {
            // Là nhân viên bình thường -> Chỉ xem của mình
            $query->where('verified_by', $user->id);
        }

        // Clone query để tính tổng trước khi phân trang
        $summaryQuery = clone $query;
        $totalItems = $summaryQuery->count();
        $totalLength = $summaryQuery->sum('length');

        $items = $query->orderBy('verified_at', 'desc')->paginate(50);

        return view('livewire.production.scan-history', [
            'items' => $items,
            'totalItems' => $totalItems,
            'totalLength' => $totalLength
        ]);
    }
}
