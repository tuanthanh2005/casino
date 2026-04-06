@extends('layouts.admin')

@section('page_title', 'Support Messages')

@section('admin_content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-4">{{ __('Conversations') }}</h5>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($conversations as $convo)
                <div class="p-3 border rounded-4 cursor-pointer hover-bg-light transition-all" onclick="openConvo('{{ $convo->user_id ?: $convo->session_id }}', '{{ $convo->guest_name ?: ($convo->user->name ?? 'Guest User') }}')" style="border-color: rgba(15, 23, 42, 0.05) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold small">{{ $convo->user ? $convo->user->name : ($convo->guest_name ? __('Khách') . ': ' . $convo->guest_name : __('Khách') . ': ' . __('Ẩn danh')) }}</span>
                        <span class="text-secondary" style="font-size: 0.65rem;">{{ $convo->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div id="chat-box" class="card border-0 shadow-sm flex-column overflow-hidden" style="display: none; height: 600px;">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center" style="background: var(--dark); color: white;">
                <h5 id="convo-name" class="fw-bold mb-0 h6">{{ __('Select a conversation') }}</h5>
                <span class="badge bg-success small">{{ __('Online') }}</span>
            </div>
            
            <div id="convo-messages" class="card-body p-4 overflow-y-auto" style="background: #f8fafc; flex-grow: 1; display: flex; flex-direction: column; gap: 1rem;">
                <!-- Messages load here -->
            </div>

            <div class="p-4 border-top">
                <div class="d-flex gap-2">
                    <input type="text" id="admin-input" class="form-control border-0 bg-light" placeholder="{{ __('Type your response...') }}" style="padding: 0.75rem 1.25rem; border-radius: 99px; font-size: 0.8125rem;">
                    <button onclick="sendAdminReply()" class="btn btn-primary" style="border-radius: 99px;">{{ __('Send') }}</button>
                </div>
            </div>
        </div>

        <div id="empty-chat" class="h-100 d-flex flex-column align-items-center justify-content-center opacity-50 p-5">
            <div style="width: 64px; height: 64px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">💬</div>
            <p class="small text-secondary fw-bold">{{ __('Select a conversation from the left to start chatting.') }}</p>
        </div>
    </div>
</div>

<script>
    let currentId = null;

    function openConvo(id, name) {
        currentId = id;
        document.getElementById('empty-chat').style.display = 'none';
        document.getElementById('chat-box').style.display = 'flex';
        document.getElementById('convo-name').innerText = name;
        loadMessages();
    }

    async function loadMessages() {
        if(!currentId) return;
        const res = await fetch(`/admin/messages/${currentId}`);
        const data = await res.json();
        const container = document.getElementById('convo-messages');
        container.innerHTML = '';
        data.messages.forEach(msg => {
            const div = document.createElement('div');
            div.style.cssText = msg.is_from_admin 
                ? 'align-self: flex-end; background: var(--dark); color: white; padding: 0.75rem 1rem; border-radius: 12px 12px 0 12px; font-size: 0.8125rem; max-width: 80%;'
                : 'align-self: flex-start; background: white; padding: 0.75rem 1rem; border-radius: 0 12px 12px 12px; font-size: 0.8125rem; max-width: 80%; border: 1px solid rgba(15,23,42,0.05);';
            div.innerText = msg.message;
            container.appendChild(div);
        });
        container.scrollTop = container.scrollHeight;
    }

    async function sendAdminReply() {
        const input = document.getElementById('admin-input');
        const msg = input.value.trim();
        if(!msg || !currentId) return;
        
        input.value = '';
        
        await fetch('/admin/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: msg, to_id: currentId })
        });
        
        loadMessages();
    }
</script>

<style>
    .hover-bg-light:hover { background: #f8fafc !important; }
    .transition-all { transition: all 0.2s ease; }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection
