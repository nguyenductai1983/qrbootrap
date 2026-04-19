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

        return $agent->stream($request->input('message'));
    }
}
