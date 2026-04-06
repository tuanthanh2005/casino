<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // For User frontend
        \Illuminate\Support\Facades\View::composer('layouts.app', \App\View\Composers\FooterComposer::class);

        // For Admin sidebar
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $count = \App\Models\Message::where('is_from_admin', false)->where('is_read', false)->count();
            $view->with('unread_admin_messages', $count);
        });
    }
}
