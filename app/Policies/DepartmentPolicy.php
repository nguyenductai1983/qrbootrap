<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
     public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) { // Sử dụng Spatie hasRole
            return true;
        }
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('departments view')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xem danh sách phòng ban.');
    }

    public function view(User $user, Department $department): Response
    {
        return $user->hasPermissionTo('departments view')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xem phòng ban này.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('departments create')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền tạo phòng ban mới.');
    }

    public function update(User $user, Department $department): Response
    {
        // Không cho phép chỉnh sửa phòng ban 'admin' trừ khi bạn là admin và không tự hạ cấp
        if ($department->name === 'admin' && !$user->hasRole('admin')) {
             return Response::deny('Bạn không có quyền chỉnh sửa phòng ban Admin.');
        }
        return $user->hasPermissionTo('departments edit')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền cập nhật phòng ban này.');
    }

    public function delete(User $user, Department $department): Response
    {
        // Không cho phép xóa phòng ban 'admin' hoặc nếu có người dùng đang giữ phòng ban này
        if ($department->name === 'admin' || $department->users()->count() > 0) {
            return Response::deny('Không thể xóa phòng ban này.');
        }
        return $user->hasPermissionTo('departments delete')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xóa phòng ban này.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Department $department): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Department $department): bool
    {
        return false;
    }
}
