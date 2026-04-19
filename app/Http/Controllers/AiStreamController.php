<?php

namespace App\Http\Controllers;

use App\Ai\Agents\WarehouseAssistant;
use Illuminate\Http\Request;

class AiStreamController extends Controller
{
    public function stream(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|string',
        ]);

        $agent = new WarehouseAssistant;

        if ($conversationId = $request->input('conversation_id')) {
            $agent = $agent->continue($conversationId, as: $request->user());
        } else {
            $agent = $agent->forUser($request->user());
        }

        return response()->stream(function () use ($agent, $request) {
            try {
                $stream = $agent->stream($request->input('message'));

                foreach ($stream as $event) {
                    // Trả về delta dạng JSON thô (laravel/ai đã tự serialize object event thành json)
                    echo "data: " . ((string) $event) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }

                if ($agent->currentConversation()) {
                    echo "data: " . json_encode(['conversationId' => $agent->currentConversation()]) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }

            } catch (\Laravel\Ai\Exceptions\RateLimitedException $e) {
                $msg = '⚠️ Hệ thống AI (Gemini) đang quá tải giới hạn API. Vui lòng chờ 1 phút rồi đặt câu hỏi tiếp theo.';
                echo "data: " . json_encode(['error' => $msg]) . "\n\n";
            } catch (\Exception $e) {
                echo "data: " . json_encode(['error' => 'Lỗi kết nối AI: ' . $e->getMessage()]) . "\n\n";
            }

            echo "data: [DONE]\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();
            
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }
}
