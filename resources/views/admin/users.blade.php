@extends('layouts.admin')

@section('title', 'Quản lý User')

@section('admin-content')
<div class="page-header d-flex align-center justify-between">
    <div>
        <h1>👥 Quản lý Người Dùng</h1>
        <p>Xem và chỉnh sửa số điểm của người dùng</p>
    </div>
</div>

<!-- Search -->
<div class="card mb-3" style="margin-bottom:1.25rem">
    <div class="card-body" style="padding:1rem 1.5rem">
        <form method="GET" action="{{ route('admin.users') }}" style="display:flex; gap:0.75rem; align-items:center">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, email..."
                   value="{{ request('search') }}" style="max-width:350px">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Tìm
            </button>
            @if(request('search'))
                <a href="{{ route('admin.users') }}" class="btn btn-outline">Xóa bộ lọc</a>
            @endif
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Email</th>
                    <th>Số dư (PT)</th>
                    <th>Số cược</th>
                    <th>Ngày tham gia</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.75rem">
                            <img src="{{ $user->avatar_url }}" style="width:32px; height:32px; border-radius:50%;">
                            <span style="font-weight:600">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--text-muted)">{{ $user->email }}</td>
                    <td>
                        <span style="font-weight:700; color:var(--accent)" id="balance-{{ $user->id }}">
                            {{ number_format($user->balance_point, 2) }}
                        </span>
                    </td>
                    <td>{{ $user->bets_count }}</td>
                    <td style="color:var(--text-muted)">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex; gap:0.5rem">
                            <button onclick="openAdjustModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->balance_point }})"
                                    class="btn btn-sm btn-primary">
                                <i class="bi bi-coin"></i> Điều chỉnh điểm
                            </button>
                            <button onclick="handleResetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    class="btn btn-sm btn-outline" style="color:var(--warning); border-color:rgba(245,158,11,0.3)">
                                <i class="bi bi-key"></i> Reset mật khẩu
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="color:var(--text-muted); padding:2rem">
                        Không tìm thấy người dùng nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:1rem 1.5rem; border-top:1px solid var(--border)">
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Adjust Points Modal -->
<div class="modal-overlay" id="adjust-modal">
    <div class="modal-box">
        <div class="modal-title" id="modal-user-name">Điều chỉnh điểm</div>
        <p style="color:var(--text-muted); font-size:0.875rem; margin-bottom:1.5rem">
            Số dư hiện tại: <strong style="color:var(--accent)" id="modal-current-bal">0</strong> PT
        </p>

        <div class="mb-3">
            <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">Hành động</label>
            <div style="display:flex; gap:0.75rem;">
                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.875rem">
                    <input type="radio" name="modal-action" value="add" id="radio-add" checked style="accent-color:var(--success)">
                    <span style="color:#10b981">Cộng điểm</span>
                </label>
                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.875rem">
                    <input type="radio" name="modal-action" value="subtract" id="radio-sub" style="accent-color:var(--danger)">
                    <span style="color:#ef4444">Trừ điểm</span>
                </label>
            </div>
        </div>

        <div class="mb-3">
            <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">Số điểm</label>
            <input type="number" id="modal-amount" class="form-control" placeholder="Nhập số điểm..." min="1">
        </div>

        <div class="mb-3">
            <label style="font-size:0.8rem; color:var(--text-muted); display:block; margin-bottom:0.4rem">Lý do</label>
            <input type="text" id="modal-reason" class="form-control" placeholder="VD: Nạp tiền mặt 100k, hoàn điểm...">
        </div>

        <div style="display:flex; gap:0.75rem">
            <button onclick="closeAdjustModal()" class="btn btn-outline" style="flex:1">Hủy</button>
            <button onclick="submitAdjust()" class="btn btn-primary" style="flex:1" id="modal-submit-btn">
                <i class="bi bi-check2"></i> Xác nhận
            </button>
        </div>
    </div>
</div>

