<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppSettingsController extends Controller
{
    /**
     * Danh sách setting keys cho App Download
     */
    private array $settingKeys = [
        'app_download_enabled',
        'app_android_url',
        'app_ios_url',
        'app_android_icon',
        'app_ios_icon',
        'app_android_label',
        'app_ios_label',
        'app_banner_title',
        'app_banner_subtitle',
    ];

    private array $defaults = [
        'app_download_enabled' => '1',
        'app_android_url'      => '#',
        'app_ios_url'          => '#',
        'app_android_icon'     => '',
        'app_ios_icon'         => '',
        'app_android_label'    => 'Tải cho Android',
        'app_ios_label'        => 'Tải cho iOS',
        'app_banner_title'     => 'Tải App AquaHub',
        'app_banner_subtitle'  => 'Trải nghiệm tốt hơn với ứng dụng di động',
    ];

    public function index()
    {
        $settings = GameSetting::getMany($this->settingKeys);
        $settings = array_merge($this->defaults, $settings);

        return view('admin.app-settings', compact('settings'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'app_download_enabled' => 'nullable|in:0,1',
            'app_android_url'      => 'nullable|string|max:500',
            'app_ios_url'          => 'nullable|string|max:500',
            'app_android_label'    => 'nullable|string|max:100',
            'app_ios_label'        => 'nullable|string|max:100',
            'app_banner_title'     => 'nullable|string|max:150',
            'app_banner_subtitle'  => 'nullable|string|max:255',
            'app_android_icon'     => 'nullable|image|mimes:png,jpg,jpeg,webp,gif|max:2048',
            'app_ios_icon'         => 'nullable|image|mimes:png,jpg,jpeg,webp,gif|max:2048',
        ]);

        // Xử lý upload icon Android
        if ($request->hasFile('app_android_icon') && $request->file('app_android_icon')->isValid()) {
            $file = $request->file('app_android_icon');
            $filename = 'app_android_icon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/app-icons'), $filename);
            GameSetting::set('app_android_icon', 'uploads/app-icons/' . $filename);
        }

        // Xử lý upload icon iOS
        if ($request->hasFile('app_ios_icon') && $request->file('app_ios_icon')->isValid()) {
            $file = $request->file('app_ios_icon');
            $filename = 'app_ios_icon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/app-icons'), $filename);
            GameSetting::set('app_ios_icon', 'uploads/app-icons/' . $filename);
        }

        // Lưu các settings còn lại
        $textKeys = [
            'app_download_enabled',
            'app_android_url',
            'app_ios_url',
            'app_android_label',
            'app_ios_label',
            'app_banner_title',
            'app_banner_subtitle',
        ];

        foreach ($textKeys as $key) {
            if ($key === 'app_download_enabled') {
                GameSetting::set($key, $request->input($key, '0'));
            } else {
                GameSetting::set($key, $request->input($key, ''));
            }
        }

        return redirect()
            ->route('admin.app-settings')
            ->with('success', 'Đã lưu cài đặt App Download thành công!');
    }

    /**
     * Xóa icon theo platform (android|ios)
     */
    public function removeIcon(string $platform)
    {
        $key = match($platform) {
            'android' => 'app_android_icon',
            'ios'     => 'app_ios_icon',
            default   => null,
        };

        if (!$key) {
            return response()->json(['success' => false, 'message' => 'Platform không hợp lệ.'], 422);
        }

        $current = GameSetting::get($key, '');
        if ($current && file_exists(public_path($current))) {
            @unlink(public_path($current));
        }
        GameSetting::set($key, '');

        $label = $platform === 'android' ? 'Android' : 'iOS';
        return response()->json(['success' => true, 'message' => "Đã xóa icon {$label}."]);
    }
}
