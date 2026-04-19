<?php

namespace App\Livewire\Ai;

use App\Ai\Agents\WarehouseAssistant;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['menu' => 'ai'])]
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
        $this->conversations = DB::table('agent_conversations')
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->limit(30)
            ->get(['id', 'title', 'updated_at'])
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title ?: 'Hội thoại mới',
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

    public function deleteConversation(string $conversationId)
    {
        DB::table('agent_conversation_messages')
            ->where('conversation_id', $conversationId)
            ->delete();

        DB::table('agent_conversations')
            ->where('id', $conversationId)
            ->where('user_id', Auth::id())
            ->delete();

        if ($this->conversationId === $conversationId) {
            $this->conversationId = null;
            $this->messages = [];
        }

        $this->loadConversations();
    }

    public function loadMessages()
    {
        if (!$this->conversationId) {
            $this->messages = [];
            return;
        }

        $rows = DB::table('agent_conversation_messages')
            ->where('conversation_id', $this->conversationId)
            ->orderBy('created_at', 'asc')
            ->get(['role', 'content']);

        $this->messages = $rows->map(fn($m) => [
            'role' => $m->role,
            'content' => $m->content,
        ])->toArray();
    }

    /**
     * Atomic sync after streaming completes.
     * Sets conversationId, loads messages, and refreshes sidebar in ONE Livewire round-trip,
     * so there's no intermediate re-render with empty data.
     */
    public function syncAfterStream(?string $newConversationId)
    {
        if ($newConversationId) {
            $this->conversationId = $newConversationId;
        } elseif (!$this->conversationId) {
            // Stream didn't provide conversationId — find the latest one created by this user
            $latest = DB::table('agent_conversations')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->first(['id']);

            if ($latest) {
                $this->conversationId = $latest->id;
            }
        }

        $this->loadMessages();
        $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.ai.ai-chat');
    }
}
