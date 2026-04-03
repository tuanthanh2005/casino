@extends('layouts.admin')

@section('title', 'Thông báo hệ thống')

@section('admin-content')
<div class="page-header">
    <h1>🔔 Thông báo hệ thống</h1>
    <p>Gửi thông báo đến toàn bộ user hoặc gửi riêng lẻ theo từng tài khoản.</p>
</div>

<div class="card" style="max-width:920px; margin-bottom:1rem;">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul style="margin:0; padding-left:1.1rem;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.notifications.store') }}" method="POST" id="notif-form">
            @csrf

            <div class="mb-3">
                <label class="form-label-admin">Tiêu đề thông báo</label>
                <input type="text" class="form-control" name="title" value="{{ old('title') }}" required maxlength="180" placeholder="VD: Bảo trì hệ thống lúc 02:00">
            </div>

            <div class="mb-3">
                <label class="form-label-admin">Nội dung</label>
                <textarea class="form-control" name="message" rows="5" required maxlength="5000" placeholder="Nhập nội dung thông báo...">{{ old('message') }}</textarea>
            </div>

            <div class="grid-2 mb-3">
                <div>
                    <label class="form-label-admin">Loại gửi</label>
                    <select class="form-control" name="target_type" id="target_type" onchange="toggleTargetUser()">
                        <option value="all" {{ old('target_type') === 'all' ? 'selected' : '' }}>Gửi toàn bộ user</option>
                        <option value="user" {{ old('target_type') === 'user' ? 'selected' : '' }}>Gửi user cụ thể</option>
                    </select>
                </div>
                <div id="target_user_wrap" style="display:none;">
                    <label class="form-label-admin">Chọn user nhận</label>
                    <select class="form-control" name="target_user_id" id="target_user_id">
                        <option value="">-- Chọn user --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ (string)old('target_user_id') === (string)$u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Gửi thông báo</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="bi bi-clock-history"></i> Lịch sử thông báo</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Tiêu đề</th>
                    <th>Đối tượng</th>
                    <th>Người gửi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $n)
                    <tr>
                        <td style="white-space:nowrap; font-size:0.8rem; color:var(--text-muted)">{{ $n->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $n->title }}</div>
                            <div style="font-size:0.78rem; color:var(--text-muted)">{{ \Illuminate\Support\Str::limit($n->message, 120) }}</div>
                        </td>
                        <td>
                            @if($n->target_type === 'all')
                                <span class="badge badge-primary">Toàn bộ user</span>
                            @else
                                <span class="badge badge-warning">{{ $n->targetUser?->name ?? 'User riêng' }}</span>
                            @endif
                        </td>
                        <td>{{ $n->sender?->name ?? 'Admin' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding:2rem; color:var(--text-muted)">Chưa có thông báo nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($notifications->hasPages())
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--border)">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@push('admin-styles')
<style>
    .form-label-admin {
        display: block;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
        font-weight: 500;
    }
</style>
@endpush

@push('admin-scripts')
<script>
function toggleTargetUser() {
    const type = document.getElementById('target_type').value;
    const wrap = document.getElementById('target_user_wrap');
    wrap.style.display = type === 'user' ? '' : 'none';
}

document.addEventListener('DOMContentLoaded', toggleTargetUser);
</script>
@endpush
