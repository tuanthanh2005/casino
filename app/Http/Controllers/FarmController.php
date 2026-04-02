<?php

namespace App\Http\Controllers;

use App\Models\FarmCrop;
use App\Models\FarmInventory;
use App\Models\FarmNotification;
use App\Models\FarmTransaction;
use App\Models\GameSetting;
use App\Models\SeedType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmController extends Controller
{
    private const SLOTS = 20;
    private const WATER_COOLDOWN = 600;  // 10 phút (giây)
    private const DEAD_AFTER_HOURS = 12;
    private const DELETE_AFTER_HRS = 24;  // giờ chờ sau khi chết

    // ═══════════════════════════════════════════════
    // TRANG CHÍNH
    // ═══════════════════════════════════════════════

    public function index()
    {
        $user = auth()->user();

        // Auto-check for the current user to trigger state updates & notifications
        $this->checkUserCrops($user->id);

        $crops = FarmCrop::where('user_id', $user->id)
            ->with('seedType')->get()->keyBy('slot_number');

        $seeds = SeedType::where('is_active', true)
            ->orderBy('sort_order')->get();

        $inventory = FarmInventory::where('user_id', $user->id)
            ->with('seedType')->where('quantity', '>', 0)->get();

        $notifications = FarmNotification::where('user_id', $user->id)
            ->where('is_read', false)->latest()->take(10)->get();

        $unreadCount = FarmNotification::where('user_id', $user->id)
            ->where('is_read', false)->count();

        $marketData = $this->getMarketPrices();
        $marketPrices = $marketData['prices'];
        $marketExp = $marketData['expires_at'];

        $history = FarmTransaction::where('user_id', $user->id)
            ->with('seedType')->latest()->take(30)->get();

        return view('farm.index', compact(
            'crops',
            'seeds',
            'inventory',
            'notifications',
            'unreadCount',
            'marketPrices',
            'marketExp',
            'history'
        ));
    }

    // ═══════════════════════════════════════════════
    // TRỒNG CÂY
    // ═══════════════════════════════════════════════

    public function plant(Request $request)
    {
        $request->validate([
            'slot' => 'required|integer|between:1,' . self::SLOTS,
            'seed_type_id' => 'required|exists:seed_types,id',
        ]);

        $user = auth()->user();
        $slot = (int) $request->slot;
        $seed = SeedType::where('id', $request->seed_type_id)
            ->where('is_active', true)->firstOrFail();

        // Kiểm tra ô trống
        if (FarmCrop::where('user_id', $user->id)->where('slot_number', $slot)->exists()) {
            return response()->json(['success' => false, 'message' => 'Ô đất này đang có cây! Chờ xóa hoặc thu hoạch.']);
        }

        if ((float) $user->balance_point < (float) $seed->price_buy) {
            return response()->json(['success' => false, 'message' => "Không đủ PT! Cần {$seed->price_buy} PT."]);
        }

        $now = now();
        $ripeAt = $now->copy()->addMinutes($seed->grow_time_mins);

        DB::transaction(function () use ($user, $seed, $slot, $now, $ripeAt) {
            $user->decrement('balance_point', $seed->price_buy);
            FarmCrop::create([
                'user_id' => $user->id,
                'seed_type_id' => $seed->id,
                'slot_number' => $slot,
                'status' => 'growing',
                'planted_at' => $now,
                'ripe_at' => $ripeAt,
                'last_watered_at' => $now,
                'watering_count' => 0,
            ]);
            FarmTransaction::create([
                'user_id' => $user->id,
                'seed_type_id' => $seed->id,
                'type' => 'buy_seed',
                'quantity' => 1,
                'unit_price_pt' => $seed->price_buy,
                'total_pt' => $seed->price_buy,
                'note' => "Trồng {$seed->name} ô #{$slot}",
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "🌱 Đã trồng {$seed->emoji} {$seed->name} vào ô #{$slot}!",
            'balance' => (float) $user->fresh()->balance_point,
        ]);
    }

    // ═══════════════════════════════════════════════
    // MUA NHANH HÀNG LOẠT
    // ═══════════════════════════════════════════════

    public function plantBulk(Request $request)
    {
        $request->validate([
            'seed_type_id' => 'required|exists:seed_types,id',
            'quantity' => 'required|integer|min:1|max:' . self::SLOTS,
        ]);

        $user = auth()->user();
        $qty = (int) $request->quantity;
        $seed = SeedType::where('id', $request->seed_type_id)
            ->where('is_active', true)->firstOrFail();

        $totalCost = $seed->price_buy * $qty;
        if ((float) $user->balance_point < (float) $totalCost) {
            return response()->json(['success' => false, 'message' => "Không đủ PT! Bạn cần " . number_format($totalCost) . " PT!"]);
        }

        // Tìm các ô trống
        $usedSlots = FarmCrop::where('user_id', $user->id)->pluck('slot_number')->toArray();
        $emptySlots = [];
        for ($i = 1; $i <= self::SLOTS; $i++) {
            if (!in_array($i, $usedSlots)) {
                $emptySlots[] = $i;
            }
        }

        if (count($emptySlots) < $qty) {
            return response()->json(['success' => false, 'message' => "Bạn chỉ còn " . count($emptySlots) . " ô đất trống!"]);
        }

        $slotsToPlant = array_slice($emptySlots, 0, $qty);
        $now = now();
        $ripeAt = $now->copy()->addMinutes($seed->grow_time_mins);

        DB::transaction(function () use ($user, $seed, $slotsToPlant, $now, $ripeAt, $totalCost, $qty) {
            $user->decrement('balance_point', $totalCost);

            $insertData = [];
            foreach ($slotsToPlant as $slot) {
                $insertData[] = [
                    'user_id' => $user->id,
                    'seed_type_id' => $seed->id,
                    'slot_number' => $slot,
                    'status' => 'growing',
                    'planted_at' => $now,
                    'ripe_at' => $ripeAt,
                    'last_watered_at' => $now,
                    'watering_count' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            FarmCrop::insert($insertData);

            FarmTransaction::create([
                'user_id' => $user->id,
                'seed_type_id' => $seed->id,
                'type' => 'buy_seed',
                'quantity' => $qty,
                'unit_price_pt' => $seed->price_buy,
                'total_pt' => $totalCost,
                'note' => "Mua nhanh {$qty} {$seed->name}",
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "🌱 Đã gieo trồng {$qty} {$seed->emoji} {$seed->name} thành công!",
            'balance' => (float) $user->fresh()->balance_point,
        ]);
    }

    // ═══════════════════════════════════════════════
    // TƯỚI NƯỚC
    // ═══════════════════════════════════════════════

    public function water(Request $request, $cropId)
    {
        $crop = FarmCrop::find($cropId);
        if (!$crop) {
            return response()->json([
                'success' => false,
                'message' => 'Cây không tìm thấy hoặc đã thối hỏng. Hãy load trang lại xem nhé!'
            ]);
        }

        if ($crop->status !== 'growing') {
            return response()->json(['success' => false, 'message' => 'Cây này không ở trạng thái đang lớn!']);
        }

        if ($crop->seedType->grow_time_mins < 20) {
            return response()->json(['success' => false, 'message' => 'Cây này lớn quá nhanh, không cần tưới!']);
        }

        $maxW = $crop->seedType->max_waterings;
        if ($crop->watering_count >= $maxW) {
            return response()->json(['success' => false, 'message' => "Đã tưới đủ {$maxW} lần rồi!"]);
        }

        if ($crop->isWaterOnCooldown()) {
            $rem = $crop->waterCooldownRemainingSeconds();
            $min = ceil($rem / 60);
            return response()->json(['success' => false, 'message' => "Chờ {$min} phút nữa để tưới lại!"]);
        }

        // Giảm thời gian chín random 10–60 giây
        $reduction = rand(10, 60);
        $newRipeAt = $crop->ripe_at->copy()->subSeconds($reduction);
        if ($newRipeAt->lte(now()->addSecond())) {
            $newRipeAt = now()->addSecond();
        }

        $crop->update([
            'last_watered_at' => now(),
            'watering_count' => $crop->watering_count + 1,
            'ripe_at' => $newRipeAt,
        ]);

        $left = $maxW - $crop->watering_count - 1;
        return response()->json([
            'success' => true,
            'message' => "💧 Đã tưới! Rút ngắn {$reduction}s. Còn {$left} lần tưới.",
            'reduction' => $reduction,
            'ripe_at_ts' => $newRipeAt->timestamp,
            'water_count' => $crop->watering_count + 1,
            'max_water' => $maxW,
        ]);
    }

    // ═══════════════════════════════════════════════
    // THU HOẠCH
    // ═══════════════════════════════════════════════

    public function harvest(Request $request, $cropId)
    {
        $crop = FarmCrop::find($cropId);
        if (!$crop) {
            return response()->json([
                'success' => false,
                'message' => 'Lô hàng này không còn tồn tại hoặc đã có thay đổi. Hãy load trang lại xem nhé!'
            ]);
        }

        if ($crop->user_id !== auth()->id())
            abort(403);

        $isRipeTime = $crop->status === 'ripe' || ($crop->status === 'growing' && $crop->ripe_at->lte(now()));
        if (!$isRipeTime) {
            return response()->json(['success' => false, 'message' => 'Cây chưa chín!']);
        }

        $qty = 1;
        $seedName = $crop->seedType->name;
        $seedId = $crop->seed_type_id;
        $userId = $crop->user_id;

        $totalInBag = FarmInventory::where('user_id', $userId)->sum('quantity');
        if ($totalInBag + $qty > 10) {
            return response()->json(['success' => false, 'message' => "Túi đồ đã ĐẦY (Max 10 trái)! Bạn đang có {$totalInBag} trái. Hãy qua Chợ bán bớt trước khi thu hoạch nhé!"]);
        }

        DB::transaction(function () use ($userId, $seedId, $qty, $seedName, $crop) {
            // Cộng vào túi đồ - Mỗi lần thu hoạch là một dòng mới (Lô hàng) để có thời gian đếm ngược riêng
            FarmInventory::create([
                'user_id' => $userId,
                'seed_type_id' => $seedId,
                'quantity' => $qty,
                'expires_at' => now()->addHours(12),
            ]);
            // Lịch sử thu hoạch
            FarmTransaction::create([
                'user_id' => $userId,
                'seed_type_id' => $seedId,
                'type' => 'harvest',
                'quantity' => $qty,
                'note' => "Thu hoạch {$seedName} ô #{$crop->slot_number}",
            ]);
            // Đánh dấu thông báo chín đã đọc
            FarmNotification::where('farm_crop_id', $crop->id)->update(['is_read' => true]);
            $crop->delete();
        });

        $emoji = $crop->seedType->emoji;
        $msg = "✅ Thu hoạch được {$qty} {$emoji} {$seedName}!";

        return response()->json(['success' => true, 'message' => $msg, 'qty' => $qty, 'lucky' => false]);
    }

    // ═══════════════════════════════════════════════
    // BÁN TRÁI (Auto cộng PT)
    // ═══════════════════════════════════════════════

    public function sell(Request $request)
    {
        $request->validate([
            'seed_type_id' => 'required|exists:seed_types,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $seed = SeedType::findOrFail($request->seed_type_id);
        $qty = (int) $request->quantity;

        $totalInInventory = FarmInventory::where('user_id', $user->id)
            ->where('seed_type_id', $seed->id)
            ->sum('quantity');

        if ($totalInInventory < $qty) {
            return response()->json(['success' => false, 'message' => "Bạn chỉ có {$totalInInventory} trái trong kho!"]);
        }

        $marketData = $this->getMarketPrices();
        $prices = $marketData['prices'];
        $marketUnitPrice = $prices[$seed->id] ?? (float) $seed->price_sell_base;
        $modifier = round($marketUnitPrice / max(1, (float) $seed->price_sell_base), 4);
        $fair = $this->drawSellLuckProvablyFair((int) $user->id);
        $batchLuck = (float) $fair['luck_modifier'];

        $totalPt = 0;

        return DB::transaction(function () use ($user, $seed, $qty, $marketUnitPrice, $modifier, $batchLuck, $fair, &$totalPt) {
            $remainingToSell = $qty;

            // Get batches FIFO
            $inventories = FarmInventory::where('user_id', $user->id)
                ->where('seed_type_id', $seed->id)
                ->orderBy('expires_at', 'asc')
                ->get();

            $totalBaseValue = $marketUnitPrice * $qty;
            foreach ($inventories as $inv) {
                if ($remainingToSell <= 0)
                    break;

                $unitsFromThisInv = min($inv->quantity, $remainingToSell);

                if ($inv->quantity <= $unitsFromThisInv) {
                    $inv->delete();
                } else {
                    $inv->decrement('quantity', $unitsFromThisInv);
                }

                $remainingToSell -= $unitsFromThisInv;
            }

            $totalPt = (float) floor($totalBaseValue * (1 + $batchLuck));
            $user->increment('balance_point', $totalPt);

            FarmTransaction::create([
                'user_id' => $user->id,
                'seed_type_id' => $seed->id,
                'type' => 'sell_fruit',
                'quantity' => $qty,
                'unit_price_pt' => $marketUnitPrice,
                'total_pt' => $totalPt,
                'price_modifier' => $modifier, // legacy field, keeping for schema
                'note' => "Bán {$qty} {$seed->name} (Luck: " . ($batchLuck * 100) . "%, nonce: {$fair['nonce']})",
            ]);

            $luckPct = round($batchLuck * 100, 1);

            return response()->json([
                'success' => true,
                'message' => "💰 Bán thành công {$qty} {$seed->name}!",
                'total_pt' => (float) $totalPt,
                'luck_pct' => (float) $luckPct,
                'fair' => [
                    'nonce' => (int) $fair['nonce'],
                    'roll' => (int) $fair['roll'],
                    'server_seed_hash' => (string) $fair['server_seed_hash'],
                    'client_seed' => (string) $fair['client_seed'],
                ],
                'balance' => (float) $user->fresh()->balance_point,
                'inventory' => FarmInventory::where('user_id', $user->id)
                    ->where('quantity', '>', 0)
                    ->with('seedType')
                    ->get()
                    ->map(function ($inv) {
                        return [
                            'id' => $inv->id,
                            'seed_type_id' => $inv->seed_type_id,
                            'name' => $inv->seedType->name,
                            'emoji' => $inv->seedType->emoji,
                            'quantity' => $inv->quantity,
                            'expires_at_ts' => $inv->expires_at?->timestamp,
                        ];
                    }),
            ]);
        });
    }

    private function drawSellLuckProvablyFair(int $userId): array
    {
        $keyPrefix = 'farm_sell_pf_v1_' . $userId;
        $serverSeedKey = $keyPrefix . '_server_seed';
        $clientSeedKey = $keyPrefix . '_client_seed';
        $nonceKey = $keyPrefix . '_nonce';

        $serverSeed = session($serverSeedKey);
        if (!$serverSeed) {
            $serverSeed = bin2hex(random_bytes(32));
            session()->put($serverSeedKey, $serverSeed);
        }

        $clientSeed = session($clientSeedKey);
        if (!$clientSeed) {
            $clientSeed = substr(hash('sha256', $userId . '|' . microtime(true) . '|' . bin2hex(random_bytes(8))), 0, 32);
            session()->put($clientSeedKey, $clientSeed);
        }

        $nonce = (int) session($nonceKey, 0) + 1;
        session()->put($nonceKey, $nonce);

        $payload = $clientSeed . ':' . $nonce . ':farm_sell';
        $hash = hash_hmac('sha256', $payload, $serverSeed);

        $winRateTarget = (int) GameSetting::get('farm_sell_win_rate_target', '45');
        $winRateTarget = max(5, min(95, $winRateTarget));

        $lossPool = $this->parseSellLuckPool((string) GameSetting::get('farm_sell_loss_pool', '-10,-20,-30,-40,-50'), [-10, -20, -30, -40, -50]);
        $winPool = $this->parseSellLuckPool((string) GameSetting::get('farm_sell_win_pool', '10,20,30,40,50'), [10, 20, 30, 40, 50]);

        // 1..10000, with win threshold based on admin setting
        $roll = (hexdec(substr($hash, 0, 8)) % 10000) + 1;
        $isWin = $roll <= ($winRateTarget * 100);

        $pool = $isWin ? $winPool : $lossPool;
        $poolIndex = hexdec(substr($hash, 8, 2)) % count($pool);
        $luckPct = (float) $pool[$poolIndex];

        return [
            'luck_modifier' => $luckPct / 100,
            'luck_pct' => $luckPct,
            'roll' => $roll,
            'nonce' => $nonce,
            'server_seed_hash' => hash('sha256', $serverSeed),
            'client_seed' => $clientSeed,
        ];
    }

    private function parseSellLuckPool(string $raw, array $fallback): array
    {
        $items = array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== '');
        $values = [];

        foreach ($items as $item) {
            if (!is_numeric($item)) {
                continue;
            }

            $val = (int) round((float) $item);
            if ($val === 0) {
                continue;
            }
            $values[] = $val;
        }

        return count($values) > 0 ? array_values($values) : $fallback;
    }

    // ═══════════════════════════════════════════════
    // POLLING: trạng thái cây (mỗi 30s JS gọi)
    // ═══════════════════════════════════════════════

    public function getStatus()
    {
        $user = auth()->user();

        // Auto-check for the current user
        $this->checkUserCrops($user->id);

        $crops = FarmCrop::where('user_id', $user->id)
            ->with('seedType')->get()
            ->map(function ($c) {
                /** @var \App\Models\FarmCrop $c */
                return [
                    'id' => $c->id,
                    'slot' => $c->slot_number,
                    'status' => $c->status,
                    'seed_name' => $c->seedType->name,
                    'seed_emoji' => $c->seedType->emoji,
                    'ripe_at_ts' => $c->ripe_at?->timestamp,
                    'delete_at_ts' => $c->delete_at?->timestamp,
                    'water_count' => $c->watering_count,
                    'max_water' => $c->seedType->max_waterings,
                    'can_water' => $c->canWater(),
                    'water_cd_sec' => $c->waterCooldownRemainingSeconds(),
                    'cd_ts' => $c->isWaterOnCooldown() ? $c->last_watered_at->copy()->addSeconds(600)->timestamp : 0,
                    'progress' => $c->progressPercent(),
                    'secs_left' => $c->secondsUntilRipe(),
                    'planted_ts' => $c->planted_at->timestamp,
                ];
            });

        $inventory = FarmInventory::where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->with('seedType')
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'seed_type_id' => $inv->seed_type_id,
                    'name' => $inv->seedType->name,
                    'emoji' => $inv->seedType->emoji,
                    'quantity' => $inv->quantity,
                    'expires_at_ts' => $inv->expires_at?->timestamp,
                    'is_expiring' => $inv->expires_at?->subMinutes(5)->lte(now()),
                ];
            });

        $marketData = $this->getMarketPrices();

        return response()->json([
            'crops' => $crops,
            'inventory' => $inventory,
            'unread' => FarmNotification::where('user_id', $user->id)->where('is_read', false)->count(),
            'balance' => (float) $user->fresh()->balance_point,
            'market_exp' => $marketData['expires_at'],
        ]);
    }

    // ═══════════════════════════════════════════════
    // THÔNG BÁO
    // ═══════════════════════════════════════════════

    public function markNotificationsRead()
    {
        FarmNotification::where('user_id', auth()->id())
            ->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════
    // GIÁ THỊ TRƯỜNG (cố định theo giá gốc, không random theo phiên)
    // ═══════════════════════════════════════════════

    private function getMarketPrices(): array
    {
        $prices = [];
        foreach (SeedType::where('is_active', true)->get() as $seed) {
            $prices[$seed->id] = round((float) $seed->price_sell_base, 2);
        }

        return ['prices' => $prices, 'expires_at' => null];
    }

    public function refreshMarket()
    {
        // Trả về giá cố định theo giá gốc hiện tại
        $marketData = $this->getMarketPrices();
        $prices = $marketData['prices'];

        // Gắn % thay đổi
        $seeds = SeedType::where('is_active', true)->get()->keyBy('id');
        $result = [];
        foreach ($prices as $id => $price) {
            $base = (float) ($seeds[$id]->price_sell_base ?? 1);
            $result[$id] = [
                'price' => $price,
                'base' => $base,
                'pct' => round(($price / $base - 1) * 100, 1),
                'is_profit' => $price >= $base,
            ];
        }
        return response()->json(['prices' => $result, 'expires_at' => $marketData['expires_at']]);
    }

    // ═══════════════════════════════════════════════
    // TIỆN ÍCH CHẠY NGẦM CHO NGƯỜI DÙNG HIỆN TẠI
    // ═══════════════════════════════════════════════

    private function checkUserCrops($userId)
    {
        $now = now();
        $crops = FarmCrop::where('user_id', $userId)
            ->whereIn('status', ['growing', 'ripe', 'dead'])
            ->with('seedType')->get();

        foreach ($crops as $crop) {
            /** @var \App\Models\FarmCrop $crop */
            // 1. Check delete dead crops (> 24h dead)
            if ($crop->status === 'dead' && $crop->delete_at && $crop->delete_at->lte($now)) {
                $crop->delete();
                continue;
            }

            // 2. Check dead because of not harvested within 12 hours + 1 min grace period
            if ($crop->ripe_at && $crop->ripe_at->copy()->addHours(12)->addMinutes(1)->lte($now)) {
                if ($crop->status !== 'dead') {
                    $crop->update([
                        'status' => 'dead',
                        'died_at' => $now,
                        'delete_at' => $now->copy()->addHours(24),
                    ]);
                    FarmNotification::create([
                        'user_id' => $userId,
                        'farm_crop_id' => $crop->id,
                        'type' => 'dead',
                        'message' => "💀 Cây {$crop->seedType->emoji} {$crop->seedType->name} (ô #{$crop->slot_number}) đã thối hỏng do không được thu hoạch kịp thời! Sẽ dọn sau 24h.",
                    ]);
                }
                continue;
            }

            // 3. Check ripe
            if ($crop->status === 'growing' && $crop->ripe_at && $crop->ripe_at->lte($now)) {
                $crop->update(['status' => 'ripe']);

                $alreadyNotified = FarmNotification::where('farm_crop_id', $crop->id)
                    ->where('type', 'ripe')->exists();

                if (!$alreadyNotified) {
                    FarmNotification::create([
                        'user_id' => $userId,
                        'farm_crop_id' => $crop->id,
                        'type' => 'ripe',
                        'message' => "✅ Cây {$crop->seedType->emoji} {$crop->seedType->name} (ô #{$crop->slot_number}) đã chín! Vào thu hoạch ngay nhé (Giới hạn 12h trước khi thối)!",
                    ]);
                }
            }
        }

        // 4. Check Inventory Expiration & Spoilage Warnings
        // First, fix any legacy NULL expirations (prevent them staying forever)
        FarmInventory::where('user_id', $userId)
            ->whereNull('expires_at')
            ->update(['expires_at' => $now->copy()->addHours(12)]);

        $invs = FarmInventory::where('user_id', $userId)
            ->with('seedType')->get();

        foreach ($invs as $inv) {
            /** @var \App\Models\FarmInventory $inv */
            // expires_at is now guaranteed to be set

            // Check if rotted
            if ($inv->expires_at->lte($now)) {
                $seedName = $inv->seedType->name;
                $emoji = $inv->seedType->emoji;
                $qty = $inv->quantity;

                $inv->delete();

                FarmNotification::create([
                    'user_id' => $userId,
                    'type' => 'dead',
                    'message' => "💀 {$qty} {$emoji} {$seedName} trong túi đã thối hỏng do để quá 12 giờ!",
                ]);
                continue;
            }

            // Check if 5-minute warning needed
            if ($inv->expires_at->copy()->subMinutes(5)->lte($now)) {
                if (!$inv->warned_at) {
                    $seedName = $inv->seedType->name;
                    $emoji = $inv->seedType->emoji;

                    $inv->update(['warned_at' => $now]);

                    FarmNotification::create([
                        'user_id' => $userId,
                        'type' => 'ripe', // Using ripe style for warning
                        'message' => "⚠️ CẢNH BÁO: Lô {$emoji} {$seedName} trong túi của bạn chỉ còn chưa đầy 5 phút sẽ bị THỐI! Hãy bán ngay trước khi mất trắng!",
                    ]);
                }
            }
        }
    }
}
