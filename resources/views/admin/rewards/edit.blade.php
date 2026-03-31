@extends('layouts.admin')

@section('title', 'Sửa Phần Thưởng')

@section('admin-content')
<div class="page-header">
    <h1>✏️ Sửa Phần Thưởng</h1>
</div>

<div class="card" style="max-width:700px">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul style="margin:0; padding-left:1.25rem">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.rewards.update', $reward) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label-admin">Tên phần thưởng <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $reward->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label-admin">Mô tả</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $reward->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label-admin">Giá (Point) <span style="color:var(--danger)">*</span></label>
                <input type="number" name="point_price" class="form-control" value="{{ old('point_price', $reward->point_price) }}" required min="1">
            </div>

            <div class="mb-3">
                <label class="form-label-admin">Hình ảnh</label>
                @if($reward->image)
                    <div style="margin-bottom:0.75rem">
                        <img src="{{ asset($reward->image) }}" style="max-width:150px; border-radius:8px; margin-bottom:0.5rem; display:block">
                        <small style="color:var(--text-muted)">Ảnh hiện tại. Chọn ảnh mới để thay thế.</small>
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                <div id="img-preview" style="margin-top:0.75rem; display:none">
                    <img id="preview-img" src="" style="max-width:200px; border-radius:10px;">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-admin">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="active" {{ $reward->status === 'active' ? 'selected' : '' }}>Đang bán</option>
                    <option value="inactive" {{ $reward->status === 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                </select>
            </div>

            <div style="display:flex; gap:0.75rem; margin-top:1.5rem">
                <a href="{{ route('admin.rewards.index') }}" class="btn btn-outline">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('admin-styles')
<style>
.form-label-admin { display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.4rem; font-weight: 500; }
.mb-3 { margin-bottom: 1.25rem; }
</style>
@endpush

@push('admin-scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('img-preview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
