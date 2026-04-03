<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\DepositOrder;
use App\Models\ExchangeRequest;
use App\Models\FarmTransaction;
use App\Models\GameSetting;
use App\Models\GameSession;
use App\Models\MiniGameLog;
use App\Models\NavOrder;
use App\Models\User;
use App\Models\WithdrawalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


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
     * Lịch sử giao dịch toàn hệ thống cho admin đối soát khi khách báo lỗi.
     */
    public function systemHistory(Request $request)
    {
        $filters = [
            'user_id' => (int) $request->query('user_id', 0),
            'source' => (string) $request->query('source', ''),
            'status' => (string) $request->query('status', ''),
            'from' => (string) $request->query('from', ''),
            'to' => (string) $request->query('to', ''),
            'q' => trim((string) $request->query('q', '')),
        ];

        $predictionLogs = DB::table('bets as b')
            ->leftJoin('users as u', 'u.id', '=', 'b.user_id')
            ->selectRaw("
                b.created_at as occurred_at,
                'prediction' as source,
                CONCAT('BTC ', UPPER(b.bet_type)) as action,
                b.user_id as user_id,
                COALESCE(u.name, CONCAT('User #', b.user_id)) as user_name,
                b.profit as amount_pt,
                NULL as amount_vnd,
                b.status as status,
                CONCAT('BET#', b.id) as reference,
                CONCAT('Dat ', b.bet_amount, ' PT') as note
            ");

        $miniGameLogs = DB::table('mini_game_logs as m')
            ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
            ->selectRaw("
                m.created_at as occurred_at,
                CONCAT('minigame_', m.game_type) as source,
                CASE
                    WHEN m.game_type = 'spin' THEN 'Vong quay'
                    WHEN m.game_type = 'dice' THEN 'Tai xiu'
                    WHEN m.game_type = 'rps' THEN 'Keo bua bao'
                    ELSE m.game_type
                END as action,
                m.user_id as user_id,
                COALESCE(u.name, CONCAT('User #', m.user_id)) as user_name,
                m.profit as amount_pt,
                NULL as amount_vnd,
                CASE WHEN m.won = 1 THEN 'won' ELSE 'lost' END as status,
                CONCAT('MGL#', m.id) as reference,
                CONCAT('Dat ', m.bet_amount, ' PT, tra ', m.payout, ' PT') as note
            ");

        $depositLogs = DB::table('deposit_orders as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.user_id')
            ->selectRaw("
                d.created_at as occurred_at,
                'deposit' as source,
                CASE
                    WHEN d.method = 'bank_qr' THEN 'Nap QR Bank'
                    WHEN d.method = 'card' THEN 'Nap the cao'
                    ELSE 'Nap tien'
                END as action,
                d.user_id as user_id,
                COALESCE(u.name, CONCAT('User #', d.user_id)) as user_name,
                CASE WHEN d.status = 'approved' THEN d.points_credited ELSE 0 END as amount_pt,
                d.amount as amount_vnd,
                d.status as status,
                d.order_code as reference,
                COALESCE(d.admin_note, '') as note
            ");

        $withdrawalLogs = DB::table('withdrawal_orders as w')
            ->leftJoin('users as u', 'u.id', '=', 'w.user_id')
            ->selectRaw("
                w.created_at as occurred_at,
                'withdrawal' as source,
                CASE
                    WHEN w.method = 'bank_transfer' THEN 'Rut chuyen khoan'
                    WHEN w.method = 'card' THEN 'Doi the cao'
                    ELSE 'Rut tien'
                END as action,
                w.user_id as user_id,
                COALESCE(u.name, CONCAT('User #', w.user_id)) as user_name,
                (0 - w.points_used) as amount_pt,
                w.net_amount as amount_vnd,
                w.status as status,
                w.order_code as reference,
                COALESCE(w.admin_note, '') as note
            ");

        $exchangeLogs = DB::table('exchange_requests as e')
            ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
            ->leftJoin('reward_items as r', 'r.id', '=', 'e.reward_item_id')
            ->selectRaw("
                e.created_at as occurred_at,
                'shop_exchange' as source,
                'Doi qua' as action,
                e.user_id as user_id,
                COALESCE(u.name, CONCAT('User #', e.user_id)) as user_name,
                (0 - e.points_spent) as amount_pt,
                NULL as amount_vnd,
                e.status as status,
                CONCAT('EXC#', e.id) as reference,
                CONCAT('Vat pham: ', COALESCE(r.name, 'N/A')) as note
            ");

        $farmLogs = DB::table('farm_transactions as f')
            ->leftJoin('users as u', 'u.id', '=', 'f.user_id')
            ->selectRaw("
                f.created_at as occurred_at,
                'farm' as source,
                f.type as action,
                f.user_id as user_id,
                COALESCE(u.name, CONCAT('User #', f.user_id)) as user_name,
                CASE
                    WHEN f.type = 'buy_seed' THEN (0 - f.total_pt)
                    WHEN f.type = 'sell_fruit' THEN f.total_pt
                    ELSE 0
                END as amount_pt,
                NULL as amount_vnd,
                'done' as status,
                CONCAT('FARM#', f.id) as reference,
                COALESCE(f.note, '') as note
            ");

        $navLogs = DB::table('nav_orders as n')
            ->leftJoin('users as u', 'u.id', '=', 'n.user_id')
            ->selectRaw("
                n.created_at as occurred_at,
                'nav' as source,
                'Don ho tro MXH' as action,
                n.user_id as user_id,
                COALESCE(u.name, CONCAT('User #', n.user_id)) as user_name,
                CASE WHEN n.payment_method = 'points' THEN (0 - n.amount) ELSE 0 END as amount_pt,
                CASE WHEN n.payment_method = 'points' THEN NULL ELSE n.amount END as amount_vnd,
                n.status as status,
                n.order_code as reference,
                CONCAT('Thanh toan: ', n.payment_method) as note
            ");

        $union = $predictionLogs
            ->unionAll($miniGameLogs)
            ->unionAll($depositLogs)
            ->unionAll($withdrawalLogs)
            ->unionAll($exchangeLogs)
            ->unionAll($farmLogs)
            ->unionAll($navLogs);

        $historyQuery = DB::query()->fromSub($union, 'h');

        if ($filters['user_id'] > 0) {
            $historyQuery->where('h.user_id', $filters['user_id']);
        }
        if ($filters['source'] !== '') {
            $historyQuery->where('h.source', $filters['source']);
        }
        if ($filters['status'] !== '') {
            $historyQuery->where('h.status', $filters['status']);
        }
        if ($filters['from'] !== '') {
            $historyQuery->whereDate('h.occurred_at', '>=', $filters['from']);
        }
        if ($filters['to'] !== '') {
            $historyQuery->whereDate('h.occurred_at', '<=', $filters['to']);
        }
        if ($filters['q'] !== '') {
            $kw = '%' . $filters['q'] . '%';
            $historyQuery->where(function ($q) use ($kw) {
                $q->where('h.user_name', 'like', $kw)
                    ->orWhere('h.reference', 'like', $kw)
                    ->orWhere('h.action', 'like', $kw)
                    ->orWhere('h.note', 'like', $kw);
            });
        }

        $summary = [
            'total_rows' => (clone $historyQuery)->count(),
            'sum_pt' => (float) ((clone $historyQuery)->sum('h.amount_pt') ?? 0),
            'sum_vnd' => (float) ((clone $historyQuery)->sum('h.amount_vnd') ?? 0),
        ];

        $logs = (clone $historyQuery)
            ->orderByDesc('h.occurred_at')
            ->paginate(50)
            ->withQueryString();

        $users = User::query()
            ->where('role', 'user')
            ->orderBy('name')
            ->select('id', 'name', 'email')
            ->limit(500)
            ->get();

        $sources = [
            'prediction' => 'BTC Prediction',
            'minigame_spin' => 'Mini game: Vòng quay',
            'minigame_dice' => 'Mini game: Tài xỉu',
            'minigame_rps' => 'Mini game: Kéo búa bao',
            'deposit' => 'Nạp tiền',
            'withdrawal' => 'Rút tiền',
            'shop_exchange' => 'Đổi quà',
            'farm' => 'Nông trại',
            'nav' => 'Hỗ trợ MXH',
        ];

        return view('admin.system-history', compact('logs', 'summary', 'users', 'sources', 'filters'));
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
     * Reset mật khẩu cho User ngẫu nhiên
     */
    public function resetPassword(User $user)
    {
        $newPassword = Str::random(10); // Tạo 10 ký tự ngẫu nhiên
        
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return response()->json([
            'success'  => true,
            'message'  => "Đã reset mật khẩu cho {$user->name} thành công!",
            'password' => $newPassword,
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
     * Casino Stats — Vòng Quay, Tài Xỉu, Kéo Búa Bao
     */
    public function casinoStats()
    {
        $since = now()->subHours(24);
        $monthStart = now()->startOfMonth();

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
        $rpsStats = $buildStats('rps');

        $totalGames    = $spinStats['total'] + $diceStats['total'] + $rpsStats['total'];
        $houseProfit   = $spinStats['house_profit'] + $diceStats['house_profit'] + $rpsStats['house_profit'];
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
        $rpsTopPlayers = $topQuery('rps');

        // Finance summary for easier admin management
        $todayBase = MiniGameLog::whereDate('created_at', today());
        $todayRevenue = (float) (clone $todayBase)->sum('bet_amount');
        $todayPayout = (float) (clone $todayBase)->sum('payout');
        $todayNet = $todayRevenue - $todayPayout;

        $monthBase = MiniGameLog::where('created_at', '>=', $monthStart);
        $monthRevenue = (float) (clone $monthBase)->sum('bet_amount');
        $monthPayout = (float) (clone $monthBase)->sum('payout');
        $monthNet = $monthRevenue - $monthPayout;

        $rpsMonthBase = MiniGameLog::where('game_type', 'rps')->where('created_at', '>=', $monthStart);
        $rpsMonthGames = (int) (clone $rpsMonthBase)->count();
        $rpsMonthWins = (int) (clone $rpsMonthBase)->where('won', true)->count();
        $rpsMonthWinRate = $rpsMonthGames > 0 ? round($rpsMonthWins * 100 / $rpsMonthGames, 1) : 0;
        $rpsMonthWinRateTarget = (float) GameSetting::get('rps_monthly_win_rate_target', '45');

        // Load all current settings for the config form
        $settings = GameSetting::all()->map(fn($s) => $s->value);

        return view('admin.casino', compact(
            'spinStats','diceStats','rpsStats','totalGames','houseProfit',
            'overallWinRate','winnersCount','spinTopPlayers','diceTopPlayers','rpsTopPlayers',
            'todayRevenue','todayPayout','todayNet','monthRevenue','monthPayout','monthNet',
            'rpsMonthGames','rpsMonthWinRate','rpsMonthWinRateTarget',
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
            'rps_enabled'         => 'required|in:0,1',
            'rps_house_edge'      => 'required|in:0,1',
            'rps_win_rate_limit'  => 'required|numeric|min:10|max:95',
            'rps_win_rate_target' => 'required|numeric|min:5|max:90',
            'rps_monthly_win_rate_target' => 'required|numeric|min:5|max:90',
            'rps_draw_rate'       => 'required|numeric|min:0|max:40',
            'rps_single_payout_mult' => 'required|numeric|min:1.0|max:5.0',
            'rps_bo3_payout_mult' => 'required|numeric|min:1.0|max:8.0',
            'rps_max_bet'         => 'required|numeric|min:0',
            'farm_sell_win_rate_target' => 'required|numeric|min:5|max:95',
            'farm_sell_loss_pool'       => 'required|string|max:255',
            'farm_sell_win_pool'        => 'required|string|max:255',
            'register_bonus_enabled'    => 'required|in:0,1',
            'register_bonus_points'     => 'required|numeric|min:0|max:100000000',
        ]);

        $keys = [
            'spin_enabled','spin_house_edge','spin_win_rate_limit','spin_win_rate_target','spin_max_bet',
            'dice_enabled','dice_house_edge','dice_win_rate_limit','dice_payout_mult','dice_max_bet',
            'rps_enabled','rps_house_edge','rps_win_rate_limit','rps_win_rate_target','rps_monthly_win_rate_target','rps_draw_rate','rps_single_payout_mult','rps_bo3_payout_mult','rps_max_bet',
            'farm_sell_win_rate_target','farm_sell_loss_pool','farm_sell_win_pool',
            'register_bonus_enabled','register_bonus_points',
        ];
        foreach ($keys as $key) {
            GameSetting::set($key, $request->input($key));
        }

        return response()->json(['success' => true, 'message' => 'Đã lưu cấu hình Casino thành công!']);
    }

    /**
     * Trang quản lý liên hệ hỗ trợ
     */
    public function supportContacts()
    {
        $keys = [
            'support_title',
            'support_subtitle',
            'support_center_label',
            'support_phone',
            'support_email',
            'support_zalo_url',
            'support_messenger_url',
            'support_working_hours',
            'telegram_enabled',
            'telegram_bot_token',
            'telegram_chat_id',
        ];

        $settings = GameSetting::getMany($keys);

        $defaults = [
            'support_title' => 'Liên hệ hỗ trợ',
            'support_subtitle' => 'Hỗ trợ nhanh khi cần xử lý giao dịch / game',
            'support_center_label' => 'Trung tâm hỗ trợ MXH',
            'support_phone' => 'https://t.me/aquahub',
            'support_email' => 'support@aquahub.vn',
            'support_zalo_url' => 'https://t.me',
            'support_messenger_url' => 'https://m.me',
            'support_working_hours' => '08:00 - 22:00 mỗi ngày',
            'telegram_enabled' => '0',
            'telegram_bot_token' => '',
            'telegram_chat_id' => '',
        ];

        $settings = array_merge($defaults, $settings);

        return view('admin.support-contacts', compact('settings'));
    }

    /**
     * Lưu cấu hình liên hệ hỗ trợ
     */
    public function saveSupportContacts(Request $request)
    {
        $validated = $request->validate([
            'support_title' => 'required|string|max:120',
            'support_subtitle' => 'required|string|max:255',
            'support_center_label' => 'required|string|max:120',
            'support_phone' => 'required|url|max:255',
            'support_email' => 'required|email|max:120',
            'support_zalo_url' => 'required|url|max:255',
            'support_messenger_url' => 'required|url|max:255',
            'support_working_hours' => 'required|string|max:120',
            'telegram_enabled' => 'nullable|in:0,1',
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:120',
        ]);

        $validated['telegram_enabled'] = $request->input('telegram_enabled', '0');

        foreach ($validated as $key => $value) {
            GameSetting::set($key, (string) $value);
        }

        return redirect()
            ->route('admin.support.contacts')
            ->with('success', 'Đã lưu thông tin liên hệ hỗ trợ thành công!');
    }

    /**
     * Tổng thống kê doanh thu game cho admin
     */
    public function financeSummary()
    {
        $todayStart = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        $sum = function ($from) {
            $base = MiniGameLog::where('created_at', '>=', $from);
            $revenue = (float) (clone $base)->sum('bet_amount');
            $payout = (float) (clone $base)->sum('payout');
            return [
                'revenue' => $revenue,
                'payout' => $payout,
                'net' => $revenue - $payout,
                'games' => (int) (clone $base)->count(),
            ];
        };

        $today = $sum($todayStart);
        $week = $sum($weekStart);
        $month = $sum($monthStart);

        $byGame = MiniGameLog::selectRaw('game_type, COUNT(*) as total_games, SUM(bet_amount) as revenue, SUM(payout) as payout, SUM(bet_amount - payout) as net')
            ->where('created_at', '>=', $monthStart)
            ->groupBy('game_type')
            ->orderByDesc('revenue')
            ->get();

        return view('admin.finance-summary', compact('today', 'week', 'month', 'byGame'));
    }

    /**
     * Báo cáo doanh thu lỗ cho admin
     */
    public function financeLoss()
    {
        $days = MiniGameLog::selectRaw('DATE(created_at) as report_date, SUM(bet_amount) as revenue, SUM(payout) as payout, SUM(bet_amount - payout) as net')
            ->where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->groupBy('report_date')
            ->orderBy('report_date', 'desc')
            ->get();

        $lossDays = $days->where('net', '<', 0)->count();
        $totalLoss = (float) $days->where('net', '<', 0)->sum('net');
        $totalProfit = (float) $days->where('net', '>=', 0)->sum('net');

        $lossByGame = MiniGameLog::selectRaw('game_type, SUM(bet_amount - payout) as net')
            ->where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->groupBy('game_type')
            ->orderBy('net')
            ->get();

        return view('admin.finance-loss', compact('days', 'lossDays', 'totalLoss', 'totalProfit', 'lossByGame'));
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
