<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScaleStation;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Events\ScaleWeightUpdated;
use App\Enums\MovementAction;
use App\Services\ItemWeightService;

class ScaleApiController extends Controller
{
    /**
     * C# App gọi API này để broadcast trọng lượng từ cân lên WebSocket.
     * Điện thoại (Web) sẽ lắng nghe event qua Echo để hiển thị real-time.
     *
     * POST /api/scale/broadcast-weight
     */
    public function broadcastWeight(Request $request)
    {
        $request->validate([
            'station_token' => 'required|string',
            'weight'        => 'required|numeric|min:0',
            'is_stable'     => 'sometimes|boolean',
        ]);

        $station = ScaleStation::where('station_token', $request->station_token)
            ->where('status', true)
            ->first();

        if (!$station) {
            return response()->json([
                'success' => false,
                'message' => 'Trạm cân không hợp lệ hoặc đã bị vô hiệu hóa.',
            ], 401);
        }

        // Broadcast event qua Reverb cho Web Mobile lắng nghe
        event(new ScaleWeightUpdated(
            $station->code,
            (float) $request->weight,
            (bool) ($request->is_stable ?? false)
        ));

        return response()->json([
            'success' => true,
            'message' => 'Đã phát sóng trọng lượng.',
            'station_code' => $station->code,
            'weight'       => (float) $request->weight,
        ]);
    }

    /**
     * C# App hoặc Web gọi API này để chốt ghi nhận trọng lượng cho 1 Item.
     * Dự phòng cho trường hợp thao tác trực tiếp trên máy tính (không qua Livewire).
     *
     * POST /api/warehouse/update-weight
     */
    public function updateWeight(Request $request)
    {
        $request->validate([
            'station_token' => 'required|string',
            'barcode'       => 'required|string',
            'weight'        => 'required|numeric|min:0',
        ]);

        $station = ScaleStation::where('station_token', $request->station_token)
            ->where('status', true)
            ->first();

        if (!$station) {
            return response()->json([
                'success' => false,
                'message' => 'Trạm cân không hợp lệ hoặc đã bị vô hiệu hóa.',
            ], 401);
        }

        $item = Item::where('code', trim($request->barcode))->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy mã '{$request->barcode}' trong hệ thống.",
            ], 404);
        }

        $newWeight = (float) $request->weight;
        $userId = $request->user() ? $request->user()->id : null;

        // Xử lý cập nhật thông qua Service dùng chung
        $service = new ItemWeightService();
        $result = $service->updateWeight($item, $newWeight, $userId);

        return response()->json([
            'success'    => true,
            'message'    => $result['is_surplus'] ? 'Đã ghi nhận tái nhập dư.' : 'Đã cập nhật trọng lượng.',
            'item_code'  => $item->code,
            'old_weight' => $result['old_weight'],
            'new_weight' => $result['new_weight'],
            'is_surplus' => $result['is_surplus'],
        ]);
    }
}
