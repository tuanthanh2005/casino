@extends('layouts.admin')

@section('title', 'Sửa Bài Blog')

@section('admin-content')
<div class="page-header">
    <h1>🛠️ Cập Nhật Bài Blog</h1>
    <p>Điều chỉnh nội dung, metadata hoặc trạng thái publish.</p>
</div>

@include('admin.blog.partials.form', [
    'action' => route('admin.blog-posts.update', $post),
    'method' => 'PUT',
    'post' => $post,
])
@endsection
