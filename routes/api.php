<?php

use App\Http\Controllers\Api\QrCodeScanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; // Import AuthController
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route để nhận dữ liệu JSON từ QR code


// Route tùy chọn để xem dữ liệu quét (ví dụ để kiểm tra)
Route::get('/qrscan/{qr_code_id}', [QrCodeScanController::class, 'show']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route cho đăng nhập (không yêu cầu xác thực token)
Route::post('/login', [AuthController::class, 'login']);

// Các route yêu cầu xác thực bằng Sanctum token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logoutall', [AuthController::class, 'logoutAll']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/qrscan', [QrCodeScanController::class, 'store']);
    // Thêm các API route bảo mật khác của bạn ở đây
    // Route::get('/protected-resource', function (Request $request) {
    //     return response()->json(['message' => 'Đây là tài nguyên bảo mật', 'user' => $request->user()]);
    // });
});
