<?php

namespace App\Livewire\Production;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Item;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class BarcodeList extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedDept = ''; // Lọc theo phòng ban
    public $fromDate = '';
    public $toDate = '';

    // Dữ liệu cho Modal chi tiết
    public $selectedItem = null;

    // Reset phân trang khi search
    public function updatedSearch() { $this->resetPage(); }
    public function updatedSelectedDept() { $this->resetPage(); }

    public function render()
    {
        /** @var \App\Models\User $user */ // <-- Đã thêm dòng fix lỗi IDE
        $user = Auth::user();

        // 1. LẤY DANH SÁCH PHÒNG BAN (Để hiển thị Dropdown lọc)
        $departments = [];
        if ($user->hasRole('admin')) {
            $departments = Department::whereNotNull('code')->get();
        } else {
            $departments = $user->departments; // Quan hệ User n-n Department
        }

        // 2. QUERY DỮ LIỆU BARCODE (ITEM)
        $query = Item::query();
        $query->with(['creator', 'verifier']);

        // --- A. PHÂN QUYỀN DỮ LIỆU ---
        if (!$user->hasRole('admin')) {
            // Lấy danh sách Code của các phòng ban user này quản lý
            $myDeptCodes = $user->departments->pluck('code')->toArray();

            // Logic: Chỉ xem các Barcode có chứa mã phòng ban của mình
            // VD: User thuộc KHO-VAI -> xem được RM-KHO-VAI-001...
            $query->where(function($q) use ($myDeptCodes) {
                foreach ($myDeptCodes as $code) {
                    $q->orWhere('code', 'LIKE', '%' . $code . '%');
                }
            });
        }

        // --- B. BỘ LỌC NGƯỜI DÙNG ---

        // Lọc theo phòng ban cụ thể (Nếu user chọn dropdown)
        if ($this->selectedDept) {
            $query->where('code', 'LIKE', '%' . $this->selectedDept . '%');
        }

        // Tìm kiếm chung (Mã tem hoặc Note)
        if ($this->search) {
            $query->where(function($q) {
                $q->where('code', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('properties', 'LIKE', '%' . $this->search . '%'); // Tìm trong cả JSON
            });
        }

        // Lọc theo ngày
        if ($this->fromDate) $query->whereDate('created_at', '>=', $this->fromDate);
        if ($this->toDate) $query->whereDate('created_at', '<=', $this->toDate);

        // Sắp xếp mới nhất lên đầu
        $items = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.production.barcode-list', [
            'items' => $items,
            'departments' => $departments
        ]);
    }

    // Hàm xem chi tiết (Mở Modal)
    public function viewDetails($itemId)
    {
        $this->selectedItem = Item::find($itemId);
        $this->dispatch('open-detail-modal'); // Kích hoạt JS mở modal
    }
}
