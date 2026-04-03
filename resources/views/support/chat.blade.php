@extends('layouts.app')
@section('title', 'Hỗ trợ chat')

@push('styles')
<style>
.chat-shell { max-width: 1120px; margin: 0 auto; }
.chat-grid { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 1rem; align-items: start; }
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
.support-panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 1rem; display: grid; gap: 0.65rem; }
.support-title { font-size: 1rem; font-weight: 800; }
.support-sub { font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.25rem; }
.support-meta { font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem; }
@media (max-width: 992px) {
    .chat-grid { grid-template-columns: 1fr; }
    .chat-list { height: 62vh; }
    .msg-bubble { max-width: 88%; }
}
</style>
@endpush

@section('content')
<div class="chat-shell">
    <div style="margin-bottom:1rem">
        <h1 style="font-size:1.6rem; font-weight:900">💬 Hỗ trợ chat</h1>
        <p style="color:var(--text-muted)">Nhắn trực tiếp với admin để được xử lý đơn hàng nhanh hơn.</p>
    </div>

    <div class="chat-grid">
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

        <aside class="support-panel">
            <div>
                <div class="support-title">📞 Kênh hỗ trợ khác</div>
                <div class="support-sub">Nếu cần xử lý gấp, bạn có thể liên hệ trực tiếp qua các kênh bên dưới.</div>
            </div>

            <a href="{{ route('nav.index') }}" class="btn btn-outline" style="justify-content:flex-start">
                <i class="bi bi-shield-check"></i> {{ $support['center_label'] }}
            </a>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $support['phone']) }}" class="btn btn-outline" style="justify-content:flex-start">
                <i class="bi bi-telephone"></i> Hotline: {{ $support['phone'] }}
            </a>
            <a href="mailto:{{ $support['email'] }}" class="btn btn-outline" style="justify-content:flex-start">
                <i class="bi bi-envelope"></i> Email: {{ $support['email'] }}
            </a>
            <a href="{{ $support['zalo_url'] }}" target="_blank" rel="noopener" class="btn btn-outline" style="justify-content:flex-start">
                <i class="bi bi-telegram"></i> Telegram hỗ trợ
            </a>
            <a href="{{ $support['messenger_url'] }}" target="_blank" rel="noopener" class="btn btn-outline" style="justify-content:flex-start">
                <i class="bi bi-messenger"></i> Facebook Messenger
            </a>

            <div class="support-meta">
                Thời gian hỗ trợ: {{ $support['working_hours'] }}.
            </div>
        </aside>
    </div>

    <div class="chat-box" style="margin-top:1rem">
        <div class="chat-head">Lưu ý xử lý nhanh</div>
        <div style="padding:0.9rem 1.1rem; color:var(--text-muted); font-size:0.86rem; line-height:1.55">
            Khi nhắn hỗ trợ, bạn nên gửi kèm mã đơn hàng, dịch vụ đã mua và ảnh lỗi (nếu có) để admin xử lý nhanh hơn.
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
