<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        $locale = strtolower($locale);
        $availableLocales = ['en', 'vi', 'vn', 'us', 'uk'];
        
        if (in_array($locale, $availableLocales)) {
            $effectiveLocale = in_array($locale, ['vi', 'vn']) ? 'vi' : 'en';
            
            // Try to find if user was on a category page to redirect them to the translated slug
            $previousUrl = url()->previous();
            $urlParts = explode('/category/', $previousUrl);
            
            \Illuminate\Support\Facades\Session::put('locale', $effectiveLocale);
            app()->setLocale($effectiveLocale);

            if (count($urlParts) > 1) {
                $currentSlug = $urlParts[1];
                $category = \App\Models\Category::where('slug', $currentSlug)->first();
                
                if ($category && $category->ref_key) {
                    $translatedCategory = \App\Models\Category::where('ref_key', $category->ref_key)
                        ->where('lang', $effectiveLocale)
                        ->first();
                    
                    if ($translatedCategory) {
                        return redirect()->to(url('/category/' . $translatedCategory->slug));
                    }
                }
            }
        }
        
        return redirect()->back();
    }
}
