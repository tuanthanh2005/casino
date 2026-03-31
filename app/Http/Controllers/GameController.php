<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Trang chủ - hiển thị game chính
     */
    public function index()
    {
        $user = auth()->user();

        // Dọn dẹp session rỗng cũ khi load trang
        $this->cleanupEmptySessions();

        // Lấy hoặc tạo session đang pending
        $activeSession = GameSession::where('status', 'pending')->latest()->first();

        if (!$activeSession) {
            $activeSession = $this->createNewSession();
        }

        // Lịch sử cược của user
        $myBets = Bet::with('session')
            ->where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        // Chỉ lấy 5 phiên gần nhất CÓ ít nhất 1 cược
        $completedSessions = GameSession::where('status', 'completed')
            ->has('bets')           // chỉ phiên có cược
            ->withCount('bets')
            ->latest()
            ->take(5)
            ->get();

        return view('game.index', compact('user', 'activeSession', 'myBets', 'completedSessions'));
    }

    /**
     * AJAX: Đặt cược
     */
    public function placeBet(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:game_sessions,id',
            'bet_type' => 'required|in:long,short',
            'bet_amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        $session = GameSession::findOrFail($request->session_id);

        if ($session->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Phiên cược này đã kết thúc.'], 422);
        }

        if ($user->balance_point < $request->bet_amount) {
            return response()->json(['success' => false, 'message' => 'Số dư Point không đủ.'], 422);
        }

        // Kiểm tra đã cược chưa trong phiên này
        $existingBet = Bet::where('user_id', $user->id)
            ->where('session_id', $session->id)
            ->first();

        if ($existingBet) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đặt cược trong phiên này rồi.'], 422);
        }

        DB::transaction(function () use ($user, $request, $session) {
            // Trừ point ngay lập tức
            $user->decrement('balance_point', $request->bet_amount);

            // Lưu bet
            Bet::create([
                'user_id' => $user->id,
                'session_id' => $session->id,
                'bet_type' => $request->bet_type,
                'bet_amount' => $request->bet_amount,
                'status' => 'pending',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Đặt cược thành công! Chờ kết quả...',
            'new_balance' => number_format($user->fresh()->balance_point, 2),
        ]);
    }

    /**
     * AJAX: Lấy lịch sử cược của user
     */
    public function myBets()
    {
        $bets = Bet::with('session')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($bet) {
                return [
                    'id' => $bet->id,
                    'bet_type' => $bet->bet_type,
                    'bet_type_label' => $bet->bet_type === 'long' ? '▲ LONG' : '▼ SHORT',
                    'bet_amount' => number_format($bet->bet_amount, 2),
                    'status' => $bet->status,
                    'profit' => $bet->profit ? number_format($bet->profit, 2) : null,
                    'created_at' => $bet->created_at->format('d/m H:i'),
                    'session_id' => $bet->session_id,
                    'start_price' => $bet->session ? number_format($bet->session->start_price, 2) : null,
                    'end_price' => $bet->session ? number_format($bet->session->end_price, 2) : null,
                ];
            });

        return response()->json(['bets' => $bets, 'balance' => number_format(auth()->user()->balance_point, 2)]);
    }

    /**
     * Tạo phiên game mới
     */
    private function createNewSession(): GameSession
    {
        // Lấy giá từ Binance
        $price = $this->getBtcPrice();
        
        return GameSession::create([
            'start_time' => now(),
            'end_time' => now()->addMinutes(1),
            'start_price' => $price,
            'status' => 'pending',
        ]);
    }

    /**
     * Lấy giá BTC từ Binance API (backend)
     */
    private function getBtcPrice(): float
    {
        try {
            $ch = curl_init('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
            return (float) ($data['price'] ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * AJAX: Lấy thông tin session hiện tại
     * Tự động chốt kết quả nếu phiên đã hết giờ → tạo phiên mới luôn.
     */
    public function currentSession()
    {
        // Tự chốt các phiên pending đã hết giờ
        $expiredSessions = GameSession::where('status', 'pending')
            ->where('end_time', '<=', now())
            ->get();

        foreach ($expiredSessions as $expired) {
            $this->resolveSession($expired);
        }

        // Dọn dẹp session rỗng (0 cược) sau 24h
        $this->cleanupEmptySessions();

        // Lấy phiên đang mở (hoặc tạo mới nếu không có)
        $session = GameSession::where('status', 'pending')->latest()->first();

        if (!$session) {
            $session = $this->createNewSession();
        }

        $userBet = null;
        if (auth()->check()) {
            $userBet = Bet::where('user_id', auth()->id())
                ->where('session_id', $session->id)
                ->first();
        }

        return response()->json([
            'session' => [
                'id'          => $session->id,
                'start_price' => $session->start_price,
                'end_time'    => $session->end_time,
                'status'      => $session->status,
            ],
            'user_bet' => $userBet ? [
                'bet_type'   => $userBet->bet_type,
                'bet_amount' => $userBet->bet_amount,
            ] : null,
        ]);
    }

    /**
     * Xóa các session đã hoàn tất mà không có cược nào, tồn tại quá 24h.
     * Giữ lại phiên hiện tại (pending) và phiên có cược.
     */
    private function cleanupEmptySessions(): void
    {
        GameSession::where('status', 'completed')
            ->where('end_time', '<', now()->subHours(24))
            ->doesntHave('bets')
            ->delete();
    }

    /**
     * Chốt kết quả một phiên (dùng chung cho auto-resolve và admin manual)
     */
    private function resolveSession(GameSession $session): void
    {
        if ($session->status !== 'pending') {
            return;
        }

        $endPrice = $this->getBtcPrice();

        // Nếu không lấy được giá, dùng start_price (hòa, để tránh crash)
        if ($endPrice <= 0) {
            $endPrice = (float) $session->start_price;
        }

        DB::transaction(function () use ($session, $endPrice) {
            $session->update([
                'end_price' => $endPrice,
                'end_time'  => now(),
                'status'    => 'completed',
            ]);

            $direction = $endPrice > (float) $session->start_price ? 'long' : 'short';

            $bets = Bet::where('session_id', $session->id)
                ->where('status', 'pending')
                ->with('user')
                ->get();

            foreach ($bets as $bet) {
                if ($bet->bet_type === $direction) {
                    // Thắng: hoàn vốn + x1.95
                    $profit = round((float) $bet->bet_amount * 1.95, 2);
                    $bet->update(['status' => 'won', 'profit' => $profit]);
                    $bet->user->increment('balance_point', $profit);
                } else {
                    // Thua: mất vốn (đã trừ lúc đặt)
                    $bet->update(['status' => 'lost', 'profit' => 0]);
                }
            }
        });
    }
}
