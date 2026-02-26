<?php

namespace App\Policies;

// use App\Models\Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class RolePolicy
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
        return $user->hasPermissionTo('roles view')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xem danh sách vai trò.');
    }

    /**
     * Determine whether the user can view the model.
     */
     public function view(User $user, Role $role): Response
    {
        return $user->hasPermissionTo('roles view')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xem vai trò này.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('roles create')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền tạo vai trò mới.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): Response
    {
        // Không cho phép chỉnh sửa vai trò 'admin' trừ khi bạn là admin và không tự hạ cấp
        if ($role->name === 'admin' && !$user->hasRole('admin')) {
             return Response::deny('Bạn không có quyền chỉnh sửa vai trò Admin.');
        }
        return $user->hasPermissionTo('roles edit')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền cập nhật vai trò này.');
    }

    /**
     * Determine whether the user can delete the model.
     */
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
    public function restore(User $user, Role $role): Response
    {
        return Response::deny('Bạn không có quyền khôi phục vai trò này.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
