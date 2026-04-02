@extends('layouts.admin')
@section('title', 'Quản Lý Dịch Vụ NAV')

@section('admin-content')
<div class="page-header">
    <div class="d-flex justify-between align-center">
        <div>
            <h1>🛡️ Quản Lý Dịch Vụ</h1>
            <p>Thêm, sửa, xóa các loại dịch vụ Hỗ Trợ MXH</p>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="bi bi-plus-lg"></i> Thêm Dịch Vụ
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger"><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Dịch Vụ</th>
                        <th>Giá</th>
                        <th>Hạn KC (ngày)</th>
                        <th>Đơn hàng</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $s)
                    <tr>
                        <td style="color:var(--text-muted)">{{ $s->sort_order ?: $s->id }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <span style="width:36px;height:36px;background:{{ $s->color }}22;border-radius:10px;display:flex;align-items:center;justify-content:center;color:{{ $s->color }};font-size:1rem;flex-shrink:0">
                                    <i class="bi {{ $s->icon }}"></i>
                                </span>
                                <div>
                                    <div style="font-weight:600;color:var(--text)">{{ $s->name }}</div>
                                    <div style="font-size:0.75rem;color:var(--text-muted)">{{ Str::limit($s->description, 60) }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span style="color:#69C9D0;font-weight:700">{{ number_format((float)$s->price, 0, ',', '.') }}</span> PT</td>
                        <td>{{ $s->appeal_deadline_days }} ngày</td>
                        <td>{{ $s->orders_count ?? $s->orders()->count() }} đơn</td>
                        <td>
                            @if($s->is_active)
                            <span class="badge badge-success">Hoạt động</span>
                            @else
                            <span class="badge badge-danger">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:0.5rem">
                                <button class="btn btn-outline btn-sm" onclick="openEditModal({{ $s->toJson() }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.nav.services.delete', $s->id) }}" method="POST" onsubmit="return confirm('Xóa dịch vụ này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:2rem">Chưa có dịch vụ nào. Thêm dịch vụ đầu tiên!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal-overlay" id="addModal">
    <div class="modal-box" style="max-width:560px">
        <div class="modal-title"><i class="bi bi-plus-circle" style="color:var(--primary)"></i> Thêm Dịch Vụ Mới</div>
        <form action="{{ route('admin.nav.services.store') }}" method="POST">
        @csrf
        <div class="grid-2">
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Tên dịch vụ *</label>
                <input type="text" name="name" class="form-control" placeholder="VD: Kháng cáo TK bị khóa" required>
            </div>
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Giá (PT/VNĐ) *</label>
                <input type="number" name="price" class="form-control" placeholder="200000" min="0" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Mô tả</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Mô tả ngắn về dịch vụ"></textarea>
        </div>
        <div class="mb-3">
            <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Yêu cầu cần cung cấp</label>
            <textarea name="requirements" class="form-control" rows="2" placeholder="VD: CCCD 2 mặt, email đăng ký TikTok..."></textarea>
        </div>
        <div class="grid-2">
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Hạn kháng cáo (ngày) *</label>
                <input type="number" name="appeal_deadline_days" class="form-control" value="30" min="1" max="365" required>
            </div>
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Thứ tự hiển thị</label>
                <input type="number" name="sort_order" class="form-control" value="0" min="0">
            </div>
        </div>
        <div class="grid-2">
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Icon (Bootstrap Icons)</label>
                <input type="text" name="icon" class="form-control" value="bi-shield-check" placeholder="bi-shield-check">
            </div>
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Màu sắc</label>
                <input type="color" name="color" value="#69C9D0" style="width:100%;height:38px;background:var(--bg-card2);border:1px solid var(--border);border-radius:8px;padding:2px">
            </div>
        </div>
        <div class="mb-3" style="display:flex;align-items:center;gap:0.75rem">
            <input type="checkbox" name="is_active" value="1" id="addActive" checked>
            <label for="addActive" style="font-size:0.85rem">Hiển thị cho người dùng</label>
        </div>
        <div style="display:flex;gap:0.75rem;margin-top:1rem">
            <button type="submit" class="btn btn-primary" style="flex:1">Thêm Dịch Vụ</button>
            <button type="button" class="btn btn-outline" onclick="closeModal('addModal')">Huỷ</button>
        </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="editModal">
    <div class="modal-box" style="max-width:560px">
        <div class="modal-title"><i class="bi bi-pencil" style="color:var(--accent)"></i> Sửa Dịch Vụ</div>
        <form id="editForm" method="POST">
        @csrf @method('PUT')
        <div class="grid-2">
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Tên dịch vụ *</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Giá (PT/VNĐ) *</label>
                <input type="number" name="price" id="edit_price" class="form-control" min="0" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Mô tả</label>
            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
        </div>
        <div class="mb-3">
            <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Yêu cầu cần cung cấp</label>
            <textarea name="requirements" id="edit_requirements" class="form-control" rows="2"></textarea>
        </div>
        <div class="grid-2">
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Hạn kháng cáo (ngày)</label>
                <input type="number" name="appeal_deadline_days" id="edit_deadline" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Thứ tự</label>
                <input type="number" name="sort_order" id="edit_sort" class="form-control">
            </div>
        </div>
        <div class="grid-2">
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Icon</label>
                <input type="text" name="icon" id="edit_icon" class="form-control">
            </div>
            <div class="mb-3">
                <label class="text-muted" style="display:block;font-size:0.8rem;margin-bottom:0.4rem">Màu sắc</label>
                <input type="color" name="color" id="edit_color" style="width:100%;height:38px;background:var(--bg-card2);border:1px solid var(--border);border-radius:8px;padding:2px">
            </div>
        </div>
        <div class="mb-3" style="display:flex;align-items:center;gap:0.75rem">
            <input type="checkbox" name="is_active" value="1" id="edit_active">
            <label for="edit_active" style="font-size:0.85rem">Hiển thị cho người dùng</label>
        </div>
        <div style="display:flex;gap:0.75rem;margin-top:1rem">
            <button type="submit" class="btn btn-success" style="flex:1">Lưu Thay Đổi</button>
            <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Huỷ</button>
        </div>
        </form>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
function openAddModal() {
    document.getElementById('addModal').classList.add('active');
}
function openEditModal(svc) {
    document.getElementById('edit_name').value = svc.name;
    document.getElementById('edit_price').value = svc.price;
    document.getElementById('edit_description').value = svc.description || '';
    document.getElementById('edit_requirements').value = svc.requirements || '';
    document.getElementById('edit_deadline').value = svc.appeal_deadline_days;
    document.getElementById('edit_sort').value = svc.sort_order || 0;
    document.getElementById('edit_icon').value = svc.icon || 'bi-shield-check';
    document.getElementById('edit_color').value = svc.color || '#69C9D0';
    document.getElementById('edit_active').checked = !!svc.is_active;
    document.getElementById('editForm').action = `/admin/nav/services/${svc.id}`;
    document.getElementById('editModal').classList.add('active');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});
</script>
@endpush
