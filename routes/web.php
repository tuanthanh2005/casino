<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RewardItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
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
use App\Http\Controllers\MiniGameController;

Route::middleware('auth')->group(function () {
    // Trang chủ game
    Route::get('/', [GameController::class, 'index'])->name('home');

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

    // Shop
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::post('/shop/exchange/{item}', [ShopController::class, 'exchange'])->name('shop.exchange');

    // 💳 Nạp / Rút tiền
    Route::get('/payment/deposit',  [PaymentController::class, 'depositIndex'])->name('payment.deposit');
    Route::post('/payment/deposit', [PaymentController::class, 'depositStore'])->name('payment.deposit.store');
    Route::get('/payment/withdraw', [PaymentController::class, 'withdrawIndex'])->name('payment.withdraw');
    Route::post('/payment/withdraw',[PaymentController::class, 'withdrawStore'])->name('payment.withdraw.store');
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

    // 💳 Quản lý Nạp tiền
    Route::get('/deposits', [AdminController::class, 'deposits'])->name('deposits');
    Route::post('/deposits/{deposit}/approve', [AdminController::class, 'approveDeposit'])->name('deposits.approve');
    Route::post('/deposits/{deposit}/reject',  [AdminController::class, 'rejectDeposit'])->name('deposits.reject');

    // 💸 Quản lý Rút tiền
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
    Route::post('/withdrawals/{withdrawal}/approve', [AdminController::class, 'approveWithdrawal'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject',  [AdminController::class, 'rejectWithdrawal'])->name('withdrawals.reject');
});
