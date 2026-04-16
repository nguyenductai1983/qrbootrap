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
use App\Livewire\Production\BarcodeGeneratorExcel;
use App\Livewire\Production\ScanProduct;
use App\Livewire\Admin\OrderManager;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Production\ExcelManager;
use App\Livewire\Production\BarcodeList;
use App\Livewire\Admin\PropertyManager;
use App\Livewire\Admin\ItemTypeManager;
use App\Livewire\Production\ItemManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\MachineManager;
use App\Livewire\Admin\UserMachineAssignment;
use App\Livewire\Warehouse\LocationManager;
use App\Livewire\Production\CoatingConfirmation;
use App\Livewire\Warehouse\ScanToLocation;
use App\Livewire\Admin\PrintStationManager;
use App\Livewire\Admin\UserPrintStationAssignment;
use App\Livewire\Admin\ScaleStationManager;
use App\Livewire\Admin\UserScaleStationAssignment;
use App\Livewire\Warehouse\ReportManager;
use App\Livewire\Warehouse\WarehouseInboundList;
use App\Livewire\Warehouse\WarehouseDashboard;
use App\Livewire\Dashboard\AnalyticsDashboard;
use App\Http\Controllers\Print\PrintController;
use App\Http\Controllers\Print\PrintAppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrinterController;
use App\Livewire\Warehouse\ScanToLocationClassic;
//Role::withoutGlobalScopes()->get(); // Lấy tất cả vai trò mà không áp dụng bất kỳ global scope nào
Route::view('/', 'welcome');
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
Route::view('/guide', 'guide')->middleware(['auth'])->name('guide');
require __DIR__ . '/auth.php';
Route::post('/api/login', [AuthController::class, 'login']);
// Tất cả các Route liên quan đến máy in đều dùng chung 1 "chìa khóa" Sanctum
Route::prefix('printapi')->middleware('auth:sanctum')->group(function () {
    Route::get('/pending-jobs/{station_token}', [PrintAppController::class, 'pendingJobs']);
    Route::post('/statusupdate', [PrintAppController::class, 'receiveStatus']);
    Route::get('/config', [PrintAppController::class, 'getSocketConfig']);
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');
    // Trang Báo Cáo & Phân Tích — có quyền xem bất kỳ loại nào
    Route::get('/analytics', AnalyticsDashboard::class)->name('analytics');

    // Các Route phục vụ máy in nội bộ xử lý in bù
    Route::get('/printer/pending-jobs/{mac}', [PrinterController::class, 'pendingJobs']);
    Route::post('/printer/mark-printed/{jobId}', [PrinterController::class, 'markPrinted']);

    // ==========================================
    // 1. NHÓM ADMIN (Tiền tố: /admin/...)
    // ==========================================
    Route::prefix('admin')->group(function () {

        // --- Quản lý Users ---
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', UserList::class)->name('index')->middleware('role:admin');
            Route::get('/create', UserForm::class)->name('create')->middleware('permission:users.create');
            Route::get('/{userId}/edit', UserForm::class)->name('edit')->middleware('permission:users.edit');
        });

        // --- Quản lý Departments ---
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', DepartmentList::class)->name('index')->middleware('permission:departments.view');
            Route::get('/create', DepartmentForm::class)->name('create')->middleware('permission:departments.create');
            Route::get('/{departmentId}/edit', DepartmentForm::class)->name('edit')->middleware('permission:departments.edit');
        });

        // --- Quản lý Roles ---
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', RoleList::class)->name('index')->middleware('permission:roles.view');
            Route::get('/create', RoleForm::class)->name('create')->middleware('permission:roles.create');
            Route::get('/{roleId}/edit', RoleForm::class)->name('edit')->middleware('permission:roles.edit');
        });

        // --- Quản lý Permissions ---
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', PermissionList::class)->name('index')->middleware('permission:permissions.view');
            Route::get('/create', PermissionForm::class)->name('create')->middleware('permission:permissions.create');
            Route::get('/{permissionId}/edit', PermissionForm::class)->name('edit')->middleware('permission:permissions.edit');
        });

        // --- Các danh mục Admin khác ---

    });

    Route::middleware(['permission:manager'])->prefix('manager')->group(function () {
        Route::get('/orders', OrderManager::class)->name('manager.orders');
        Route::get('/products', ProductManager::class)->name('manager.products');
        Route::get('/properties', PropertyManager::class)->name('manager.properties');
        Route::get('/item-types', ItemTypeManager::class)->name('manager.item-types');
        Route::get('/categories', CategoryManager::class)->name('manager.categories');
        Route::get('/machines', MachineManager::class)->name('manager.machines');
        Route::get('/user-machines', UserMachineAssignment::class)->name('manager.user-machines');
        Route::get('/print-stations', PrintStationManager::class)->name('manager.print-stations');
        Route::get('/user-print-stations', UserPrintStationAssignment::class)->name('manager.user-print-stations');
        Route::get('/scale-stations', ScaleStationManager::class)->name('manager.scale-stations');
        Route::get('/user-scale-stations', UserScaleStationAssignment::class)->name('manager.user-scale-stations');
    });

    // Tách riêng quản lý Items để các bộ phận khác (có quyền items.view) truy cập được
    Route::middleware(['permission:items.view'])->prefix('manager')->group(function () {
        Route::get('/items', ItemManager::class)->name('manager.items');
        Route::get('/items/{id}/genealogy', \App\Livewire\Production\ItemGenealogyTrace::class)->name('manager.items.genealogy');
    });

    // ==========================================
    // 2. NHÓM PRODUCTION (Sản xuất)
    // ==========================================
    // Ví dụ: Bọc tất cả các route in tem, quét tem vào chung 1 quyền "manage production"
    // Nếu các route này dùng chung 1 quyền, đây là cách bạn bọc middleware cho cả group:   
    Route::middleware(['permission:products.print'])->group(function () {
        Route::get('/production/barcode-generator-excel', BarcodeGeneratorExcel::class)->name('production.barcode-generator-excel');
        Route::get('/production/barcode-generator', BarcodeGenerator::class)->name('production.barcode-generator');
    });
    Route::middleware(['permission:print'])->group(function () {
        Route::get('/print-station/{station_id?}', function ($station_id = '01') {
            return view('production.print-station', compact('station_id'));
        })->name('production.print-station');
    });
    // Các route production khác không dùng chung middleware trên

    Route::middleware(['permission:products.scan'])->group(function () {
        Route::prefix('production')->name('production.')->group(function () {
            Route::get('/scan-mobile', ScanProduct::class)->name('scan'); // Đã gộp tiền tố URL
            Route::get('/excel-manager', ExcelManager::class)->name('excel-manager');
        });
    });

    Route::middleware(['permission:coating.scan'])->group(function () {
        Route::prefix('production')->name('production.')->group(function () {
            Route::get('/coating-confirmation', CoatingConfirmation::class)->name('coating-confirmation');
        });
    });
    // ==========================================
    // 3. NHÓM KHO (Warehouse)
    // ==========================================
    Route::middleware('permission:warehouse.scan')
        ->prefix('warehouse')
        ->name('warehouse.')
        ->group(function () {
            Route::get('/scan-to-location-classic', ScanToLocationClassic::class)->name('scan-to-location-classic');
            Route::get('/scan-to-location', ScanToLocation::class)->name('scan-to-location');
            Route::get('/locations', LocationManager::class)->name('locations');
            Route::get('/reports', ReportManager::class)->name('reports');
            Route::get('/scan', ScanToLocation::class)->name('scan');
            Route::get('/inbound-list', WarehouseInboundList::class)->name('inbound-list');
            Route::get('/dashboard', WarehouseDashboard::class)->name('dashboard');
            Route::get('/movement-log', \App\Livewire\Warehouse\MovementLog::class)->name('movement-log');
        });

    // --- Tính Năng In Ấn Tập Trung (DRY Reprint) ---
    Route::get('/print-labels', [PrintController::class, 'printLabels'])->name('print.labels')->middleware('permission:print');
    Route::get('/print-locations', [PrintController::class, 'printLocations'])->name('locations.print')->middleware('permission:print');
    Route::get('/print-location-codes', [PrintController::class, 'printLocationCodes'])->name('locations.print-code')->middleware('permission:print');
});
