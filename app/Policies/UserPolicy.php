<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role; // <-- Import Role model
class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
     public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) { // Kiểm tra vai trò admin bằng Spatie
            return true;
        }
        return null;
    }
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('roles view')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xem danh sách vai trò.');
    }

    public function view(User $user, Role $role): Response
    {
        return $user->hasPermissionTo('roles view')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xem vai trò này.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('roles create')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền tạo vai trò mới.');
    }

    public function update(User $user, Role $role): Response
    {
        // Không cho phép chỉnh sửa vai trò 'admin' trừ khi bạn là admin và không tự hạ cấp
        if ($role->name === 'admin' && !$user->hasRole('admin')) {
             return Response::deny('Bạn không có quyền chỉnh sửa vai trò Admin.');
        }
        return $user->hasPermissionTo('edit roles')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền cập nhật vai trò này.');
    }

    public function delete(User $user, Role $role): Response
    {
        // Không cho phép xóa vai trò 'admin' hoặc nếu có người dùng đang giữ vai trò này
        if ($role->name === 'admin' || $role->users()->count() > 0) {
            return Response::deny('Không thể xóa vai trò này.');
        }
        return $user->hasPermissionTo('roles delete')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xóa vai trò này.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
