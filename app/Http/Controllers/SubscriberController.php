<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index()
    {
        return view('newsletter');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $existing = Subscriber::where('email', $request->email)->first();

        if ($existing) {
            return back()->with('error', __('This email is already registered!'));
        }

        Subscriber::create([
            'email' => $request->email,
            'status' => 'active'
        ]);

        return back()->with('success', __('You have successfully subscribed to our newsletter! Welcome aboard.'));
    }
}
