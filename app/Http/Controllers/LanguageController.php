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
            \Illuminate\Support\Facades\Session::put('locale', $effectiveLocale);
            app()->setLocale($effectiveLocale);
        }
        return redirect()->back();
    }
}
