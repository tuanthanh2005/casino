@extends('layouts.admin')
@section('title', 'Support Chat')

@push('admin-styles')
<style>
.sc-grid { display: grid; grid-template-columns: 280px 1fr; gap: 1rem; }
.sc-panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.sc-threads { max-height: 72vh; overflow-y: auto; }
.sc-thread { display: block; padding: 0.75rem 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.04); color: var(--text); text-decoration: none; }
.sc-thread.active { background: rgba(6,182,212,0.12); }
.sc-msgs { height: 62vh; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.6rem; }
.sc-row { display: flex; }
.sc-row.user { justify-content: flex-start; }
.sc-row.admin { justify-content: flex-end; }
.sc-bubble { max-width: 78%; padding: 0.7rem 0.85rem; border-radius: 12px; font-size: 0.88rem; line-height: 1.45; }
.sc-row.user .sc-bubble { background: var(--bg-card2); border: 1px solid var(--border); }
.sc-row.admin .sc-bubble { background: rgba(6,182,212,0.2); border: 1px solid rgba(6,182,212,0.45); }
.sc-time { font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem; }
@media(max-width: 900px){ .sc-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('admin-content')
<div class="page-header">
    <h1>💬 Hỗ trợ chat</h1>
    <p>Admin phản hồi tin nhắn khách hàng theo thời gian thực đơn giản.</p>
</div>

<div class="sc-grid">
    <div class="sc-panel">
        <div class="card-header">Khách hàng</div>
        <div class="sc-threads">
            @forelse($threads as $t)
                <a href="{{ route('admin.support.chat', ['user_id' => $t['user_id']]) }}" class="sc-thread {{ $selectedUserId === $t['user_id'] ? 'active' : '' }}">
                    <div style="display:flex; justify-content:space-between; gap:0.5rem">
                        <strong>{{ $t['name'] }}</strong>
                        @if($t['unread'] > 0)
                            <span style="background:var(--danger); color:#fff; border-radius:999px; padding:0 0.45rem; font-size:0.72rem">{{ $t['unread'] }}</span>
                        @endif
                    </div>
                    <div style="font-size:0.75rem; color:var(--text-muted)">{{ $t['email'] }}</div>
                </a>
            @empty
                <div style="padding:1rem; color:var(--text-muted)">Chưa có hội thoại nào.</div>
            @endforelse
        </div>
    </div>

    <div class="sc-panel">
        <div class="card-header">
            <span>{{ $selectedUser ? ('Chat với: ' . $selectedUser->name) : 'Chưa chọn khách hàng' }}</span>
        </div>

        @if($selectedUser)
            <div class="sc-msgs" id="admin-chat-list">
                @foreach($messages as $m)
                    <div class="sc-row {{ $m->from_role === 'admin' ? 'admin' : 'user' }}" data-id="{{ $m->id }}">
                        <div class="sc-bubble">
                            <div>{{ $m->message }}</div>
                            <div class="sc-time">{{ $m->created_at->format('H:i d/m') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="padding:0.9rem; border-top:1px solid var(--border); display:grid; grid-template-columns:1fr auto; gap:0.6rem">
                <input id="admin-chat-input" class="form-control" maxlength="1000" placeholder="Nhập phản hồi cho khách hàng...">
                <button class="btn btn-primary" onclick="sendAdminChat()"><i class="bi bi-send"></i> Gửi</button>
            </div>
        @else
            <div style="padding:1.5rem; color:var(--text-muted)">Hãy chọn 1 khách hàng ở cột trái.</div>
        @endif
    </div>
</div>
@endsection

@if($selectedUser)
@push('admin-scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const listEl = document.getElementById('admin-chat-list');
const inputEl = document.getElementById('admin-chat-input');
const userId = {{ (int) $selectedUser->id }};
let sendingAdminMsg = false;
let pollingAdminChat = false;

function getLastId(){
    const rows = listEl.querySelectorAll('[data-id]');
    return rows.length ? Number(rows[rows.length - 1].dataset.id || 0) : 0;
}

function appendAdminMsg(m){
    if (!m || !m.id) return;
    if (listEl.querySelector('[data-id="' + m.id + '"]')) return;

    const row = document.createElement('div');
    row.className = 'sc-row ' + (m.from_role === 'admin' ? 'admin' : 'user');
    row.dataset.id = m.id;
    row.innerHTML = `<div class="sc-bubble"><div>${String(m.text || '').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</div><div class="sc-time">${m.created_at}</div></div>`;
    listEl.appendChild(row);
    listEl.scrollTop = listEl.scrollHeight;
}

async function sendAdminChat(){
    const message = inputEl.value.trim();
    if (!message || sendingAdminMsg) return;
    sendingAdminMsg = true;

    const resp = await fetch('{{ route('admin.support.chat.send') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ user_id: userId, message })
    });
    const data = await resp.json();
    if (data.success) {
        inputEl.value = '';
        appendAdminMsg(data.message);
    }

    sendingAdminMsg = false;
}

async function pollAdminChat(){
    if (pollingAdminChat) return;
    pollingAdminChat = true;

    const resp = await fetch('{{ route('admin.support.chat.fetch', $selectedUser->id) }}?after_id=' + getLastId(), { headers: { 'Accept': 'application/json' } });
    const data = await resp.json();
    (data.messages || []).forEach(appendAdminMsg);

    pollingAdminChat = false;
}

inputEl?.addEventListener('keydown', function(e){
    if (e.key === 'Enter') {
        e.preventDefault();
        sendAdminChat();
    }
});

setInterval(pollAdminChat, 3500);
setTimeout(() => { listEl.scrollTop = listEl.scrollHeight; }, 100);
</script>
@endpush
@endif
