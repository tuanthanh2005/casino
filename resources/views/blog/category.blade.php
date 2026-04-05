@extends('layouts.app')
@section('title', 'Category')
@section('content')
<div class='container py-5'>
<div class='row g-4'>
@foreach($posts as $post)
<div class='col-md-4'><div class='card card-body'><h5 class='card-title'>{{ $post->title }}</h5><p class='card-text'>{{ $post->excerpt }}</p><a href='/blog/{{ $post->slug }}' class='btn btn-primary'>Read</a></div></div>
@endforeach
</div>
<div class='mt-5'>{{ $posts->links() }}</div>
</div>
@endsection
