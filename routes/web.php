<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('lang/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Legal & Static Pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::prefix('legal')->name('legal.')->group(function () {
    Route::view('/privacy', 'pages.legal.privacy')->name('privacy');
    Route::view('/disclaimer', 'pages.legal.disclaimer')->name('disclaimer');
    Route::view('/affiliate-disclosure', 'pages.legal.affiliate-disclosure')->name('affiliate');
    Route::view('/terms', 'pages.legal.terms')->name('terms');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('posts', AdminPostController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('tags', AdminTagController::class);
});

/*
|--------------------------------------------------------------------------
| SEO & Utility
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nSitemap: " . url('sitemap.xml'), 200)
        ->header('Content-Type', 'text/plain');
});
