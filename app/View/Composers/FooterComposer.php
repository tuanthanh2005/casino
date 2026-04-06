<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Country;
use App\Models\Message;

class FooterComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $settings = Setting::where('group', 'footer')->pluck('value', 'key');
        $locale = app()->getLocale();
        
        // Dynamic content based on locale
        $settings['footer_about'] = $settings["footer_about_{$locale}"] ?? $settings['footer_about_en'] ?? '';
        
        // Unread replies from admin
        $unreadUserMessages = Message::where('is_from_admin', true)
            ->where('is_read', false)
            ->where(function($q) {
                $q->where('session_id', session()->getId());
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $q->orWhere('user_id', \Illuminate\Support\Facades\Auth::id());
                }
            })->count();

        $view->with('footer_settings', $settings);
        $view->with('footer_categories', Category::where('lang', $locale)->withCount('posts')->take(5)->get());
        $view->with('unread_user_messages', $unreadUserMessages);
        $view->with('active_countries', Country::where('status', true)->get());
    }
}
