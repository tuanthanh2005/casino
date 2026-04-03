@extends('layouts.admin')

@section('title', 'Blog SEO')

@section('admin-content')
<div class="page-header d-flex align-center justify-between">
    <div>
        <h1>📝 Quản lý Blog SEO</h1>
        <p>Viết bài chuẩn SEO, quản lý publish và tối ưu nội dung.</p>
    </div>
    <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Viết bài mới
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tiêu đề</th>
                    <th>Slug</th>
                    <th>Trạng thái</th>
                    <th>Xuất bản</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>
                            <div style="font-weight:600">{{ $post->title }}</div>
                            <div style="font-size:0.75rem; color:var(--text-muted)">
                                {{ \Illuminate\Support\Str::limit($post->excerpt ?: strip_tags($post->content), 80) }}
                            </div>
                        </td>
                        <td style="font-size:0.78rem; color:var(--text-muted)">{{ $post->slug }}</td>
                        <td>
                            @if($post->is_published)
                                <span class="badge badge-success">Đã publish</span>
                            @else
                                <span class="badge badge-warning">Bản nháp</span>
                            @endif
                        </td>
                        <td style="font-size:0.8rem; color:var(--text-muted)">
                            {{ $post->published_at?->format('d/m/Y H:i') ?: '-' }}
                        </td>
                        <td style="font-size:0.8rem; color:var(--text-muted)">{{ $post->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div style="display:flex; gap:0.45rem; align-items:center;">
                                <a href="{{ route('admin.blog-posts.edit', $post) }}" class="btn btn-sm btn-outline">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($post->is_published)
                                    <a href="{{ route('blog.show', $post) }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.blog-posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Xóa bài viết này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding:2rem; color:var(--text-muted);">
                            Chưa có bài blog nào. <a href="{{ route('admin.blog-posts.create') }}" style="color:var(--primary)">Viết bài đầu tiên</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--border)">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
