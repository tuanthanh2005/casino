<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'guest_name' => 'nullable|string',
            'guest_email' => 'nullable|string',
        ]);

        $sessionId = session()->getId();

        Message::create([
            'user_id' => Auth::id(),
            'session_id' => $sessionId,
            'message' => $request->message,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'is_from_admin' => Auth::check() && Auth::user()->is_admin,
            'type' => 'chat',
        ]);

        return response()->json(['success' => true]);
    }

    public function getMessages()
    {
        $sessionId = session()->getId();
        $messages = Message::where(function($query) use ($sessionId) {
            $query->where('session_id', $sessionId);
            if (Auth::check()) {
                $query->orWhere('user_id', Auth::id());
            }
        })
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark as read
        Message::where('is_from_admin', true)
            ->where('is_read', false)
            ->where(function($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $q->orWhere('user_id', \Illuminate\Support\Facades\Auth::id());
                }
            })
            ->update(['is_read' => true]);

        return response()->json(['messages' => $messages]);
    }

    public function adminIndex()
    {
        // Get unique conversations based on user_id (if exists) or session_id (for guests)
        $conversations = Message::where('is_from_admin', false)
            ->where('type', 'chat')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) {
                return $item->user_id ?: $item->session_id;
            });

        return view('admin.messages.index', compact('conversations'));
    }

    public function getConversation($id)
    {
        $messages = Message::where(function($query) use ($id) {
                if (is_numeric($id)) {
                    $query->where('user_id', $id);
                } else {
                    $query->where('session_id', $id);
                }
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Message::where(function($query) use ($id) {
                if (is_numeric($id)) {
                    $query->where('user_id', $id);
                } else {
                    $query->where('session_id', $id);
                }
            })
            ->where('is_from_admin', false)
            ->update(['is_read' => true]);

        return response()->json(['messages' => $messages]);
    }

    public function adminSend(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'to_id' => 'required',
        ]);

        $isUserId = is_numeric($request->to_id);

        Message::create([
            'user_id' => $isUserId ? $request->to_id : null,
            'session_id' => $isUserId ? null : $request->to_id,
            'message' => $request->message,
            'is_from_admin' => true,
            'type' => 'chat',
        ]);

        return response()->json(['success' => true]);
    }
    public function getUnreadCount()
    {
        $sessionId = session()->getId();
        $count = Message::where('is_from_admin', true)
            ->where('is_read', false)
            ->where(function($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $query->orWhere('user_id', \Illuminate\Support\Facades\Auth::id());
                }
            })->count();

        return response()->json(['count' => $count]);
    }
}
