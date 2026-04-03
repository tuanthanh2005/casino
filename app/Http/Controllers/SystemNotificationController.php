<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemNotificationController extends Controller
{
    public function index()
    {
        $userId = (int) auth()->id();

        $notifications = DB::table('system_notifications as n')
            ->leftJoin('system_notification_reads as r', function ($join) use ($userId) {
                $join->on('r.notification_id', '=', 'n.id')
                    ->where('r.user_id', '=', $userId);
            })
            ->leftJoin('users as s', 's.id', '=', 'n.sent_by')
            ->where(function ($q) use ($userId) {
                $q->where('n.target_type', 'all')
                    ->orWhere('n.target_user_id', $userId);
            })
            ->orderByDesc('n.id')
            ->limit(30)
            ->get([
                'n.id',
                'n.title',
                'n.message',
                'n.created_at',
                'n.target_type',
                DB::raw('CASE WHEN r.id IS NULL THEN 0 ELSE 1 END as is_read'),
                DB::raw("COALESCE(s.name, 'Admin') as sender_name"),
            ]);

        $unreadCount = DB::table('system_notifications as n')
            ->leftJoin('system_notification_reads as r', function ($join) use ($userId) {
                $join->on('r.notification_id', '=', 'n.id')
                    ->where('r.user_id', '=', $userId);
            })
            ->where(function ($q) use ($userId) {
                $q->where('n.target_type', 'all')
                    ->orWhere('n.target_user_id', $userId);
            })
            ->whereNull('r.id')
            ->count();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => (int) $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'sender_name' => $n->sender_name,
                    'target_type' => $n->target_type,
                    'is_read' => (bool) $n->is_read,
                    'created_at' => \Carbon\Carbon::parse($n->created_at)->format('H:i d/m/Y'),
                ];
            }),
            'unread_count' => (int) $unreadCount,
        ]);
    }

    public function markAllRead()
    {
        $userId = (int) auth()->id();

        $visibleIds = SystemNotification::query()
            ->where(function ($q) use ($userId) {
                $q->where('target_type', 'all')
                    ->orWhere('target_user_id', $userId);
            })
            ->pluck('id');

        if ($visibleIds->isNotEmpty()) {
            $now = now();
            $rows = $visibleIds->map(fn ($id) => [
                'notification_id' => $id,
                'user_id' => $userId,
                'read_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            DB::table('system_notification_reads')->upsert(
                $rows,
                ['notification_id', 'user_id'],
                ['read_at', 'updated_at']
            );
        }

        return response()->json(['success' => true]);
    }
}
