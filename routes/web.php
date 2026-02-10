<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\QrcodeIndex;
use App\Livewire\Actions\Logout; // Import Action class của bạn
use App\Livewire\UserList; // Import UserList component
use App\Livewire\UserForm; // Import UserForm component
use App\Livewire\DepartmentList; // Import DepartmentList component
use App\Livewire\DepartmentForm; // Import DepartmentForm component
use App\Livewire\RoleList; // <-- Import RoleList
use App\Livewire\RoleForm; // <-- Import RoleForm
use App\Livewire\PermissionList; // <-- Import PermissionList
use App\Livewire\PermissionForm; // <-- Import PermissionForm
use Spatie\Permission\Models\Role;
use App\Livewire\Production\BarcodeGenerator;
use App\Livewire\Production\ScanProduct;
use App\Livewire\Admin\OrderManager;
use App\Livewire\Admin\ProductModelManager;
use App\Livewire\Production\ExcelManager;
use App\Livewire\Production\BarcodeList;

//Role::withoutGlobalScopes()->get(); // Lấy tất cả vai trò mà không áp dụng bất kỳ global scope nào
Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
Route::view('/huong-dan', 'guide')->middleware(['auth'])->name('guide');
require __DIR__ . '/auth.php';
Route::middleware(['auth', 'verified'])->group(function () {
    // Quản lý người dùng
    Route::get('admin/users', UserList::class)
        ->name('users.index')
        // ->middleware('permission:view users');
        ->middleware('role:admin');
    // Thêm middleware kiểm tra quyền tạo người dùng

    Route::get('admin/users/create', UserForm::class)
        ->name('users.create')
        ->middleware('permission:create users');

    Route::get('admin/users/{userId}/edit', UserForm::class)
        ->name('users.edit')
        ->middleware('permission:edit users');

    // Quản lý phòng ban
    Route::get('admin/departments', DepartmentList::class)
        ->name('departments.index')
        ->middleware('permission:view departments');

    Route::get('admin/departments/create', DepartmentForm::class)
        ->name('departments.create')
        ->middleware('permission:create departments');

    Route::get('admin/departments/{departmentId}/edit', DepartmentForm::class)
        ->name('departments.edit')
        ->middleware('permission:edit departments');

    // <-- Quản lý Vai trò -->
    Route::get('admin/roles', RoleList::class)
        ->name('roles.index')
        ->middleware('permission:view roles');

    Route::get('admin/roles/create', RoleForm::class)
        ->name('roles.create')
        ->middleware('permission:create roles');

    Route::get('admin/roles/{roleId}/edit', RoleForm::class)
        ->name('roles.edit')
        ->middleware('permission:edit roles');

    // <-- Quản lý Quyền hạn (Tùy chọn, ít dùng trực tiếp) -->
    Route::get('admin/permissions', PermissionList::class)
        ->name('permissions.index')
        ->middleware('permission:view permissions');

    Route::get('admin/permissions/create', PermissionForm::class)
        ->name('permissions.create')
        ->middleware('permission:create permissions');

    Route::get('admin/permissions/{permissionId}/edit', PermissionForm::class)
        ->name('permissions.edit')
        ->middleware('permission:edit permissions');
    Route::get('/production/barcode-generator', BarcodeGenerator::class)
        ->name('production.barcode-generator')
        ->middleware('permission:print barcodes');
    Route::get('/scan-mobile', ScanProduct::class)->name('production.scan');
    Route::get('/orders', OrderManager::class)->name('admin.orders');
    Route::get('/models', ProductModelManager::class)->name('admin.models');
    Route::get('/excel-manager', ExcelManager::class)->name('production.excel-manager');
    Route::get('/barcode-list', BarcodeList::class)->name('production.list');
});
