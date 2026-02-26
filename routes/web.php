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
use App\Livewire\Admin\ProductManager;
use App\Livewire\Production\ExcelManager;
use App\Livewire\Production\BarcodeList;
use App\Livewire\Admin\PropertyManager;
use App\Livewire\Admin\ItemTypeManager;
use App\Livewire\Production\ItemManager;
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

    // ==========================================
    // 1. NHÓM ADMIN (Tiền tố: /admin/...)
    // ==========================================
    Route::prefix('admin')->group(function () {

        // --- Quản lý Users ---
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', UserList::class)->name('index')->middleware('role:admin');
            Route::get('/create', UserForm::class)->name('create')->middleware('permission:users create');
            Route::get('/{userId}/edit', UserForm::class)->name('edit')->middleware('permission:users edit');
        });

        // --- Quản lý Departments ---
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', DepartmentList::class)->name('index')->middleware('permission:departments view');
            Route::get('/create', DepartmentForm::class)->name('create')->middleware('permission:departments create');
            Route::get('/{departmentId}/edit', DepartmentForm::class)->name('edit')->middleware('permission:departments edit');
        });

        // --- Quản lý Roles ---
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', RoleList::class)->name('index')->middleware('permission:roles view');
            Route::get('/create', RoleForm::class)->name('create')->middleware('permission:roles create');
            Route::get('/{roleId}/edit', RoleForm::class)->name('edit')->middleware('permission:roles edit');
        });

        // --- Quản lý Permissions ---
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', PermissionList::class)->name('index')->middleware('permission:permissions view');
            Route::get('/create', PermissionForm::class)->name('create')->middleware('permission:permissions create');
            Route::get('/{permissionId}/edit', PermissionForm::class)->name('edit')->middleware('permission:permissions edit');
        });

        // --- Các danh mục Admin khác ---

    });
    Route::middleware(['permission:product manager'])->prefix('manager')->group(function () {
        Route::get('/orders', OrderManager::class)->name('manager.orders');
        Route::get('/products', ProductManager::class)->name('manager.products');
        Route::get('/properties', PropertyManager::class)->name('manager.properties');
        Route::get('/item-types', ItemTypeManager::class)->name('manager.item-types');
        Route::get('/items', ItemManager::class)->name('manager.items');
    });
    // ==========================================
    // 2. NHÓM PRODUCTION (Sản xuất)
    // ==========================================
    // Ví dụ: Bọc tất cả các route in tem, quét tem vào chung 1 quyền "manage production"
    // Nếu các route này dùng chung 1 quyền, đây là cách bạn bọc middleware cho cả group:
    Route::middleware(['permission:print barcodes'])->group(function () {
        Route::get('/production/barcode-generator', BarcodeGenerator::class)->name('production.barcode-generator');
        Route::get('/barcode-list', BarcodeList::class)->name('production.list');
    });

    // Các route production khác không dùng chung middleware trên
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/scan-mobile', ScanProduct::class)->name('scan'); // Đã gộp tiền tố URL
        Route::get('/excel-manager', ExcelManager::class)->name('excel-manager');
    });
});
