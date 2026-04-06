<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featured_posts = Post::with('category')
            ->where('is_featured', true)
            ->where('status', 'published')
            ->where('lang', app()->getLocale())
            ->latest('published_at')
            ->take(3)
            ->get();

        $latest_posts = Post::with('category')
            ->where('status', 'published')
            ->where('lang', app()->getLocale())
            ->latest('published_at')
            ->take(6)
            ->get();

        $categories = Category::withCount('posts')
            ->where('lang', app()->getLocale())
            ->take(4)
            ->get();
# Add fallback for categories if current lang is empty
if ($categories->isEmpty()) {
    $categories = Category::withCount('posts')->take(4)->get();
}

        $review_slug = app()->getLocale() == 'vi' ? 'danh-gia-san-pham' : 'product-reviews';
        $review_category = Category::where('slug', $review_slug)->where('lang', app()->getLocale())->first();
        $reviews = $review_category ? 
            Post::where('category_id', $review_category->id)
                ->where('status', 'published')
                ->where('lang', app()->getLocale())
                ->latest('published_at')
                ->take(4)
                ->get() : collect();

        return view('welcome', compact('featured_posts', 'latest_posts', 'categories', 'reviews'));
    }
}
