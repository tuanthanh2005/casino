<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function footer()
    {
        $settings = Setting::where('group', 'footer')->pluck('value', 'key');
        return view('admin.settings.footer', compact('settings'));
    }

    public function updateFooter(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'footer');
        }

        return back()->with('success', 'Footer settings updated successfully.');
    }
}
