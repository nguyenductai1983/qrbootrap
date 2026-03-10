<?php

namespace App\Policies;

// use App\Models\Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Permission; // <-- Import Permission model
class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('permissions view')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xem danh sách quyền hạn.');
    }

    public function view(User $user, Permission $permission): Response
    {
        return $user->hasPermissionTo('permissions view')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xem quyền hạn này.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('permissions create')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền tạo quyền hạn mới.');
    }

    public function update(User $user, Permission $permission): Response
    {
        return $user->hasPermissionTo('permissions edit')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền cập nhật quyền hạn này.');
    }

    public function delete(User $user, Permission $permission): Response
    {
        // Không cho phép xóa quyền hạn nếu có vai trò nào đang giữ nó
        if ($permission->roles()->count() > 0) {
            return Response::deny('Không thể xóa quyền hạn này vì có vai trò đang giữ nó.');
        }
        return $user->hasPermissionTo('permissions delete')
                    ? Response::allow()
                    : Response::deny('Bạn không có quyền xóa quyền hạn này.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): Response
    {
        return Response::deny('Bạn không có quyền khôi phục quyền hạn này.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): Response
    {
        return Response::deny('Bạn không có quyền xóa vĩnh viễn quyền hạn này.');
    }
}
