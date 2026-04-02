<?php

namespace App\Traits;

use App\Scopes\DepartmentScope;

trait ScopedByDepartment
{
    /**
     * Boot the scoped by department trait for a model.
     *
     * @return void
     */
    protected static function bootScopedByDepartment()
    {
        static::addGlobalScope(new DepartmentScope);
    }
}
