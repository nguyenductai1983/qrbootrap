<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Để tạo ID ngẫu nhiên nếu cần
use App\Models\QrScan;
use Illuminate\Support\Facades\Log; // Để ghi log thông tin

class QrCodeScanController extends Controller
{

    /**
     * Nhận chuỗi JSON từ QR code và lưu vào cơ sở dữ liệu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();
        // 1. Xác thực dữ liệu đầu vào
        //'{"qr_code":"{\"token\":\"bc437330-4ba6-4827-9d18-6793ccdec9a7\",\"platform\":\"windows\",\"browser\":\"Google+Chrome\",\"browser_version\":\"136.0.0.0\",\"type\":\"web_login\"}"}'
        //'{"ID":"1","KHO":"950","PO":"F5300922-TBC","MA VAI":"CMWESX950XXXXXXX","MA CAY VAI":"H3005L","VI TRI CAY VAI":"A7","LOG VAI":"","DA KiEM ":"","KK 30/5/2023":"TON","KiEM TON ":"23/3/24-OANH","Mau":"WE","LOAI VAI":"S","KHO TRANG THUC TE":"950","LOAI PP/PE":"PE","MAY DET":"C01-16","MAT DO":"","SO LO NHUA":"T06","DINH LUONG":"85.2","TEN DON HANG":"DAN TAP","GHI CHU":"HE 62G, PE TRONG","NGAY DU KiEN SU DUNG VAI DET MOI":"44788","NGAY DU KiEN SU DUNG VAI HOAN KHO":"N/A","DON HANG KET THUC":"DHKT","NHOM":"2","MA KH":"VDG","CHAT LUONG":"DAT","SO MET":"1670","TRONG LUONG ONG ":"13.46","TRONG LUONG":"168","TRONG LUONG":"154.54","SO MET KT LAI":"1909.31554237707","Chenh lech":"0.143302719986269","NGUOI NHAN":"THAO","NGAY NHAP":"44804","CA NHAP":"2","NGUOI GIAO":"THUY","THU KHO NHAP":"TRANG","BO PHAN":"TRANG","GHI CHU VAI HK":"","HAN SU DUNG":"45169","SO NGAY TON":"988","NGAY XUAT":"","CA XUAT":"","THU KHO XUAT":"","NGUOI NHAN":"","BO PHAN NHAN":"","DON HANG XUAT":"","SO PHIEU XUAT":""}'
        $validator = Validator::make($request->all(), [
            // 'qr_code_id' => 'required|string|max:255|unique:qr_scans,qr_code_id', // Đảm bảo ID là duy nhất
            'data' => 'required', // Yêu cầu trường 'data' phải là JSON hợp lệ
            // 'other_field' => 'nullable|string', // Thêm các trường khác nếu có
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Xác thực không thành công.',
                'errors' => $validator->errors()
            ], 422); // Mã lỗi 422 Unprocessable Entity
        }

        // 2. Lấy dữ liệu đã xác thực
        $validatedData = $validator->validated();

        try {
            Log::info($validatedData['data']);
            // 3. Lưu dữ liệu vào cơ sở dữ liệu
            $dataArray = $validatedData['data'];
            if (is_string($dataArray)) {
                // Nếu dữ liệu là chuỗi, cố gắng giải mã nó
                $decodedData = json_decode($dataArray, true);

                // Kiểm tra xem việc giải mã có thành công và kết quả có phải là một mảng không
                // json_last_error() giúp kiểm tra lỗi khi decode JSON
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
                    $dataArray = $decodedData;
                } else {
                    // Xử lý trường hợp chuỗi không phải là JSON hợp lệ hoặc không phải là JSON array
                    // Ví dụ: coi nó là một mảng rỗng hoặc log lỗi
                    $dataArray = []; // Hoặc giữ nguyên $dataArray nếu bạn muốn
                }
            }
            //   kiêm tra trùng lặp
            $existingQrScan = QrScan::where('qr_code', $dataArray["MA VAI"] ?? null)->first();
            if ($existingQrScan) {
                $existingQrScan->update([
                    'user_id' => $user ? $user->id : null, // Lưu ID người dùng nếu đã đăng nhập
                    'qr_code' => isset($dataArray["MA VAI"]) ? $dataArray["MA VAI"] : Str::uuid(), // Tạo ID ngẫu nhiên nếu không có
                    'data' =>  $dataArray, // Dữ liệu JSON từ QR code
                    'scanner_ip' => $request->ip(), // Lấy IP của người gửi request
                    'user_agent' => $request->header('User-Agent'), // Lấy User Agent
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'QR Code cập nhật thành công.',
                     'id' => $existingQrScan->id,
                    'uuid' => $existingQrScan->qr_code
                ], 201);
            } else {
                // Nếu không có bản ghi nào trùng lặp, tạo mới

                $qrScan = QrScan::create([
                    'user_id' => $user ? $user->id : null, // Lưu ID người dùng nếu đã đăng nhập
                    'qr_code' => isset($dataArray["MA VAI"]) ? $dataArray["MA VAI"] : Str::uuid(), // Tạo ID ngẫu nhiên nếu không có
                    'data' =>  $dataArray, // Dữ liệu JSON từ QR code
                    'scanner_ip' => $request->ip(), // Lấy IP của người gửi request
                    'user_agent' => $request->header('User-Agent'), // Lấy User Agent
                ]);

                // 4. Trả về phản hồi thành công
                return response()->json([
                    'status' => 'success',
                    'message' => 'QR code lưu thành công!',
                    'id' => $qrScan->id,
                    'uuid' => $qrScan->qr_code
                ], 201); // Mã 201 Created
            }
        } catch (\Illuminate\Database\QueryException $e) {
            log::error('Lỗi cơ sở dữ liệu: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            // Xử lý lỗi trùng lặp (ví dụ nếu qr_code_id đã tồn tại dù đã unique ở validation)
            if ($e->getCode() == 23000) { // Mã lỗi SQL cho vi phạm unique constraint
                return response()->json([
                    'message' => 'QR Code ID đã tồn tại.',
                    'error_code' => 'DUPLICATE_QR_ID'
                ], 409); // Mã 409 Conflict
            }
            // Xử lý các lỗi database khác
            return response()->json([
                'message' => 'Lưu dữ liệu không thành công.',
                'error' => $e->getMessage()
            ], 500); // Mã 500 Internal Server Error
        } catch (\Exception $e) {
            log::error('Lỗi không mong muốn: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Xử lý các lỗi chung khác
            return response()->json([
                'message' => 'Đã xảy ra lỗi không mong muốn.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin quét QR code theo ID (tùy chọn để kiểm tra)
     */
    public function show($qr_code)
    {
        $qrScan = QrScan::where('qr_code', $qr_code)->first();

        if (!$qrScan) {
            return response()->json(['message' => 'QR Code scan not found.'], 404);
        }

        return response()->json($qrScan);
    }
}
