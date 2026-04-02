@extends('layouts.admin')
@section('title','Admin – Hạt Giống')

@section('admin-content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem">
    <h1 style="font-size:1.5rem; font-weight:900; margin:0">🌱 Quản Lý Hạt Giống</h1>
    <button onclick="openAdd()" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Thêm hạt giống</button>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul style="margin:0; padding-left:1rem">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Hạt giống</th><th>Giá mua</th><th>Giá bán base</th>
                    <th>Thời gian chín</th><th>Tưới tối đa</th><th>Lucky%</th>
                    <th>Trạng thái</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($seeds as $seed)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.6rem">
                            <span style="font-size:1.5rem">{{ $seed->emoji }}</span>
                            <div>
                                <div style="font-weight:700">{{ $seed->name }}</div>
                                <div style="font-size:0.72rem; color:var(--text-muted)">{{ $seed->description }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ number_format($seed->price_buy,0) }} PT</td>
                    <td>{{ number_format($seed->price_sell_base,0) }} PT</td>
                    <td>{{ $seed->grow_time_text }}</td>
                    <td>{{ $seed->max_waterings }} lần</td>
                    <td>{{ round($seed->lucky_chance*100) }}%</td>
                    <td>
                        @if($seed->is_active)
                            <span class="badge badge-success">🟢 Đang bán</span>
                        @else
                            <span class="badge badge-danger">⛔ Ngừng bán</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="openEdit({{ $seed->toJson() }})">Sửa</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteSeed({{ $seed->id }},'{{ $seed->name }}')">Xóa</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--text-muted)">Chưa có hạt giống nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ADD/EDIT MODAL --}}
<div class="modal-overlay @if($errors->any()) active @endif" id="seed-modal">
    <div class="modal-box" style="max-width:520px">
        <div class="modal-title" id="modal-title">Thêm hạt giống</div>
        
        @if($errors->any())
        <div style="background:rgba(255,0,0,0.1); border-left:4px solid red; padding:0.5rem; margin-bottom:1rem; font-size:0.85rem; color:#ffb7b7">
            Vui lòng kiểm tra lại các thông tin bên dưới.
        </div>
        @endif

        <form id="seed-form" method="POST" action="{{ route('admin.farm.seeds.store') }}">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <input type="hidden" name="seed_id" id="seed_id">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:0.75rem">
                <div>
                    <label class="form-label">Tên hạt giống</label>
                    <input class="form-control" name="name" id="f-name" required placeholder="VD: Cam, Táo...">
                </div>
                <div>
                    <label class="form-label">Emoji</label>
                    <input class="form-control" name="emoji" id="f-emoji" required placeholder="🍊" maxlength="4">
                </div>
                <div>
                    <label class="form-label">Giá mua (PT)</label>
                    <input class="form-control" type="number" name="price_buy" id="f-buy" required min="1">
                </div>
                <div>
                    <label class="form-label">Giá bán base (PT/trái)</label>
                    <input class="form-control" type="number" name="price_sell_base" id="f-sell" required min="1">
                </div>
                <div>
                    <label class="form-label">Thời gian chín (phút)</label>
                    <input class="form-control" type="number" name="grow_time_mins" id="f-time" required min="1" placeholder="60">
                </div>
                <div>
                    <label class="form-label">Tưới tối đa (lần)</label>
                    <input class="form-control" type="number" name="max_waterings" id="f-water" value="5" min="1" max="20">
                </div>
                <div>
                    <label class="form-label">Lucky chance (0–1, 20%=0.2)</label>
                    <input class="form-control" type="number" name="lucky_chance" id="f-lucky" value="0.20" min="0" max="1" step="0.01">
                </div>
                <div>
                    <label class="form-label">Sort order</label>
                    <input class="form-control" type="number" name="sort_order" id="f-sort" value="0" min="0">
                </div>
            </div>
            <div style="margin-bottom:0.75rem">
                <label class="form-label">Mô tả (tuỳ chọn)</label>
                <input class="form-control" name="description" id="f-desc" placeholder="Mô tả ngắn...">
            </div>
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem">
                <input type="checkbox" name="is_active" id="f-active" value="1" checked style="width:auto">
                <label for="f-active" style="font-size:0.85rem">Đang bán</label>
            </div>

            <div style="display:flex; gap:0.75rem">
                <button type="submit" class="btn btn-primary w-100" id="form-submit">Lưu</button>
                <button type="button" onclick="closeSeedModal()" class="btn btn-outline" style="flex:1">Huỷ</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function openAdd() {
    document.getElementById('modal-title').textContent = 'Thêm hạt giống';
    document.getElementById('seed-form').action = '{{ route("admin.farm.seeds.store") }}';
    document.getElementById('form-method').value = 'POST';
    ['name','emoji','buy','sell','time','desc','lucky','sort'].forEach(k => {
        const el = document.getElementById('f-'+k);
        if (el) el.value = k === 'water' ? 5 : k === 'lucky' ? '0.20' : '';
    });
    document.getElementById('f-water').value = 5;
    document.getElementById('f-active').checked = true;
    document.getElementById('seed-modal').classList.add('active');
}

function openEdit(seed) {
    document.getElementById('modal-title').textContent = 'Sửa hạt giống';
    document.getElementById('seed-form').action = `/admin/farm/seeds/${seed.id}`;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('f-name').value = seed.name;
    document.getElementById('f-emoji').value = seed.emoji;
    document.getElementById('f-buy').value = seed.price_buy;
    document.getElementById('f-sell').value = seed.price_sell_base;
    document.getElementById('f-time').value = seed.grow_time_mins;
    document.getElementById('f-water').value = seed.max_waterings;
    document.getElementById('f-lucky').value = seed.lucky_chance;
    document.getElementById('f-sort').value = seed.sort_order;
    document.getElementById('f-desc').value = seed.description || '';
    document.getElementById('f-active').checked = !!seed.is_active;
    document.getElementById('seed-modal').classList.add('active');
}

function closeSeedModal() {
    document.getElementById('seed-modal').classList.remove('active');
}

async function deleteSeed(id, name) {
    if (!confirm(`Xóa hạt giống "${name}"? Thao tác này không thể hoàn tác.`)) return;
    const resp = await fetch(`/admin/farm/seeds/${id}`, {
        method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
    });
    const data = await resp.json();
    showToast(data.message, data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 1200);
}

document.getElementById('seed-modal').addEventListener('click', function(e) {
    if (e.target === this) closeSeedModal();
});
</script>
@endpush
