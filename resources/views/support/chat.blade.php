@extends('layouts.app')
@section('title', 'Hỗ trợ chat')

@push('styles')
<style>
.chat-shell { max-width: 820px; margin: 0 auto; }
.chat-box { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
.chat-head { padding: 1rem 1.2rem; border-bottom: 1px solid var(--border); font-weight: 700; }
.chat-list { height: 58vh; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.65rem; }
.msg-row { display: flex; }
.msg-row.user { justify-content: flex-end; }
.msg-row.admin { justify-content: flex-start; }
.msg-bubble { max-width: 78%; padding: 0.7rem 0.85rem; border-radius: 12px; font-size: 0.9rem; line-height: 1.45; }
.msg-row.user .msg-bubble { background: rgba(6,182,212,0.18); border: 1px solid rgba(6,182,212,0.45); }
.msg-row.admin .msg-bubble { background: var(--bg-card2); border: 1px solid var(--border); }
.msg-time { font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem; }
.chat-send { display: grid; grid-template-columns: 1fr auto; gap: 0.6rem; padding: 0.9rem; border-top: 1px solid var(--border); }
@media (max-width: 768px) { .chat-list { height: 62vh; } .msg-bubble { max-width: 88%; } }
</style>
@endpush

@section('content')
<div class="chat-shell">
    <div style="margin-bottom:1rem">
        <h1 style="font-size:1.6rem; font-weight:900">💬 Hỗ trợ chat</h1>
        <p style="color:var(--text-muted)">Nhắn trực tiếp với admin để được xử lý đơn hàng nhanh hơn.</p>
    </div>

    <div class="chat-box">
        <div class="chat-head">Chat với admin</div>
        <div class="chat-list" id="chat-list">
            @forelse($messages as $m)
                <div class="msg-row {{ $m->from_role === 'user' ? 'user' : 'admin' }}" data-id="{{ $m->id }}">
                    <div class="msg-bubble">
                        <div>{{ $m->message }}</div>
                        <div class="msg-time">{{ $m->created_at->format('H:i d/m') }}</div>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-muted); padding:2rem 0">Chưa có tin nhắn. Hãy gửi tin đầu tiên.</div>
            @endforelse
        </div>
        <div class="chat-send">
            <input id="chat-input" class="form-control" maxlength="1000" placeholder="Nhập tin nhắn cần hỗ trợ...">
            <button class="btn btn-primary" onclick="sendChat()"><i class="bi bi-send"></i> Gửi</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const listEl = document.getElementById('chat-list');
const inputEl = document.getElementById('chat-input');

function getLastId() {
    const rows = listEl.querySelectorAll('[data-id]');
    return rows.length ? Number(rows[rows.length - 1].dataset.id || 0) : 0;
}

function appendMessage(m) {
    const row = document.createElement('div');
    row.className = 'msg-row ' + (m.from_role === 'user' ? 'user' : 'admin');
    row.dataset.id = m.id;
    row.innerHTML = `<div class="msg-bubble"><div>${String(m.text || '').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div><div class="msg-time">${m.created_at}</div></div>`;
    listEl.appendChild(row);
    listEl.scrollTop = listEl.scrollHeight;
}

async function sendChat() {
    const message = inputEl.value.trim();
    if (!message) return;
    const resp = await fetch('{{ route('support.chat.send') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ message })
    });
    const data = await resp.json();
    if (data.success) {
        inputEl.value = '';
        appendMessage(data.message);
    }
}

async function pollChat() {
    const resp = await fetch('{{ route('support.chat.fetch') }}?after_id=' + getLastId(), { headers: { 'Accept': 'application/json' } });
    const data = await resp.json();
    (data.messages || []).forEach(appendMessage);
}

inputEl?.addEventListener('keydown', function(e){
    if (e.key === 'Enter') {
        e.preventDefault();
        sendChat();
    }
});

setInterval(pollChat, 4000);
setTimeout(() => { listEl.scrollTop = listEl.scrollHeight; }, 100);
</script>
@endpush