<!-- Reset Password Result Modal -->
<div class="modal-overlay" id="reset-modal">
    <div class="modal-box" style="max-width:380px; text-align:center">
        <div style="font-size:3rem; margin-bottom:1rem">🔑</div>
        <div class="modal-title" style="margin-bottom:0.5rem">Reset mật khẩu thành công</div>
        <p style="color:var(--text-muted); font-size:0.875rem; margin-bottom:1.5rem">
            Mật khẩu mới của <strong id="reset-user-name">Người dùng</strong> là:
        </p>
        
        <div style="position:relative; margin-bottom:1.5rem">
            <input type="text" id="new-password-display" class="form-control" readonly 
                   style="text-align:center; font-family:monospace; font-size:1.2rem; font-weight:700; padding:0.8rem; background:rgba(99,102,241,0.1); border:1px dashed var(--primary); color:var(--primary)">
            <button onclick="copyPassword()" class="btn btn-sm btn-primary" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); padding:0.4rem 0.6rem">
                <i class="bi bi-clipboard"></i> Copy
            </button>
        </div>

        <button onclick="closeResetModal()" class="btn btn-primary w-100">Đóng</button>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let currentUserId = null;

function openAdjustModal(userId, name, balance) {
    currentUserId = userId;
    document.getElementById('modal-user-name').textContent = '💰 ' + name;
    document.getElementById('modal-current-bal').textContent = parseFloat(balance).toLocaleString('en-US', { minimumFractionDigits: 2 });
    document.getElementById('modal-amount').value = '';
    document.getElementById('modal-reason').value = '';
    document.getElementById('radio-add').checked = true;
    document.getElementById('adjust-modal').classList.add('active');
}

function closeAdjustModal() {
    document.getElementById('adjust-modal').classList.remove('active');
    currentUserId = null;
}

async function submitAdjust() {
    const amount = parseFloat(document.getElementById('modal-amount').value);
    const action = document.querySelector('input[name="modal-action"]:checked').value;
    const reason = document.getElementById('modal-reason').value.trim();

    if (!amount || amount <= 0) { showToast('Nhập số điểm hợp lệ', 'error'); return; }
    if (!reason) { showToast('Nhập lý do điều chỉnh', 'error'); return; }

    const btn = document.getElementById('modal-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass"></i> Đang xử lý...';

    try {
        const resp = await fetch(`/admin/users/${currentUserId}/adjust-points`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ amount, action, reason })
        });
        const data = await resp.json();

        if (data.success) {
            showToast(data.message, 'success');
            document.getElementById(`balance-${currentUserId}`).textContent = data.new_balance;
            document.getElementById('modal-current-bal').textContent = data.new_balance;
            closeAdjustModal();
        } else {
            showToast(data.message, 'error');
        }
    } catch (e) {
        showToast('Lỗi kết nối', 'error');
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check2"></i> Xác nhận';
}

// Reset Password Logic
async function handleResetPassword(userId, name) {
    if (!confirm(`Bạn có chắc chắn muốn reset mật khẩu cho [${name}] không?\nHệ thống sẽ tạo mật khẩu ngẫu nhiên mới.`)) return;

    try {
        const resp = await fetch(`/admin/users/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            }
        });
        const data = await resp.json();

        if (data.success) {
            document.getElementById('reset-user-name').textContent = name;
            document.getElementById('new-password-display').value = data.password;
            document.getElementById('reset-modal').classList.add('active');
        } else {
            showToast(data.message, 'error');
        }
    } catch (e) {
        showToast('Lỗi kết nối', 'error');
    }
}

function closeResetModal() {
    document.getElementById('reset-modal').classList.remove('active');
}

function copyPassword() {
    const input = document.getElementById('new-password-display');
    input.select();
    document.execCommand('copy');
    showToast('Đã copy mật khẩu vào bộ nhớ tạm!', 'success');
}

// Close on backdrop
document.getElementById('adjust-modal').addEventListener('click', function(e) {
    if (e.target === this) closeAdjustModal();
});
document.getElementById('reset-modal').addEventListener('click', function(e) {
    if (e.target === this) closeResetModal();
});
</script>
@endpush
