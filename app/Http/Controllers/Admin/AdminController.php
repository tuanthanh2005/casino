<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\DepositOrder;
use App\Models\ExchangeRequest;
use App\Models\GameSetting;
use App\Models\GameSession;
use App\Models\MiniGameLog;
use App\Models\User;
use App\Models\WithdrawalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_balance' => User::where('role', 'user')->sum('balance_point'),
            'total_bets_today' => Bet::whereDate('created_at', today())->count(),
            'total_sessions' => GameSession::count(),
            'pending_exchanges' => ExchangeRequest::where('status', 'pending')->count(),
            'active_sessions' => GameSession::where('status', 'pending')->count(),
        ];

        $recentBets = Bet::with(['user', 'session'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBets'));
    }

    /**
     * Quản lý Users
     */
    public function users(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->withCount('bets')->latest()->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Cộng/Trừ Point cho User
     */
    public function adjustPoints(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'action' => 'required|in:add,subtract',
            'reason' => 'required|string|max:255',
        ]);

        $amount = abs($request->amount);

        if ($request->action === 'subtract') {
            if ($user->balance_point < $amount) {
                return response()->json(['success' => false, 'message' => 'Số dư không đủ để trừ.'], 422);
            }
            $user->decrement('balance_point', $amount);
            $action = 'Trừ';
        } else {
            $user->increment('balance_point', $amount);
            $action = 'Cộng';
        }

        return response()->json([
            'success' => true,
            'message' => "{$action} {$amount} điểm cho {$user->name} thành công.",
            'new_balance' => number_format($user->fresh()->balance_point, 2),
        ]);
    }

    /**
     * Quản lý Đổi Quà
     */
    public function exchanges(Request $request)
    {
        $query = ExchangeRequest::with(['user', 'rewardItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $exchanges = $query->latest()->paginate(20);

        return view('admin.exchanges', compact('exchanges'));
    }

    /**
     * Duyệt yêu cầu đổi quà
     */
    public function approveExchange(Request $request, ExchangeRequest $exchange)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($exchange->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Yêu cầu này đã được xử lý rồi.'], 422);
        }

        $exchange->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã duyệt yêu cầu đổi quà.']);
    }

    /**
     * Từ chối yêu cầu đổi quà + hoàn điểm
     */
    public function rejectExchange(Request $request, ExchangeRequest $exchange)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($exchange->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Yêu cầu này đã được xử lý rồi.'], 422);
        }

        DB::transaction(function () use ($exchange, $request) {
            // Hoàn điểm
            $exchange->user->increment('balance_point', $exchange->points_spent);

            $exchange->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note ?? 'Yêu cầu bị từ chối.',
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Đã từ chối và hoàn điểm cho user.']);
    }

    /**
     * Quản lý Game Sessions
     */
    public function sessions()
    {
        $sessions = GameSession::withCount('bets')->latest()->paginate(20);
        return view('admin.sessions', compact('sessions'));
    }

    /**
     * Tạo session mới thủ công
     */
    public function createSession()
    {
        // Kết thúc session pending cũ
        $pending = GameSession::where('status', 'pending')->latest()->first();
        if ($pending) {
            return response()->json(['success' => false, 'message' => 'Vẫn còn phiên đang mở. Hãy chốt phiên đó trước.'], 422);
        }

        $price = $this->getBtcPrice();

        $session = GameSession::create([
            'start_time' => now(),
            'end_time' => now()->addMinutes(1),
            'start_price' => $price,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã tạo phiên #{$session->id} với giá mở {$price}.",
        ]);
    }

    /**
     * Chốt kết quả thủ công
     */
    public function resolveSession(GameSession $session)
    {
        if ($session->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Phiên này đã được chốt rồi.'], 422);
        }

        $endPrice = $this->getBtcPrice();
        $this->processSession($session, $endPrice);

        return response()->json([
            'success' => true,
            'message' => "Đã chốt phiên #{$session->id}. Giá đóng: {$endPrice}",
        ]);
    }

    /**
     * Xử lý kết quả session
     */
    private function processSession(GameSession $session, float $endPrice): void
    {
        DB::transaction(function () use ($session, $endPrice) {
            $session->update([
                'end_price' => $endPrice,
                'end_time' => now(),
                'status' => 'completed',
            ]);

            $direction = $endPrice > $session->start_price ? 'long' : 'short';

            $bets = Bet::where('session_id', $session->id)->where('status', 'pending')->with('user')->get();

            foreach ($bets as $bet) {
                if ($bet->bet_type === $direction) {
                    // Thắng: hoàn vốn + lãi x1.95
                    $profit = round($bet->bet_amount * 1.95, 2);
                    $bet->update(['status' => 'won', 'profit' => $profit]);
                    $bet->user->increment('balance_point', $profit);
                } else {
                    // Thua: mất vốn (đã trừ lúc đặt)
                    $bet->update(['status' => 'lost', 'profit' => 0]);
                }
            }
        });
    }

    /**
     * Lấy giá BTC từ Binance
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
     * Casino Stats — Vòng Quay & Tài Xỉu
     */
    public function casinoStats()
    {
        $since = now()->subHours(24);

        $buildStats = function (string $game) use ($since) {
            $base       = MiniGameLog::where('game_type', $game)->where('created_at', '>=', $since);
            $total      = (clone $base)->count();
            $wins       = (clone $base)->where('won', true)->count();
            $totalBet   = (float)(clone $base)->sum('bet_amount');
            $totalPayout= (float)(clone $base)->sum('payout');
            $winRate    = $total > 0 ? round($wins / $total * 100, 1) : 0;
            $triplets   = 0;
            if ($game === 'dice') {
                $triplets = (clone $base)->whereJsonContains('details->triplet', true)->count();
            }
            return [
                'total'        => $total,
                'win_rate'     => $winRate,
                'house_profit' => $totalBet - $totalPayout,
                'total_bet'    => $totalBet,
                'triplets'     => $triplets,
            ];
        };

        $spinStats = $buildStats('spin');
        $diceStats = $buildStats('dice');

        $totalGames    = $spinStats['total'] + $diceStats['total'];
        $houseProfit   = $spinStats['house_profit'] + $diceStats['house_profit'];
        $logsAll       = MiniGameLog::where('created_at', '>=', $since);
        $overallWinRate = $totalGames > 0
            ? round((clone $logsAll)->where('won', true)->count() / $totalGames * 100, 1)
            : 0;
        $winnersCount  = (clone $logsAll)->groupBy('user_id')
            ->havingRaw('SUM(profit) > 0')
            ->count(DB::raw('DISTINCT user_id'));

        $topQuery = fn(string $game) => MiniGameLog::with('user')
            ->where('game_type', $game)
            ->where('created_at', '>=', $since)
            ->selectRaw('user_id, COUNT(*) as total_games, SUM(won) as total_wins, ROUND(SUM(won)*100.0/COUNT(*),1) as win_rate, SUM(profit) as total_profit')
            ->groupBy('user_id')
            ->orderByDesc('total_profit')
            ->take(15)
            ->get();

        $spinTopPlayers = $topQuery('spin');
        $diceTopPlayers = $topQuery('dice');

        // Load all current settings for the config form
        $settings = GameSetting::all()->map(fn($s) => $s->value);

        return view('admin.casino', compact(
            'spinStats','diceStats','totalGames','houseProfit',
            'overallWinRate','winnersCount','spinTopPlayers','diceTopPlayers',
            'settings'
        ));
    }

    /**
     * Lưu cấu hình Casino
     */
    public function saveCasinoSettings(Request $request)
    {
        $request->validate([
            'spin_enabled'        => 'required|in:0,1',
            'spin_house_edge'     => 'required|in:0,1',
            'spin_win_rate_limit' => 'required|numeric|min:10|max:95',
            'spin_win_rate_target'=> 'required|numeric|min:5|max:70',
            'spin_max_bet'        => 'required|numeric|min:0',
            'dice_enabled'        => 'required|in:0,1',
            'dice_house_edge'     => 'required|in:0,1',
            'dice_win_rate_limit' => 'required|numeric|min:10|max:95',
            'dice_payout_mult'    => 'required|numeric|min:1.0|max:5.0',
            'dice_max_bet'        => 'required|numeric|min:0',
        ]);

        $keys = [
            'spin_enabled','spin_house_edge','spin_win_rate_limit','spin_win_rate_target','spin_max_bet',
            'dice_enabled','dice_house_edge','dice_win_rate_limit','dice_payout_mult','dice_max_bet',
        ];
        foreach ($keys as $key) {
            GameSetting::set($key, $request->input($key));
        }

        return response()->json(['success' => true, 'message' => 'Đã lưu cấu hình Casino thành công!']);
    }

    // ═══════════════════════════════════════
    // QUẢN LÝ NẠP TIỀN
    // ═══════════════════════════════════════

    public function deposits()
    {
        $deposits = DepositOrder::with('user')->latest()->paginate(30);
        $stats = [
            'pending'  => DepositOrder::where('status','pending')->count(),
            'today'    => DepositOrder::where('status','approved')->whereDate('approved_at', today())->sum('points_credited'),
            'total'    => DepositOrder::where('status','approved')->sum('points_credited'),
        ];
        return view('admin.deposits', compact('deposits','stats'));
    }

    public function approveDeposit(Request $request, DepositOrder $deposit)
    {
        if ($deposit->status !== 'pending') {
            return response()->json(['success'=>false,'message'=>'Đơn này đã được xử lý rồi.'],422);
        }
        $points = (float)$deposit->amount; // 1 VNĐ = 1 PT
        DB::transaction(function () use ($deposit, $points, $request) {
            $deposit->update([
                'status'           => 'approved',
                'points_credited'  => $points,
                'admin_note'       => $request->input('admin_note'),
                'approved_by'      => auth()->id(),
                'approved_at'      => now(),
            ]);
            $deposit->user->increment('balance_point', $points);
        });
        return response()->json(['success'=>true,'message'=>"Đã duyệt +{$points} PT cho {$deposit->user->name}."]);
    }

    public function rejectDeposit(Request $request, DepositOrder $deposit)
    {
        if ($deposit->status !== 'pending') {
            return response()->json(['success'=>false,'message'=>'Đơn này đã được xử lý rồi.'],422);
        }
        $deposit->update([
            'status'     => 'rejected',
            'admin_note' => $request->input('admin_note','Đơn bị từ chối.'),
            'approved_by'=> auth()->id(),
        ]);
        return response()->json(['success'=>true,'message'=>'Đã từ chối đơn nạp tiền.']);
    }

    // ═══════════════════════════════════════
    // QUẢN LÝ RÚT TIỀN
    // ═══════════════════════════════════════

    public function withdrawals()
    {
        $withdrawals = WithdrawalOrder::with('user')->latest()->paginate(30);
        $stats = [
            'pending'       => WithdrawalOrder::where('status','pending')->count(),
            'pending_pts'   => WithdrawalOrder::where('status','pending')->sum('points_used'),
            'paid_today'    => WithdrawalOrder::where('status','approved')->whereDate('approved_at',today())->sum('net_amount'),
        ];
        return view('admin.withdrawals', compact('withdrawals','stats'));
    }

    public function approveWithdrawal(Request $request, WithdrawalOrder $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success'=>false,'message'=>'Đơn này đã được xử lý rồi.'],422);
        }
        $withdrawal->update([
            'status'     => 'approved',
            'admin_note' => $request->input('admin_note'),
            'approved_by'=> auth()->id(),
            'approved_at'=> now(),
        ]);
        // Điểm đã bị trừ lúc tạo đơn rồi, chỉ cần cập nhật trạng thái
        return response()->json(['success'=>true,'message'=>"Đã xác nhận hoàn thành đơn rút {$withdrawal->order_code}."]);
    }

    public function rejectWithdrawal(Request $request, WithdrawalOrder $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success'=>false,'message'=>'Đơn này đã được xử lý rồi.'],422);
        }
        // Hoàn điểm lại vì đã trừ lúc tạo đơn
        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->user->increment('balance_point', (float)$withdrawal->points_used);
            $withdrawal->update([
                'status'     => 'rejected',
                'admin_note' => $request->input('admin_note','Đơn bị từ chối, điểm đã hoàn về.'),
                'approved_by'=> auth()->id(),
            ]);
        });
        return response()->json(['success'=>true,'message'=>"Đã từ chối và hoàn {$withdrawal->points_used} PT về tài khoản."]);
    }
}
