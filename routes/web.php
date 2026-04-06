<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Global & Multi-Language
|--------------------------------------------------------------------------
*/
Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Legal & Static
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
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.manual_reset');
});
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Support & Chat (Public/Guest)
|--------------------------------------------------------------------------
*/
Route::get('chat/messages', [ChatController::class, 'getMessages'])->name('chat.get');
Route::get('chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread');
Route::post('chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::get('profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile')->middleware('auth');
Route::post('profile', [App\Http\Controllers\ProfileController::class, 'update'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Administrative Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('posts', AdminPostController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('tags', AdminTagController::class);

    // Country Management (Flags/Icons)
    Route::resource('countries', \App\Http\Controllers\Admin\CountryController::class);

    // Admin Chat & Support
    Route::get('/messages', [ChatController::class, 'adminIndex'])->name('messages.index');
    Route::get('/messages/{id}', [ChatController::class, 'getConversation'])->name('messages.show');
    Route::post('/messages/send', [ChatController::class, 'adminSend'])->name('messages.send');

    // Site Settings (Footer)
    Route::get('/settings/footer', [\App\Http\Controllers\Admin\SettingController::class, 'footer'])->name('settings.footer');
    Route::post('/settings/footer', [\App\Http\Controllers\Admin\SettingController::class, 'updateFooter']);
});

/*
|--------------------------------------------------------------------------
| SEO & Technical
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nSitemap: " . url('sitemap.xml'), 200)
        ->header('Content-Type', 'text/plain');
});
