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

        return response()->json(['messages' => $messages]);
    }

    public function adminIndex()
    {
        $conversations = Message::where('is_from_admin', false)
            ->where('type', 'chat')
            ->latest()
            ->get()
            ->unique(function ($item) {
                return $item->user_id ?: $item->session_id;
            });

        return view('admin.messages.index', compact('conversations'));
    }

    public function getConversation($id)
    {
        $messages = Message::where(function($query) use ($id) {
            $query->where('user_id', $id)->orWhere('session_id', $id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        Message::where('session_id', $id)->orWhere('user_id', $id)->update(['is_read' => true]);

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
}
