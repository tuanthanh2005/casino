<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RewardItemController;
use App\Http\Controllers\Admin\FarmAdminController;
use App\Http\Controllers\Admin\NavAdminController;
use App\Http\Controllers\Admin\SupportChatAdminController;
use App\Http\Controllers\SupportChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
// Landing page
Route::get('/landing', function () {
    return view('home.landing');
})->name('landing');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MiniGameController;

Route::middleware('auth')->group(function () {
    Route::get('/games', function () {
        return view('game.catalog');
    })->name('games.catalog');

    // Thông tin tài khoản
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Trang chủ game - Dashboard
    Route::get('/', function () {
        $user = auth()->user();
        $startOfMonth = now()->startOfMonth();
        $startOfDay = now()->startOfDay();

        $pendingBetsCount = \App\Models\Bet::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $predictionResults = \App\Models\Bet::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['won', 'lost'])
            ->latest('id')
            ->get(['bet_type', 'bet_amount', 'profit', 'status', 'created_at'])
            ->map(function ($bet) {
                return [
                    'game' => 'Dự đoán ' . strtoupper((string) $bet->bet_type),
                    'bet_amount' => (float) $bet->bet_amount,
                    'profit' => (float) $bet->profit,
                    'won' => $bet->status === 'won',
                    'created_at' => $bet->created_at,
                ];
            });

        $miniGameResults = \App\Models\MiniGameLog::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->get(['game_type', 'bet_amount', 'profit', 'won', 'created_at'])
            ->map(function ($log) {
                $gameLabel = match ($log->game_type) {
                    'spin' => 'Vòng quay',
                    'dice' => 'Tài xỉu',
                    'rps' => 'Kéo búa bao',
                    default => strtoupper((string) $log->game_type),
                };

                return [
                    'game' => $gameLabel,
                    'bet_amount' => (float) $log->bet_amount,
                    'profit' => (float) $log->profit,
                    'won' => (bool) $log->won,
                    'created_at' => $log->created_at,
                ];
            });

        $allResolvedResults = $predictionResults
            ->concat($miniGameResults)
            ->sortByDesc('created_at')
            ->values();

        $currentWinStreak = 0;
        foreach ($allResolvedResults as $result) {
            if (! $result['won']) {
                break;
            }
            $currentWinStreak++;
        }

        $monthWinTotal = $allResolvedResults
            ->filter(fn ($item) => $item['profit'] > 0 && $item['created_at']?->greaterThanOrEqualTo($startOfMonth))
            ->sum('profit');

        $todayNetProfit = $allResolvedResults
            ->filter(fn ($item) => $item['created_at']?->greaterThanOrEqualTo($startOfDay))
            ->sum('profit');

        $recentActivities = $allResolvedResults->take(8)->values();

        return view('game.dashboard', [
            'balancePoint' => (float) $user->balance_point,
            'pendingBetsCount' => $pendingBetsCount,
            'monthWinTotal' => (float) $monthWinTotal,
            'todayNetProfit' => (float) $todayNetProfit,
            'currentWinStreak' => $currentWinStreak,
            'recentActivities' => $recentActivities,
        ]);
    })->name('home');

    // Game Dự Đoán Long/Short
    Route::get('/game/prediction', [GameController::class, 'index'])->name('prediction');

    // Game API
    Route::post('/bet', [GameController::class, 'placeBet'])->name('bet.place');
    Route::get('/api/my-bets', [GameController::class, 'myBets'])->name('bet.history');
    Route::get('/api/current-session', [GameController::class, 'currentSession'])->name('session.current');

    // 🎡 Vòng Quay May Mắn
    Route::get('/spin', [MiniGameController::class, 'spinIndex'])->name('spin');
    Route::post('/api/spin', [MiniGameController::class, 'doSpin'])->name('spin.do');

    // 🎲 Tài Xỉu
    Route::get('/dice', [MiniGameController::class, 'diceIndex'])->name('dice');
    Route::post('/api/dice', [MiniGameController::class, 'doDice'])->name('dice.do');

    // ✊ Kéo Búa Bao
    Route::get('/rps', [MiniGameController::class, 'rpsIndex'])->name('rps');
    Route::post('/api/rps', [MiniGameController::class, 'doRps'])->name('rps.do');

    // Shop
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::post('/shop/exchange/{item}', [ShopController::class, 'exchange'])->name('shop.exchange');

    // 💳 Nạp / Rút tiền
    Route::get('/payment/deposit',  [PaymentController::class, 'depositIndex'])->name('payment.deposit');
    Route::post('/payment/deposit', [PaymentController::class, 'depositStore'])->name('payment.deposit.store');
    Route::get('/payment/withdraw', [PaymentController::class, 'withdrawIndex'])->name('payment.withdraw');
    Route::post('/payment/withdraw',[PaymentController::class, 'withdrawStore'])->name('payment.withdraw.store');

    // 💬 Hỗ trợ chat
    Route::get('/support/chat', [SupportChatController::class, 'index'])->name('support.chat');
    Route::post('/support/chat/send', [SupportChatController::class, 'send'])->name('support.chat.send');
    Route::get('/support/chat/fetch', [SupportChatController::class, 'fetch'])->name('support.chat.fetch');

    // 🌾 Nông Trại
    Route::get('/farm',                     [FarmController::class, 'index'])->name('farm');
    Route::post('/farm/plant',              [FarmController::class, 'plant'])->name('farm.plant');
    Route::post('/farm/plant-bulk',         [FarmController::class, 'plantBulk'])->name('farm.plant.bulk');
    Route::post('/farm/water/{crop}',       [FarmController::class, 'water'])->name('farm.water');
    Route::post('/farm/harvest/{crop}',     [FarmController::class, 'harvest'])->name('farm.harvest');
    Route::post('/farm/sell',               [FarmController::class, 'sell'])->name('farm.sell');
    Route::get('/farm/status',              [FarmController::class, 'getStatus'])->name('farm.status');
    Route::post('/farm/notifications/read', [FarmController::class, 'markNotificationsRead'])->name('farm.notif.read');
    Route::post('/farm/market/refresh',     [FarmController::class, 'refreshMarket'])->name('farm.market.refresh');

    // 🛡️ Hỗ Trợ MXH
    Route::prefix('ho-tro-mxh')->name('nav.')->group(function () {
        Route::get('/',                              [NavController::class, 'index'])->name('index');
        Route::get('/don-hang',                      [NavController::class, 'myOrders'])->name('my-orders');
        Route::get('/don-hang/{code}/thanh-toan',    [NavController::class, 'payment'])->name('payment');
        Route::post('/don-hang/{code}/xac-nhan',     [NavController::class, 'confirmPayment'])->name('confirm');
        Route::get('/don-hang/{code}/hoan-thanh',    [NavController::class, 'success'])->name('success');
        Route::get('/{slug}',                        [NavController::class, 'show'])->name('show');
        Route::post('/{slug}/dang-ky',               [NavController::class, 'store'])->name('store');
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/adjust-points', [AdminController::class, 'adjustPoints'])->name('users.adjust-points');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
    
    // Exchange Requests
    Route::get('/exchanges', [AdminController::class, 'exchanges'])->name('exchanges');
    Route::post('/exchanges/{exchange}/approve', [AdminController::class, 'approveExchange'])->name('exchanges.approve');
    Route::post('/exchanges/{exchange}/reject', [AdminController::class, 'rejectExchange'])->name('exchanges.reject');
    
    // Game Sessions
    Route::get('/sessions', [AdminController::class, 'sessions'])->name('sessions');
    Route::post('/sessions/create', [AdminController::class, 'createSession'])->name('sessions.create');
    Route::post('/sessions/{session}/resolve', [AdminController::class, 'resolveSession'])->name('sessions.resolve');
    
    // Reward Items CRUD
    Route::resource('rewards', RewardItemController::class);

    // Casino Stats
    Route::get('/casino', [AdminController::class, 'casinoStats'])->name('casino');
    Route::post('/casino/settings', [AdminController::class, 'saveCasinoSettings'])->name('casino.settings');
    Route::get('/support-contacts', [AdminController::class, 'supportContacts'])->name('support.contacts');
    Route::post('/support-contacts', [AdminController::class, 'saveSupportContacts'])->name('support.contacts.save');
    Route::get('/finance/summary', [AdminController::class, 'financeSummary'])->name('finance.summary');
    Route::get('/finance/loss', [AdminController::class, 'financeLoss'])->name('finance.loss');
    Route::get('/support-chat', [SupportChatAdminController::class, 'index'])->name('support.chat');
    Route::post('/support-chat/send', [SupportChatAdminController::class, 'send'])->name('support.chat.send');
    Route::get('/support-chat/fetch/{user}', [SupportChatAdminController::class, 'fetch'])->name('support.chat.fetch');

    // 💳 Quản lý Nạp tiền
    Route::get('/deposits', [AdminController::class, 'deposits'])->name('deposits');
    Route::post('/deposits/{deposit}/approve', [AdminController::class, 'approveDeposit'])->name('deposits.approve');
    Route::post('/deposits/{deposit}/reject',  [AdminController::class, 'rejectDeposit'])->name('deposits.reject');

    // 💸 Quản lý Rút tiền
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
    Route::post('/withdrawals/{withdrawal}/approve', [AdminController::class, 'approveWithdrawal'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject',  [AdminController::class, 'rejectWithdrawal'])->name('withdrawals.reject');

    // 🌾 Admin Nông Trại
    Route::get('/farm',                  [FarmAdminController::class, 'index'])->name('farm');
    Route::get('/farm/seeds',            [FarmAdminController::class, 'seeds'])->name('farm.seeds');
    Route::post('/farm/seeds',           [FarmAdminController::class, 'storeSeed'])->name('farm.seeds.store');
    Route::put('/farm/seeds/{seed}',     [FarmAdminController::class, 'updateSeed'])->name('farm.seeds.update');
    Route::delete('/farm/seeds/{seed}',  [FarmAdminController::class, 'deleteSeed'])->name('farm.seeds.delete');
    Route::get('/farm/transactions',     [FarmAdminController::class, 'transactions'])->name('farm.transactions');

    // 🛡️ Admin Hỗ Trợ MXH
    Route::get('/nav/services',                      [NavAdminController::class, 'services'])->name('nav.services');
    Route::post('/nav/services',                     [NavAdminController::class, 'storeService'])->name('nav.services.store');
    Route::put('/nav/services/{id}',                 [NavAdminController::class, 'updateService'])->name('nav.services.update');
    Route::delete('/nav/services/{id}',              [NavAdminController::class, 'deleteService'])->name('nav.services.delete');
    Route::get('/nav/orders',                        [NavAdminController::class, 'orders'])->name('nav.orders');
    Route::get('/nav/orders/{id}',                   [NavAdminController::class, 'orderDetail'])->name('nav.orders.detail');
    Route::post('/nav/orders/{id}/approve-payment',  [NavAdminController::class, 'approvePayment'])->name('nav.orders.approve');
    Route::post('/nav/orders/{id}/complete',         [NavAdminController::class, 'completeOrder'])->name('nav.orders.complete');
    Route::post('/nav/orders/{id}/status',           [NavAdminController::class, 'updateStatus'])->name('nav.orders.status');
    Route::get('/nav/orders/{id}/generate-appeal',   [NavAdminController::class, 'generateAppeal'])->name('nav.orders.appeal');
    Route::get('/nav/settings',                      [NavAdminController::class, 'settings'])->name('nav.settings');
    Route::post('/nav/settings',                     [NavAdminController::class, 'saveSettings'])->name('nav.settings.save');
});

// Test API RapidAPI Bóng đá
Route::get('/api-test', function () {
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type'    => 'application/json',
            'x-rapidapi-host' => 'free-football-soccer-videos.p.rapidapi.com',
            'x-rapidapi-key'  => '43561f19d4msh0fcad3287658ef9p1ce84fjsn35879407b8ac',
        ])->get('https://free-football-soccer-videos.p.rapidapi.com/');

        $videos = $response->json();
        
        // RapidAPI đôi khi trả về mảng trực tiếp hoặc bọc trong một key
        if (!is_array($videos) && isset($videos['response'])) {
            $videos = $videos['response'];
        }

        return view('football.videos', ['videos' => $videos]);
    } catch (\Exception $e) {
        return view('football.videos', ['error' => $e->getMessage(), 'videos' => []]);
    }
});

