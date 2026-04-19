<?php

namespace App\Livewire\Ai;

use App\Ai\Agents\WarehouseAssistant;
use Livewire\Component;

class AiChat extends Component
{
    public string $message = '';
    public array $messages = [];
    public ?string $conversationId = null;
    public array $conversations = [];
    public bool $isStreaming = false;

    public function mount()
    {
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $this->conversations = \DB::table('agent_conversations')
            ->where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->limit(30)
            ->get(['id', 'updated_at'])
            ->map(fn($c) => [
                'id' => $c->id,
                'updated_at' => \Carbon\Carbon::parse($c->updated_at)->diffForHumans(),
            ])
            ->toArray();
    }

    public function selectConversation(string $conversationId)
    {
        $this->conversationId = $conversationId;
        $this->loadMessages();
    }

    public function newConversation()
    {
        $this->conversationId = null;
        $this->messages = [];
    }

    public function loadMessages()
    {
        if (!$this->conversationId) {
            $this->messages = [];
            return;
        }

        $rows = \DB::table('agent_conversation_messages')
            ->where('conversation_id', $this->conversationId)
            ->orderBy('created_at', 'asc')
            ->get(['role', 'content']);

        $this->messages = $rows->map(fn($m) => [
            'role' => $m->role,
            'content' => $m->content,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.ai.ai-chat')
            ->layout('layouts.app', ['menu' => 'ai']);
    }
}
