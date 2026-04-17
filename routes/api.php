<?php

use App\Http\Controllers\Api\QrCodeScanController;
use App\Http\Controllers\Api\ScaleApiController;
use App\Http\Controllers\Print\PrintAppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; // Import AuthController
use Illuminate\Support\Facades\Broadcast;
/*
|--------------------------------------------------------------------------
| API Routes s
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route để nhận dữ liệu JSON từ QR code

// Route tùy chọn để xem dữ liệu quét (ví dụ để kiểm tra)
// Route::get('/qrscan/{qr_code_id}', [QrCodeScanController::class, 'show']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route cho đăng nhập (không yêu cầu xác thực token)
Route::post('/login', [AuthController::class, 'login']);
Broadcast::routes(['middleware' => ['auth:sanctum']]);
// Các route yêu cầu xác thực bằng Sanctum token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logoutall', [AuthController::class, 'logoutAll']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/register', [AuthController::class, 'register']);

    // ============================================================
    // API Trạm Cân (Scale Station) — Dành cho C# Client
    // Xác thực bằng Header "Authorization: Bearer <token>"
    // Vẫn kết hợp xác thực station_token trong payload.
    // ============================================================
    Route::post('/scale/broadcast-weight', [ScaleApiController::class, 'broadcastWeight']);
    Route::post('/warehouse/update-weight', [ScaleApiController::class, 'updateWeight']);

    // ============================================================
    // API Trạm In (Print Station) — Dành cho C# Client
    // Xác thực bằng Header "Authorization: Bearer <token>"
    // ============================================================
    Route::prefix('print')->group(function () {
        Route::get('/config', [PrintAppController::class, 'getSocketConfig']);
        Route::get('/pending-jobs/{station_token}', [PrintAppController::class, 'pendingJobs']);
        Route::post('/statusupdate', [PrintAppController::class, 'receiveStatus']);
    });
});
