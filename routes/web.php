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
use App\Livewire\Warehouse\ReportManager;
use App\Livewire\Warehouse\WarehouseInboundList;
use App\Livewire\Warehouse\WarehouseDashboard;
use App\Livewire\Dashboard\AnalyticsDashboard;
use App\Http\Controllers\Print\PrintController;
//Role::withoutGlobalScopes()->get(); // Lấy tất cả vai trò mà không áp dụng bất kỳ global scope nào
Route::view('/', 'welcome');
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
Route::view('/guide', 'guide')->middleware(['auth'])->name('guide');
require __DIR__ . '/auth.php';
Route::get('/test-500', function () {
    abort(500, 'đang thử lỗi 500'); // Lệnh ép hệ thống quăng lỗi 500
});
// File: routes/web.php

Route::get('/test-print', function () {
    $data = [
        'Path' => 'Mau01', // Đảm bảo đường dẫn này có thật trên máy C#
        'Data' => [
            'MaSP' => 'H053HFA1 WE S 1165 PP 208 2009 001',
            'TenSP' => 'Sản phẩm 01'
        ]
    ];

    // Truyền đúng Key mà C# đang nghe
    event(new \App\Events\PrintLabelEvent('station_001_secret', $data));

    return 'Đã bắn lệnh in tới trạm station_001_secret!';
});

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::post('/printstatusvb', function (Request $request) {
    // 1. Lấy toàn bộ dữ liệu C# gửi lên
    $data = $request->all();

    // 2. GHI VÀO LOG ĐỂ DEBUG
    Log::info('--- NHẬN THÔNG TIN TỪ MÁY IN C# ---');
    Log::info('Dữ liệu thô:', $data);

    // Bạn có thể xử lý logic tại đây (ví dụ: cập nhật database)
    // $order = Order::where('code', $data['order_id'])->update(['printed' => true]);

    return response()->json([
        'success' => true,
        'message' => 'Laravel đã nhận được thông tin!'
    ]);
});
Route::get('/print-stationvb/{key}', function ($key) {
    // if ($key !== config('app.print_station_key')) {
    if ($key !== 'your_reverb_key') {
        abort(403); // Từ chối nếu Key sai
    }
    // Logic kết nối WebSocket ở đây...
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');
    // Trang Báo Cáo & Phân Tích — có quyền xem bất kỳ loại nào
    Route::get('/analytics', AnalyticsDashboard::class)->name('analytics');

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
    });

    // Tách riêng quản lý Items để các bộ phận khác (có quyền items.view) truy cập được
    Route::middleware(['permission:items.view'])->prefix('manager')->group(function () {
        Route::get('/items', ItemManager::class)->name('manager.items');
    });

    // ==========================================
    // 2. NHÓM PRODUCTION (Sản xuất)
    // ==========================================
    // Ví dụ: Bọc tất cả các route in tem, quét tem vào chung 1 quyền "manage production"
    // Nếu các route này dùng chung 1 quyền, đây là cách bạn bọc middleware cho cả group:   
    Route::middleware(['permission:products.print'])->group(function () {
        Route::get('/production/barcode-generator-excel', BarcodeGeneratorExcel::class)->name('production.barcode-generator-excel');
        Route::get('/production/barcode-generator', BarcodeGenerator::class)->name('production.barcode-generator');
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
            Route::get('/scan-to-location', ScanToLocation::class)->name('scan-to-location');
            Route::get('/locations', LocationManager::class)->name('locations');
            Route::get('/reports', ReportManager::class)->name('reports');
            Route::get('/scan', ScanToLocation::class)->name('scan');
            Route::get('/inbound-list', WarehouseInboundList::class)->name('inbound-list');
            Route::get('/dashboard', WarehouseDashboard::class)->name('dashboard');
        });

    // --- Tính Năng In Ấn Tập Trung (DRY Reprint) ---
    Route::get('/print-labels', [PrintController::class, 'printLabels'])->name('print.labels')->middleware('permission:print');
    Route::get('/print-locations', [PrintController::class, 'printLocations'])->name('locations.print')->middleware('permission:print');
    Route::get('/print-location-codes', [PrintController::class, 'printLocationCodes'])->name('locations.print-code')->middleware('permission:print');
});
