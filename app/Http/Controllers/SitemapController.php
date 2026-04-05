<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')->latest('published_at')->get();
        $categories = Category::all();

        $content = view('sitemap', [
            'posts' => $posts,
            'categories' => $categories,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
}
