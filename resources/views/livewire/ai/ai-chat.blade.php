<div class="container-fluid px-0" style="height: calc(100vh - 60px);">
    <div class="row g-0 h-100">
        {{-- Sidebar danh sách hội thoại --}}
        <div class="col-md-3 col-lg-2 d-none d-md-flex flex-column ai-sidebar" id="ai-sidebar">
            <div class="p-3 ai-sidebar-header">
                <button wire:click="newConversation" class="btn btn-outline-light btn-sm w-100 rounded-pill">
                    <i class="fas fa-plus me-1"></i> Hội thoại mới
                </button>
            </div>
            <div class="flex-grow-1 overflow-auto px-2 py-1">
                @foreach ($conversations as $conv)
                    <div class="ai-conv-item {{ $conversationId === $conv['id'] ? 'active' : '' }}"
                        wire:key="conv-{{ $conv['id'] }}">
                        <div class="ai-conv-content" wire:click="selectConversation('{{ $conv['id'] }}')">
                            <div class="ai-conv-title" title="{{ $conv['title'] }}">
                                {{ Str::limit($conv['title'], 32) }}
                            </div>
                            <div class="ai-conv-time">{{ $conv['updated_at'] }}</div>
                        </div>
                        <button class="ai-conv-delete" wire:click.stop="deleteConversation('{{ $conv['id'] }}')"
                            title="Xóa hội thoại">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @endforeach
                @if (empty($conversations))
                    <p class="text-muted small text-center mt-4 opacity-50">
                        <i class="fas fa-comments fa-2x d-block mb-2"></i>
                        Chưa có hội thoại nào
                    </p>
                @endif
            </div>
        </div>

        {{-- Khu vực chat chính --}}
        <div class="col-md-9 col-lg-10 d-flex flex-column ai-chat-main">
            {{-- Header --}}
            <div class="ai-chat-header">
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary d-md-none me-2" data-bs-toggle="offcanvas"
                        data-bs-target="#mobileChatSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="ai-header-icon me-2">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Trợ lý AI Sản xuất</h6>
                    </div>
                </div>
                <span class="badge ai-status-badge">
                    <span class="ai-status-dot"></span> Gemini
                </span>
            </div>

            {{-- Messages area --}}
            <div class="flex-grow-1 overflow-auto px-3 px-md-5 py-4" id="chat-messages"
                style="scroll-behavior: smooth;">

                @if (empty($messages) && !$conversationId)
                    {{-- Welcome screen --}}
                    <div class="ai-welcome">
                        <div class="ai-welcome-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4 class="ai-welcome-title">Xin chào! Tôi là Trợ lý AI Sản xuất</h4>
                        <p class="ai-welcome-sub">Hỏi tôi về tồn kho, sản xuất, truy vết nguồn gốc cuộn vải...</p>
                        <div class="row justify-content-center g-2 mt-2">
                            <div class="col-auto">
                                <button class="ai-suggestion-card" data-msg="Thống kê tổng quan kho hàng">
                                    <span class="ai-suggestion-icon">📊</span>
                                    <span>Tổng quan kho</span>
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="ai-suggestion-card" data-msg="Báo cáo sản xuất 7 ngày gần đây">
                                    <span class="ai-suggestion-icon">📈</span>
                                    <span>Báo cáo SX</span>
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="ai-suggestion-card" data-msg="Liệt kê các Bộ phận sản xuất">
                                    <span class="ai-suggestion-icon">🏭</span>
                                    <span>Bộ phận</span>
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="ai-suggestion-card" data-msg="Tìm cuộn vải đã nhập kho gần đây">
                                    <span class="ai-suggestion-icon">🔍</span>
                                    <span>Tìm cuộn vải</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Rendered messages --}}
                @foreach ($messages as $msg)
                    @if ($msg['role'] === 'user')
                        <div class="mb-3 d-flex justify-content-end">
                            <div class="ai-bubble ai-bubble-user">
                                {{ $msg['content'] }}
                            </div>
                        </div>
                    @elseif($msg['role'] === 'assistant')
                        <div class="mb-3 d-flex justify-content-start">
                            <div class="ai-bubble ai-bubble-assistant">
                                <div class="ai-markdown">{!! \Illuminate\Support\Str::markdown($msg['content']) !!}</div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Streaming response placeholder --}}
                <div id="streaming-container" class="mb-3 d-flex justify-content-start"
                    style="display:none !important;">
                    <div class="ai-bubble ai-bubble-assistant">
                        <div id="streaming-content" class="ai-markdown">
                            <span class="typing-indicator">
                                <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input area --}}
            <div class="ai-input-area">
                <form id="chat-form" class="d-flex gap-2 align-items-end">
                    @csrf
                    <div class="flex-grow-1 position-relative">
                        <textarea id="chat-input" class="ai-input" rows="1" placeholder="Nhập câu hỏi..." maxlength="2000"></textarea>
                    </div>
                    <button type="submit" id="send-btn" class="ai-send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                <div class="text-center mt-1">
                    <small class="text-muted" style="font-size: 0.72rem;">
                        <i class="fas fa-info-circle text-info"></i> Hệ thống xử lý từng câu hỏi <strong>độc lập</strong> (không dùng trí nhớ nhân tạo). Vui lòng nêu rõ cụ thể yêu cầu (mã cuộn vải, thời gian) trong mỗi câu.
                    </small>
                </div>
                <div class="text-center">
                    <small class="text-muted" style="font-size: 0.65rem; opacity: 0.7;">AI có thể mắc lỗi. Vui lòng tự kiểm tra lại số liệu quan trọng.</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile sidebar offcanvas --}}
    <div class="offcanvas offcanvas-start ai-sidebar" tabindex="-1" id="mobileChatSidebar" style="width:280px;">
        <div class="offcanvas-header ai-sidebar-header">
            <h6 class="offcanvas-title">Lịch sử chat</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body px-2 py-1">
            <button wire:click="newConversation" class="btn btn-outline-light btn-sm w-100 rounded-pill mb-3"
                data-bs-dismiss="offcanvas">
                <i class="fas fa-plus me-1"></i> Hội thoại mới
            </button>
            @foreach ($conversations as $conv)
                <div class="ai-conv-item {{ $conversationId === $conv['id'] ? 'active' : '' }}"
                    wire:key="mobile-conv-{{ $conv['id'] }}">
                    <div class="ai-conv-content" wire:click="selectConversation('{{ $conv['id'] }}')"
                        data-bs-dismiss="offcanvas">
                        <div class="ai-conv-title">{{ Str::limit($conv['title'], 28) }}</div>
                        <div class="ai-conv-time">{{ $conv['updated_at'] }}</div>
                    </div>
                    <button class="ai-conv-delete" wire:click.stop="deleteConversation('{{ $conv['id'] }}')"
                        title="Xóa">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    /* ===== SIDEBAR ===== */
    .ai-sidebar {
        background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
        border-right: 1px solid rgba(255, 255, 255, 0.06);
        color: #e0e0e0;
    }

    .ai-sidebar-header {
        padding: 14px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .ai-conv-item {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 10px;
        margin-bottom: 2px;
        border-radius: 8px;
        cursor: pointer;
        transition: background .15s, box-shadow .15s;
        position: relative;
    }

    .ai-conv-item:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .ai-conv-item.active {
        background: linear-gradient(135deg, #0d6efd 0%, #3d8bfd 100%);
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
    }

    .ai-conv-content {
        flex: 1;
        min-width: 0;
        cursor: pointer;
    }

    .ai-conv-title {
        font-size: 0.82rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #f0f0f0;
    }

    .ai-conv-item.active .ai-conv-title {
        color: #fff;
    }

    .ai-conv-time {
        font-size: 0.68rem;
        color: rgba(255, 255, 255, 0.4);
        margin-top: 1px;
    }

    .ai-conv-item.active .ai-conv-time {
        color: rgba(255, 255, 255, 0.7);
    }

    .ai-conv-delete {
        flex-shrink: 0;
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.2);
        font-size: 0.72rem;
        padding: 4px 6px;
        border-radius: 4px;
        opacity: 0;
        transition: opacity .15s, color .15s, background .15s;
        cursor: pointer;
    }

    .ai-conv-item:hover .ai-conv-delete {
        opacity: 1;
    }

    .ai-conv-delete:hover {
        color: #ff6b6b;
        background: rgba(255, 107, 107, 0.12);
    }

    /* ===== CHAT MAIN ===== */
    .ai-chat-main {
        background: var(--bs-body-bg, #fff);
    }

    .ai-chat-header {
        padding: 10px 20px;
        border-bottom: 1px solid var(--bs-border-color, #dee2e6);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bs-tertiary-bg, #f8f9fa);
    }

    .ai-header-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.9rem;
    }

    .ai-status-badge {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .ai-status-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #198754;
        margin-right: 4px;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.4;
        }
    }

    /* ===== WELCOME ===== */
    .ai-welcome {
        text-align: center;
        padding-top: 10vh;
    }

    .ai-welcome-icon {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 2rem;
        margin-bottom: 20px;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
    }

    .ai-welcome-title {
        color: var(--bs-body-color);
        font-weight: 300;
        font-size: 1.4rem;
    }

    .ai-welcome-sub {
        color: var(--bs-secondary-color, #6c757d);
        font-size: 0.9rem;
        margin-bottom: 24px;
    }

    .ai-suggestion-card {
        background: var(--bs-tertiary-bg, #f8f9fa);
        border: 1px solid var(--bs-border-color, #dee2e6);
        border-radius: 12px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: var(--bs-body-color);
        cursor: pointer;
        transition: all .2s;
    }

    .ai-suggestion-card:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.06);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .ai-suggestion-icon {
        font-size: 1.1rem;
    }

    /* ===== BUBBLES ===== */
    .ai-bubble {
        max-width: 85%;
        padding: 10px 16px;
        border-radius: 16px;
        word-break: break-word;
        white-space: pre-wrap;
        animation: fadeInMsg .25s ease;
    }

    @keyframes fadeInMsg {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ai-bubble-user {
        background: linear-gradient(135deg, #0d6efd, #3d8bfd);
        color: #fff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.2);
    }

    .ai-bubble-assistant {
        background: var(--bs-tertiary-bg, #f1f3f5);
        border: 1px solid var(--bs-border-color, #dee2e6);
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    }

    /* ===== INPUT ===== */
    .ai-input-area {
        border-top: 1px solid var(--bs-border-color, #dee2e6);
        padding: 12px 20px;
        background: var(--bs-body-bg, #fff);
    }

    @media (min-width: 768px) {
        .ai-input-area {
            padding: 12px 15%;
        }
    }

    .ai-input {
        width: 100%;
        border: 2px solid var(--bs-border-color, #dee2e6);
        border-radius: 14px;
        padding: 10px 16px;
        resize: none;
        max-height: 120px;
        font-size: 0.9rem;
        background: var(--bs-body-bg, #fff);
        color: var(--bs-body-color);
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }

    .ai-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }

    .ai-send-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        cursor: pointer;
        transition: transform .15s, box-shadow .15s;
    }

    .ai-send-btn:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .ai-send-btn:disabled {
        opacity: 0.5;
        transform: none;
    }

    /* ===== TYPING ===== */
    .typing-indicator {
        display: inline-flex;
        gap: 4px;
        align-items: center;
        padding: 4px 0;
    }

    .typing-indicator .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #6c757d;
        animation: typing 1.4s infinite ease-in-out;
    }

    .typing-indicator .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {

        0%,
        80%,
        100% {
            transform: scale(0.6);
            opacity: 0.4;
        }

        40% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* ===== MARKDOWN ===== */
    .ai-markdown table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0;
        font-size: 0.85rem;
    }

    .ai-markdown th,
    .ai-markdown td {
        border: 1px solid var(--bs-border-color, #dee2e6);
        padding: 6px 10px;
        text-align: left;
    }

    .ai-markdown th {
        background: var(--bs-tertiary-bg, #f8f9fa);
        font-weight: 600;
    }

    .ai-markdown pre {
        background: #1e1e2e;
        color: #cdd6f4;
        padding: 12px;
        border-radius: 8px;
        overflow-x: auto;
        font-size: 0.82rem;
    }

    .ai-markdown code {
        background: rgba(0, 0, 0, 0.06);
        padding: 1px 5px;
        border-radius: 4px;
        font-size: 0.85em;
    }

    .ai-markdown pre code {
        background: none;
        padding: 0;
        color: inherit;
    }

    .ai-markdown p {
        margin-bottom: 0.5rem;
    }

    .ai-markdown ul,
    .ai-markdown ol {
        padding-left: 1.2rem;
        margin-bottom: 0.5rem;
    }

    /* ===== SCROLLBAR ===== */
    #chat-messages::-webkit-scrollbar {
        width: 5px;
    }

    #chat-messages::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 3px;
    }

    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }

    .ai-sidebar ::-webkit-scrollbar {
        width: 4px;
    }

    .ai-sidebar ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 2px;
    }
</style>

@script
    <script>
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const streamingContainer = document.getElementById('streaming-container');
        const streamingContent = document.getElementById('streaming-content');
        const sendBtn = document.getElementById('send-btn');
        let conversationId = @json($conversationId);
        let isStreaming = false;

        // Auto-resize textarea
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Enter to send (Shift+Enter for newline)
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        // Suggestion buttons
        document.querySelectorAll('.ai-suggestion-card').forEach(btn => {
            btn.addEventListener('click', function() {
                chatInput.value = this.dataset.msg;
                chatForm.dispatchEvent(new Event('submit'));
            });
        });

        // Send message
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (!message || isStreaming) return;

            isStreaming = true;
            sendBtn.disabled = true;
            chatInput.value = '';
            chatInput.style.height = 'auto';

            // Hide welcome screen
            const welcome = document.querySelector('.ai-welcome');
            if (welcome) welcome.style.display = 'none';

            // Add user message
            appendMessage('user', message);

            // Show streaming indicator
            streamingContainer.style.display = 'flex';
            streamingContainer.style.setProperty('display', 'flex', 'important');
            streamingContent.innerHTML =
                '<span class="typing-indicator"><span class="dot"></span><span class="dot"></span><span class="dot"></span></span>';
            scrollToBottom();

            try {
                const response = await fetch('/ai/stream', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                            '{{ csrf_token() }}',
                        'Accept': 'text/event-stream',
                    },
                    body: JSON.stringify({
                        message: message,
                        conversation_id: conversationId,
                    }),
                });

                if (!response.ok) {
                    throw new Error(`Server error: ${response.status}`);
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let fullText = '';
                let buffer = '';

                while (true) {
                    const {
                        done,
                        value
                    } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, {
                        stream: true
                    });
                    const lines = buffer.split('\n');
                    buffer = lines.pop(); // Keep incomplete line in buffer

                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            const data = line.substring(6);
                            if (data === '[DONE]') continue;

                            try {
                                const parsed = JSON.parse(data);
                                if (parsed.delta) {
                                    fullText += parsed.delta;
                                    streamingContent.innerHTML = markdownToHtml(fullText);
                                    scrollToBottom();
                                }
                                if (parsed.conversationId) {
                                    conversationId = parsed.conversationId;
                                }
                                if (parsed.error) {
                                    throw new Error(parsed.error);
                                }
                            } catch (err) {
                                // Nếu là event error JSON mà ta throw, thì throw tiếp ra stream
                                if (err.message && (err.message.includes('Hệ thống AI') || err.message.includes(
                                        'Lỗi kết nối AI'))) {
                                    throw err;
                                }

                                // Some events may not be JSON, just accumulate text
                                if (data.trim() && data !== '[DONE]') {
                                    fullText += data;
                                    streamingContent.innerHTML = markdownToHtml(fullText);
                                    scrollToBottom();
                                }
                            }
                        }
                    }
                }

                // Finalize: hide streaming, add permanent message
                streamingContainer.style.setProperty('display', 'none', 'important');
                if (fullText) {
                    appendMessage('assistant', fullText, true);
                }

                // Atomic sync: set conversationId + load messages + refresh sidebar in ONE Livewire call
                // This prevents intermediate re-renders with empty data that would wipe the chat
                await $wire.call('syncAfterStream', conversationId);

            } catch (error) {
                console.error('Stream error:', error);
                streamingContainer.style.setProperty('display', 'none', 'important');

                let errorMsg = '❌ Đã xảy ra lỗi khi kết nối AI. Vui lòng thử lại.';
                if (error.message && (error.message.includes('Hệ thống AI') || error.message.includes(
                        'Lỗi kết nối AI'))) {
                    errorMsg = error.message;
                }

                appendMessage('assistant', errorMsg, true);
            }

            isStreaming = false;
            sendBtn.disabled = false;
            chatInput.focus();
        });

        function appendMessage(role, content, isMarkdown = false) {
            const wrapper = document.createElement('div');
            wrapper.className = `mb-3 d-flex ${role === 'user' ? 'justify-content-end' : 'justify-content-start'}`;

            const bubble = document.createElement('div');
            bubble.className = `ai-bubble ${role === 'user' ? 'ai-bubble-user' : 'ai-bubble-assistant'}`;

            if (isMarkdown && role !== 'user') {
                bubble.innerHTML = '<div class="ai-markdown">' + markdownToHtml(content) + '</div>';
            } else {
                bubble.textContent = content;
            }

            wrapper.appendChild(bubble);
            // Insert before streaming container
            chatMessages.insertBefore(wrapper, streamingContainer);
            scrollToBottom();
        }

        function scrollToBottom() {
            requestAnimationFrame(() => {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        }

        function markdownToHtml(text) {
            // Simple markdown: bold, italic, code blocks, tables, lists
            let html = text
                .replace(/```(\w*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>')
                .replace(/`([^`]+)`/g, '<code>$1</code>')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/^### (.+)$/gm, '<h6 class="fw-bold mt-2">$1</h6>')
            .replace(/^## (.+)$/gm, '<h5 class="fw-bold mt-2">$1</h5>')
            .replace(/^# (.+)$/gm, '<h4 class="fw-bold mt-2">$1</h4>')
            .replace(/^[-*] (.+)$/gm, '<li>$1</li>')
            .replace(/(<li>.*<\/li>\n?)+/g, '<ul>$&</ul>')
            .replace(/^\d+\. (.+)$/gm, '<li>$1</li>');

        // Tables: detect | lines
        const tableRegex = /(\|.+\|[\r\n]+\|[-| :]+\|[\r\n]+((\|.+\|[\r\n]*)+))/g;
        html = html.replace(tableRegex, (match) => {
            const rows = match.trim().split('\n').filter(r => r.trim());
            if (rows.length < 2) return match;

            const headerCells = rows[0].split('|').filter(c => c.trim());
            let table = '<table><thead><tr>';
            headerCells.forEach(c => table += `<th>${c.trim()}</th>`);
            table += '</tr></thead><tbody>';

            for (let i = 2; i < rows.length; i++) {
                const cells = rows[i].split('|').filter(c => c.trim());
                table += '<tr>';
                cells.forEach(c => table += `<td>${c.trim()}</td>`);
                    table += '</tr>';
                }
                table += '</tbody></table>';
                return table;
            });

            // Convert remaining newlines to <br>
            html = html.replace(/\n/g, '<br>');

            return html;
        }

        // Keep JS conversationId in sync with Livewire state
        // (e.g., when clicking 'Hội thoại mới' or selecting a conversation)
        $wire.$watch('conversationId', (value) => {
            conversationId = value;
            requestAnimationFrame(() => scrollToBottom());
        });

        // Listen for Livewire updates
        Livewire.on('conversationLoaded', () => {
            scrollToBottom();
        });
    </script>
@endscript
