@extends('layouts.admin')

@section('title', 'Quản lý Phần Thưởng')

@section('admin-content')
<div class="page-header d-flex align-center justify-between">
    <div>
        <h1>🎁 Quản lý Phần Thưởng</h1>
        <p>Thêm, sửa, xóa các vật phẩm trong cửa hàng</p>
    </div>
    <a href="{{ route('admin.rewards.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm phần thưởng
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hình ảnh</th>
                    <th>Tên phần thưởng</th>
                    <th>Giá (PT)</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        @if($item->image)
                            <img src="{{ asset($item->image) }}" style="width:50px; height:50px; object-fit:cover; border-radius:8px;">
                        @else
                            <div style="width:50px; height:50px; background:var(--bg-card2); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1.5rem">🎁</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600">{{ $item->name }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted)">{{ Str::limit($item->description, 50) }}</div>
                    </td>
                    <td><strong style="color:var(--accent)">{{ number_format($item->point_price, 0) }}</strong></td>
                    <td>
                        @if($item->status === 'active')
                            <span class="badge badge-success">Đang bán</span>
                        @else
                            <span class="badge badge-danger">Tạm dừng</span>
                        @endif
                    </td>
                    <td style="color:var(--text-muted)">{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex; gap:0.5rem">
                            <a href="{{ route('admin.rewards.edit', $item) }}" class="btn btn-sm btn-outline">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.rewards.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('Xóa phần thưởng này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="color:var(--text-muted); padding:2rem">
                        Chưa có phần thưởng nào.
                        <a href="{{ route('admin.rewards.create') }}" style="color:var(--primary)">Thêm ngay</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div style="padding:1rem 1.5rem; border-top:1px solid var(--border)">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection
