<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportChatAdminController extends Controller
{
    public function index(Request $request)
    {
        $threadRows = SupportChat::select('user_id', DB::raw('MAX(created_at) as last_at'))
            ->groupBy('user_id')
            ->orderByDesc('last_at')
            ->get();

        $userIds = $threadRows->pluck('user_id')->values();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $selectedUserId = (int) $request->query('user_id', $userIds->first() ?? 0);
        $selectedUser = $selectedUserId ? ($users[$selectedUserId] ?? User::find($selectedUserId)) : null;

        if ($selectedUserId > 0) {
            SupportChat::where('user_id', $selectedUserId)
                ->where('from_role', 'user')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $unreadMap = SupportChat::select('user_id', DB::raw('COUNT(*) as c'))
            ->where('from_role', 'user')
            ->where('is_read', false)
            ->groupBy('user_id')
            ->pluck('c', 'user_id');

        $threads = $threadRows->map(function ($row) use ($users, $unreadMap) {
            $u = $users[$row->user_id] ?? null;
            return [
                'user_id' => (int) $row->user_id,
                'name' => $u?->name ?? ('User #' . $row->user_id),
                'email' => $u?->email,
                'last_at' => $row->last_at,
                'unread' => (int) ($unreadMap[$row->user_id] ?? 0),
            ];
        });

        $messages = collect();
        if ($selectedUserId > 0) {
            $messages = SupportChat::where('user_id', $selectedUserId)
                ->orderBy('id')
                ->take(500)
                ->get();
        }

        return view('admin.support-chat', compact('threads', 'selectedUser', 'selectedUserId', 'messages'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $msg = SupportChat::create([
            'user_id' => (int) $data['user_id'],
            'admin_id' => auth()->id(),
            'from_role' => 'admin',
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

    public function fetch(Request $request, User $user)
    {
        $afterId = (int) $request->query('after_id', 0);

        $rows = SupportChat::where('user_id', $user->id)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get();

        SupportChat::where('user_id', $user->id)
            ->where('from_role', 'user')
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
