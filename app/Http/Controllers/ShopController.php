<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRequest;
use App\Models\RewardItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    /**
     * Trang shop
     */
    public function index()
    {
        $items = RewardItem::where('status', 'active')->get();
        $myRequests = ExchangeRequest::with('rewardItem')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        return view('shop.index', compact('items', 'myRequests'));
    }

    /**
     * Đổi quà
     */
    public function exchange(Request $request, RewardItem $item)
    {
        if ($item->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không còn khả dụng.'], 422);
        }

        $user = auth()->user();

        if ($user->balance_point < $item->point_price) {
            return response()->json([
                'success' => false,
                'message' => 'Số dư Point không đủ. Cần ' . number_format($item->point_price, 0) . ' điểm.',
            ], 422);
        }

        // Kiểm tra request pending
        $pending = ExchangeRequest::where('user_id', $user->id)
            ->where('reward_item_id', $item->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return response()->json(['success' => false, 'message' => 'Bạn đã có yêu cầu đổi quà này đang chờ duyệt.'], 422);
        }

        DB::transaction(function () use ($user, $item) {
            $user->decrement('balance_point', $item->point_price);

            ExchangeRequest::create([
                'user_id' => $user->id,
                'reward_item_id' => $item->id,
                'points_spent' => $item->point_price,
                'status' => 'pending',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu đổi quà đã được gửi! Admin sẽ xử lý sớm.',
            'new_balance' => number_format($user->fresh()->balance_point, 2),
        ]);
    }
}
