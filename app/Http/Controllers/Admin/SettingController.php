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

    public function adsense()
    {
        $settings = Setting::where('group', 'adsense')->pluck('value', 'key');
        return view('admin.settings.adsense', compact('settings'));
    }

    public function updateAdsense(Request $request)
    {
        $data = $request->only(['adsense_enabled', 'adsense_code']);
        
        // Handle checkbox
        Setting::set('adsense_enabled', $request->has('adsense_enabled') ? '1' : '0', 'adsense');
        Setting::set('adsense_code', $data['adsense_code'] ?? '', 'adsense');

        return back()->with('success', 'Cập nhật cài đặt Adsense thành công.');
    }
}
