<?php

namespace App\Http\Controllers;

use App\Models\GameSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private const REGISTER_BONUS_POINTS = 100;

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->withInput($request->only('email'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $registerBonusEnabled = (string) GameSetting::get('register_bonus_enabled', '1') === '1';
        $registerBonusPoints = max(0, (float) GameSetting::get('register_bonus_points', (string) self::REGISTER_BONUS_POINTS));
        $initialBalance = $registerBonusEnabled ? $registerBonusPoints : 0;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'balance_point' => $initialBalance,
        ]);

        Auth::login($user);

        $message = 'Đăng ký thành công! Chào mừng bạn đến với AquaHub.';
        if ($registerBonusEnabled && $registerBonusPoints > 0) {
            $message = 'Đăng ký thành công! Bạn đã nhận ' . number_format($registerBonusPoints, 0) . ' PT khởi đầu.';
        }

        return redirect()->route('home')->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
