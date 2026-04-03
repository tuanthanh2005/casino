@extends('layouts.admin')

@section('title', 'Viết Bài Blog')

@section('admin-content')
<div class="page-header">
    <h1>✍️ Viết Bài Blog Mới</h1>
    <p>Soạn nội dung SEO, đặt slug và metadata để tăng khả năng index.</p>
</div>

@include('admin.blog.partials.form', [
    'action' => route('admin.blog-posts.store'),
    'method' => 'POST',
    'post' => null,
])
@endsection
