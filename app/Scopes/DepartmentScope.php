<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class DepartmentScope implements Scope
{
    /**
     * Tự động thêm điều kiện lọc theo department_id nếu user không có quyền view_all_departments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (!$user->canViewAllDepartments()) {
                // Kiểm tra xem model có cột department_id không (để phòng hờ lỗi SQL nếu Join bảng)
                // Tuy nhiên ta chỉ gắn Trait ở những model chắc chắn có cột.
                $builder->where($model->getTable() . '.department_id', $user->department_id);
            }
        }
    }
}
