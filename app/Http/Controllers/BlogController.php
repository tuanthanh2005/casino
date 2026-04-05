<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::with('category', 'author')
            ->where('status', 'published')
            ->where('lang', app()->getLocale())
            ->latest('published_at')
            ->paginate(12);
        
        return view('blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::with('category', 'author', 'tags', 'faqs')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $related_posts = Post::with('category')
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->where('lang', app()->getLocale())
            ->take(3)
            ->get();
        
        return view('blog.show', compact('post', 'related_posts'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = Post::with('category', 'author')
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->where('lang', app()->getLocale())
            ->latest('published_at')
            ->paginate(12);
        
        return view('blog.category', compact('category', 'posts'));
    }
}
