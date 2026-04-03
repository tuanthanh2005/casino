<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::query()
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function show(BlogPost $post)
    {
        abort_unless($post->is_published && $post->published_at && $post->published_at->lte(now()), 404);

        $relatedPosts = BlogPost::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
