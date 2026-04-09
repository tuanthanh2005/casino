<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
                    ->where('is_active', true)
                    ->where('lang', app()->getLocale())
                    ->firstOrFail();

        return view('pages.dynamic', compact('page'));
    }
}
