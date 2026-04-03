<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;

class SystemNotificationController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->where('role', 'user')
            ->orderBy('name')
            ->select('id', 'name', 'email')
            ->get();

        $notifications = SystemNotification::query()
            ->with(['sender:id,name', 'targetUser:id,name'])
            ->latest()
            ->paginate(30);

        return view('admin.notifications.index', compact('users', 'notifications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:180',
            'message' => 'required|string|max:5000',
            'target_type' => 'required|in:all,user',
            'target_user_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validated['target_type'] === 'user' && empty($validated['target_user_id'])) {
            return back()->withErrors(['target_user_id' => 'Vui lòng chọn người nhận khi gửi riêng.'])->withInput();
        }

        if ($validated['target_type'] === 'all') {
            $validated['target_user_id'] = null;
        }

        $validated['sent_by'] = auth()->id();

        SystemNotification::create($validated);

        return redirect()->route('admin.notifications.index')->with('success', 'Đã gửi thông báo hệ thống.');
    }
}
