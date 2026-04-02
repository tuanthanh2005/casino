<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Cập nhật thông tin người dùng (Tên và Mật khẩu)
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'name.required' => 'Vui lòng nhập tên hiển thị.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user->name = $validated['name'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        /** @var \App\Models\User $user */
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin tài khoản thành công!',
            'user'    => [
                'name' => $user->name,
            ]
        ]);
    }
}
