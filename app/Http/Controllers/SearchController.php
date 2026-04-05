<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $posts = collect();

        if ($query) {
            $posts = Post::with('category')
                ->where('status', 'published')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%");
                })
                ->latest('published_at')
                ->paginate(20)
                ->withQueryString();
        }

        return view('search.index', compact('posts', 'query'));
    }
}
