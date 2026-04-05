<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'total_categories' => Category::count(),
            'featured_posts' => Post::where('is_featured', true)->count(),
        ];

        $latest_posts = Post::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latest_posts'));
    }
}
