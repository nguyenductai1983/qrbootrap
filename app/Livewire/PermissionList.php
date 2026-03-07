<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;

class PermissionList extends Component
{
    use WithPagination;

    public $search = '';
    public $showSuggestions = false; // Biến kiểm soát ẩn/hiện bảng gợi ý
    public function updatingSearch()
    {
        $this->resetPage();
        $this->showSuggestions = true; // Hiện gợi ý khi người dùng bắt đầu gõ
    }

    public function deletePermission($permissionId)
    {
        // Kiểm tra quyền
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->can('permissions delete')) {
            session()->flash('error', 'Bạn không có quyền xóa quyền hạn!');
            return;
        }

        $permission = Permission::find($permissionId);
        if ($permission) {
            // Kiểm tra xem có vai trò nào đang giữ quyền này không
            if ($permission->roles()->count() > 0) {
                session()->flash('error', 'Không thể xóa quyền hạn này vì có vai trò đang giữ nó.');
                return;
            }
            $permission->delete();
            session()->flash('success', 'Quyền hạn đã được xóa thành công!');
        } else {
            session()->flash('error', 'Không tìm thấy quyền hạn để xóa.');
        }
    }
    // Hàm xử lý khi người dùng click vào 1 dòng gợi ý
    public function selectSuggestion($permissionName)
    {
        $this->search = $permissionName; // Điền tên vào ô input
        $this->showSuggestions = false;  // Giấu bảng gợi ý đi
        $this->resetPage();              // Cập nhật lại bảng dữ liệu chính
    }
    public function clearSearch()
    {
        $this->search = '';
        $this->showSuggestions = false;
        $this->resetPage();
    }
    public function render()
    {
        // 1. Tạo query dùng chung
        $query = Permission::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });

        // 2. Lấy dữ liệu cho bảng chính (Paginate)
        $permissions = (clone $query)
            ->withCount('roles')
            ->orderBy('id')
            ->paginate(20);

        // 3. Lấy dữ liệu cho danh sách gợi ý (Chỉ lấy 5 kết quả đầu tiên cho nhẹ)
        $suggestions = collect();
        if ($this->showSuggestions && strlen($this->search) > 0) {
            $suggestions = (clone $query)->limit(5)->get();
        }

        return view('livewire.admin.permission-list', [
            'permissions' => $permissions,
            'suggestions' => $suggestions, // Trả thêm biến suggestions ra View
        ]);
    }
}
