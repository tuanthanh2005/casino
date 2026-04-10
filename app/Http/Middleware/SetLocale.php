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
        $locale = $this->determineLocale($request);

        if ($locale) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    protected function determineLocale(Request $request)
    {
        // 1. Ngôn ngữ đã chọn trước đó trong session
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 2. Ngôn ngữ của nội dung đang vào trực tiếp
        $contentLocale = $this->getContentLocale($request);
        if ($contentLocale) {
            return $contentLocale;
        }

        // 3. Ngôn ngữ theo quốc gia từ Cloudflare
        $countryLocale = $this->getCountryLocale($request);
        if ($countryLocale) {
            return $countryLocale;
        }

        // 4. Ngôn ngữ mặc định của domain
        return config('app.locale', 'en');
    }

    protected function getContentLocale(Request $request)
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        $routeName = $route->getName();
        $slug = $route->parameter('slug');

        if ($slug) {
            if ($routeName === 'blog.show') {
                $item = \App\Models\Post::where('slug', $slug)->first();
                return $item ? $item->lang : null;
            } elseif ($routeName === 'blog.category') {
                $item = \App\Models\Category::where('slug', $slug)->first();
                return $item ? $item->lang : null;
            } elseif ($routeName === 'page.show') {
                $item = \App\Models\Page::where('slug', $slug)->first();
                return $item ? $item->lang : null;
            }
        }

        return null;
    }

    protected function getCountryLocale(Request $request)
    {
        $countryCode = $request->header('CF-IPCountry');
        
        if ($countryCode) {
            if ($countryCode === 'VN') {
                return 'vi';
            }
            return 'en';
        }

        return null;
    }
}
