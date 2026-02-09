<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Department;
use App\Policies\UserPolicy; // <-- Import UserPolicy
use App\Policies\DepartmentPolicy;
use App\Policies\RolePolicy; // <-- Import RolePolicy
use App\Policies\PermissionPolicy; // <-- Import PermissionPolicy
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role; // <-- Import Spatie Role model
use Spatie\Permission\Models\Permission; // <-- Import Spatie Permission model

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Department::class => DepartmentPolicy::class,
        Role::class => RolePolicy::class, // <-- Đăng ký RolePolicy
        Permission::class => PermissionPolicy::class, // <-- Đăng ký PermissionPolicy
    ];

    public function boot(): void
    {
        //
    }
}
