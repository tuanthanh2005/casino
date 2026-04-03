<?php

namespace App\Http\Controllers;

use App\Models\GameSetting;
use App\Models\SupportChat;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    public function index()
    {
        $userId = (int) auth()->id();

        SupportChat::where('user_id', $userId)
            ->where('from_role', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = SupportChat::where('user_id', $userId)
            ->orderBy('id')
            ->take(300)
            ->get();

        $supportSettings = GameSetting::getMany([
            'support_center_label',
            'support_phone',
            'support_email',
            'support_zalo_url',
            'support_messenger_url',
            'support_working_hours',
        ]);

        $support = [
            'center_label' => $supportSettings['support_center_label'] ?? 'Trung tâm hỗ trợ MXH',
            'phone' => $supportSettings['support_phone'] ?? '0900000000',
            'email' => $supportSettings['support_email'] ?? 'support@aquahub.vn',
            'zalo_url' => $supportSettings['support_zalo_url'] ?? 'https://t.me',
            'messenger_url' => $supportSettings['support_messenger_url'] ?? 'https://m.me',
            'working_hours' => $supportSettings['support_working_hours'] ?? '08:00 - 22:00 mỗi ngày',
        ];

        return view('support.chat', compact('messages', 'support'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $msg = SupportChat::create([
            'user_id' => auth()->id(),
            'from_role' => 'user',
            'message' => trim($data['message']),
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'from_role' => $msg->from_role,
                'text' => $msg->message,
                'created_at' => $msg->created_at->format('H:i d/m'),
            ],
        ]);
    }

    public function fetch(Request $request)
    {
        $afterId = (int) $request->query('after_id', 0);

        $rows = SupportChat::where('user_id', auth()->id())
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get();

        SupportChat::where('user_id', auth()->id())
            ->where('from_role', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $rows->map(fn ($m) => [
                'id' => $m->id,
                'from_role' => $m->from_role,
                'text' => $m->message,
                'created_at' => $m->created_at->format('H:i d/m'),
            ]),
        ]);
    }
}
