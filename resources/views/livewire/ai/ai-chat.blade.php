<div class="container-fluid px-0" style="height: calc(100vh - 60px);">
    <div class="row g-0 h-100">
        {{-- Sidebar danh sách hội thoại --}}
        <div class="col-md-3 col-lg-2 border-end bg-dark d-none d-md-flex flex-column" id="ai-sidebar">
            <div class="p-3 border-bottom border-secondary">
                <button wire:click="newConversation" class="btn btn-outline-light btn-sm w-100">
                    <i class="fas fa-plus me-1"></i> Hội thoại mới
                </button>
            </div>
            <div class="flex-grow-1 overflow-auto p-2">
                @foreach ($conversations as $conv)
                    <div wire:click="selectConversation('{{ $conv['id'] }}')"
                        class="px-3 py-2 rounded mb-1 text-white small cursor-pointer
                            {{ $conversationId === $conv['id'] ? 'bg-primary' : 'hover-bg-secondary' }}"
                        style="cursor:pointer; transition: background .15s;"
                        onmouseover="if(!this.classList.contains('bg-primary')) this.style.background='#343a40'"
                        onmouseout="if(!this.classList.contains('bg-primary')) this.style.background='transparent'">
                        <i class="fas fa-comment-dots me-1 opacity-50"></i>
                        {{ $conv['updated_at'] }}
                    </div>
                @endforeach
                @if (empty($conversations))
                    <p class="text-muted small text-center mt-3">Chưa có hội thoại nào</p>
                @endif
            </div>
        </div>

        {{-- Khu vực chat chính --}}
        <div class="col-md-9 col-lg-10 d-flex flex-column bg-white">
            {{-- Header --}}
            <div class="px-4 py-2 border-bottom d-flex align-items-center justify-content-between bg-light">
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary d-md-none me-2" data-bs-toggle="offcanvas"
                        data-bs-target="#mobileChatSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <i class="fas fa-robot text-primary me-2 fs-5"></i>
                    <h6 class="mb-0 fw-bold">Trợ lý AI Kho Hàng</h6>
                </div>
                <span class="badge bg-success-subtle text-success">
                    <i class="fas fa-circle fa-xs me-1"></i>Gemini
                </span>
            </div>

            {{-- Messages area --}}
            <div class="flex-grow-1 overflow-auto px-3 px-md-5 py-4" id="chat-messages"
                style="scroll-behavior: smooth;">

                @if (empty($messages) && !$conversationId)
                    {{-- Welcome screen --}}
                    <div class="text-center mt-5 pt-5">
                        <div class="mb-4">
                            <i class="fas fa-robot text-primary" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                        <h4 class="text-muted fw-light">Xin chào! Tôi là Trợ lý AI Kho Hàng</h4>
                        <p class="text-muted small mb-4">Hỏi tôi về tồn kho, sản xuất, truy vết nguồn gốc cuộn
                            vải...</p>
                        <div class="row justify-content-center g-2">
                            <div class="col-auto">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 ai-suggestion"
                                    data-msg="Thống kê tổng quan kho hàng">
                                    📊 Tổng quan kho
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 ai-suggestion"
                                    data-msg="Báo cáo sản xuất 7 ngày gần đây">
                                    📈 Báo cáo SX
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 ai-suggestion"
                                    data-msg="Liệt kê các phòng ban sản xuất">
                                    🏭 Phòng ban
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3 ai-suggestion"
                                    data-msg="Tìm cuộn vải đã nhập kho gần đây">
                                    🔍 Tìm cuộn vải
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Rendered messages --}}
                @foreach ($messages as $msg)
                    <div class="mb-3 d-flex {{ $msg['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="{{ $msg['role'] === 'user' ? 'bg-primary text-white' : 'bg-light border' }} rounded-3 px-3 py-2"
                            style="max-width: 85%; white-space: pre-wrap; word-break: break-word;">
                            @if ($msg['role'] !== 'user')
                                <div class="ai-markdown">{!! \Illuminate\Support\Str::markdown($msg['content']) !!}</div>
                            @else
                                {{ $msg['content'] }}
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Streaming response placeholder --}}
                <div id="streaming-container" class="mb-3 d-flex justify-content-start" style="display:none !important;">
                    <div class="bg-light border rounded-3 px-3 py-2" style="max-width: 85%;">
                        <div id="streaming-content" class="ai-markdown">
                            <span class="typing-indicator">
                                <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input area --}}
            <div class="border-top px-3 px-md-5 py-3 bg-white">
                <form id="chat-form" class="d-flex gap-2 align-items-end">
                    @csrf
                    <div class="flex-grow-1">
                        <textarea id="chat-input" class="form-control border-2" rows="1" placeholder="Nhập câu hỏi..."
                            style="resize:none; max-height:120px; border-radius: 12px;" maxlength="2000"></textarea>
                    </div>
                    <button type="submit" id="send-btn" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width:42px; height:42px; flex-shrink:0;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                <div class="text-center mt-1">
                    <small class="text-muted" style="font-size: 0.7rem;">AI có thể mắc lỗi. Vui lòng kiểm tra thông tin quan trọng.</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile sidebar offcanvas --}}
    <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mobileChatSidebar" style="width:260px;">
        <div class="offcanvas-header border-bottom border-secondary">
            <h6 class="offcanvas-title">Lịch sử chat</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-2">
            <button wire:click="newConversation" class="btn btn-outline-light btn-sm w-100 mb-2" data-bs-dismiss="offcanvas">
                <i class="fas fa-plus me-1"></i> Hội thoại mới
            </button>
            @foreach ($conversations as $conv)
                <div wire:click="selectConversation('{{ $conv['id'] }}')"
                    class="px-3 py-2 rounded mb-1 small" style="cursor:pointer;"
                    data-bs-dismiss="offcanvas">
                    <i class="fas fa-comment-dots me-1 opacity-50"></i>
                    {{ $conv['updated_at'] }}
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .typing-indicator { display: inline-flex; gap: 4px; align-items: center; padding: 4px 0; }
    .typing-indicator .dot {
        width: 8px; height: 8px; border-radius: 50%; background: #6c757d;
        animation: typing 1.4s infinite ease-in-out;
    }
    .typing-indicator .dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator .dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
        40% { transform: scale(1); opacity: 1; }
    }
    .ai-markdown table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 0.85rem; }
    .ai-markdown th, .ai-markdown td { border: 1px solid #dee2e6; padding: 6px 10px; text-align: left; }
    .ai-markdown th { background: #f8f9fa; font-weight: 600; }
    .ai-markdown pre { background: #f1f3f5; padding: 10px; border-radius: 6px; overflow-x: auto; font-size: 0.82rem; }
    .ai-markdown code { background: #e9ecef; padding: 1px 4px; border-radius: 3px; font-size: 0.85em; }
    .ai-markdown pre code { background: none; padding: 0; }
    .ai-markdown p { margin-bottom: 0.5rem; }
    .ai-markdown ul, .ai-markdown ol { padding-left: 1.2rem; margin-bottom: 0.5rem; }
    #chat-messages::-webkit-scrollbar { width: 6px; }
    #chat-messages::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 3px; }
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
    document.querySelectorAll('.ai-suggestion').forEach(btn => {
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

        // Add user message
        appendMessage('user', message);

        // Show streaming indicator
        streamingContainer.style.display = 'flex';
        streamingContainer.style.setProperty('display', 'flex', 'important');
        streamingContent.innerHTML = '<span class="typing-indicator"><span class="dot"></span><span class="dot"></span><span class="dot"></span></span>';
        scrollToBottom();

        try {
            const response = await fetch('/ai/stream', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'text/event-stream',
                },
                body: JSON.stringify({
                    message: message,
                    conversation_id: conversationId,
                }),
            });

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let fullText = '';
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop(); // Keep incomplete line in buffer

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const data = line.substring(6);
                        if (data === '[DONE]') continue;

                        try {
                            const parsed = JSON.parse(data);
                            if (parsed.text) {
                                fullText += parsed.text;
                                streamingContent.innerHTML = markdownToHtml(fullText);
                                scrollToBottom();
                            }
                            if (parsed.conversationId) {
                                conversationId = parsed.conversationId;
                            }
                        } catch (err) {
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

            // Reload conversations in Livewire
            $wire.conversationId = conversationId;
            $wire.loadConversations();
            $wire.loadMessages();

        } catch (error) {
            console.error('Stream error:', error);
            streamingContainer.style.setProperty('display', 'none', 'important');
            appendMessage('assistant', '❌ Đã xảy ra lỗi khi kết nối AI. Vui lòng thử lại.', true);
        }

        isStreaming = false;
        sendBtn.disabled = false;
        chatInput.focus();
    });

    function appendMessage(role, content, isMarkdown = false) {
        const wrapper = document.createElement('div');
        wrapper.className = `mb-3 d-flex ${role === 'user' ? 'justify-content-end' : 'justify-content-start'}`;

        const bubble = document.createElement('div');
        bubble.className = `${role === 'user' ? 'bg-primary text-white' : 'bg-light border'} rounded-3 px-3 py-2`;
        bubble.style.maxWidth = '85%';
        bubble.style.whiteSpace = 'pre-wrap';
        bubble.style.wordBreak = 'break-word';

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

    // Listen for Livewire updates
    Livewire.on('conversationLoaded', () => {
        scrollToBottom();
    });
</script>
@endscript
