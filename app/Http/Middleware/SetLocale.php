<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Priority: Manual session (if user manually switches)
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // 2. Automatic Detection
            $locale = $this->detectLocale($request);
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }

    protected function detectLocale(Request $request)
    {
        // Simple IP based detection (In a real app, use a local DB or a package like stevebauman/location)
        // For now, we use a simple header check as a proxy for 'International' visitors vs 'Local'
        // If the user's browser prefers VI, we show VI. 
        // If we really need IP, we could call an API here, but it adds latency.
        
        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE', 'en'), 0, 2);
        
        // If IP is from Vietnam -> 'vi'
        // Else -> 'en'
        // Since we are running locally, we check for 'vi' in header as a test
        
        return ($browserLocale === 'vi') ? 'vi' : 'en';
    }
}
