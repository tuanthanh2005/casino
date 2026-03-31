<?php

namespace App\Http\Controllers;

use App\Models\GameSetting;
use App\Models\MiniGameLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MiniGameController extends Controller
{
    // ─────────────────────────────────────────
    // VÒNG QUAY MAY MẮN
    // ─────────────────────────────────────────

    public function spinIndex()
    {
        if (!GameSetting::get('spin_enabled', '1')) {
            abort(503, 'Vòng Quay đang bảo trì.');
        }
        $history = MiniGameLog::where('user_id', auth()->id())
            ->where('game_type', 'spin')
            ->latest()
            ->take(50)
            ->get();
        return view('games.spin', compact('history'));
    }

    public function doSpin(Request $request)
    {
        if (!GameSetting::get('spin_enabled', '1')) {
            return response()->json(['success' => false, 'message' => 'Vòng Quay đang tạm dừng bởi Admin.']);
        }

        $request->validate(['bet_amount' => 'required|numeric|min:1']);

        $user   = auth()->user();
        $amount = (float) $request->bet_amount;

        // Kiểm tra max bet
        $maxBet = (float) GameSetting::get('spin_max_bet', '0');
        if ($maxBet > 0 && $amount > $maxBet) {
            return response()->json(['success' => false, 'message' => "Cược tối đa là {$maxBet} PT."]);
        }

        if ($user->balance_point < $amount) {
            return response()->json(['success' => false, 'message' => 'Số dư không đủ.']);
        }

        // ------- PRIZES -------
        // Lấy win rate target từ settings (ví dụ 40 = 40% người chơi thắng)
        $targetWinRate = (float) GameSetting::get('spin_win_rate_target', '40');

        // Phân phối xác suất dựa trên target win rate
        // x0 + x0.5 = phần thua; x1 = hoàn; x1.5~x5 = thắng
        // Base probs → tổng 100
        $prizes = [
            ['mult' => 0,   'label' => '💀 Mất tất', 'color' => '#374151'],
            ['mult' => 0.5, 'label' => '😅 x0.5',    'color' => '#6b21a8'],
            ['mult' => 1,   'label' => '😐 Hoàn lại', 'color' => '#1e40af'],
            ['mult' => 1.5, 'label' => '😊 x1.5',    'color' => '#065f46'],
            ['mult' => 2,   'label' => '🤩 x2',      'color' => '#92400e'],
            ['mult' => 3,   'label' => '🔥 x3',      'color' => '#991b1b'],
            ['mult' => 5,   'label' => '💎 x5',      'color' => '#7c3aed'],
        ];

        // Tính probs theo win_rate_target
        // "Thắng" = mult > 1 (index 3,4,5,6) ; "Hoàn" = index 2 ; "Thua" = index 0,1
        $winShare  = max(5, min(70, $targetWinRate));      // capped 5-70%
        $loseShare = 100 - $winShare;

        $probs = [
            round($loseShare * 0.55),   // x0: 55% of lose share
            round($loseShare * 0.45),   // x0.5: 45% of lose share
            max(5, round($winShare * 0.15)), // x1 hoàn
            round($winShare * 0.35),    // x1.5
            round($winShare * 0.30),    // x2
            round($winShare * 0.15),    // x3
            max(1, round($winShare * 0.05)), // x5
        ];
        // Normalize tổng = 100
        $sum = array_sum($probs);
        if ($sum < 100) $probs[3] += (100 - $sum);
        if ($sum > 100) $probs[0] -= ($sum - 100);

        // House edge: nếu user đang thắng quá nhiều theo ngưỡng của admin
        $houseEdgeEnabled = (bool)(int) GameSetting::get('spin_house_edge', '1');
        $winRateLimit     = (float) GameSetting::get('spin_win_rate_limit', '60');
        $recentWinRate    = $this->getRecentWinRate($user->id, 'spin', 10);

        if ($houseEdgeEnabled && $recentWinRate > ($winRateLimit / 100)) {
            $overHeat = $recentWinRate - ($winRateLimit / 100);
            $probs[0] = min(60, $probs[0] + (int)($overHeat * 60));
            $probs[5] = max(1, $probs[5] - 2);
            $probs[6] = 0;
        }

        $prize      = $this->weightedRandom($prizes, $probs);
        $prizeIndex = array_search($prize, $prizes, true);
        $payout     = round($amount * $prize['mult'], 2);
        $profit     = $payout - $amount;
        $won        = $profit > 0;

        DB::transaction(function () use ($user, $amount, $payout, $prize, $prizeIndex, $profit, $won) {
            $user->decrement('balance_point', $amount);
            if ($payout > 0) {
                $user->increment('balance_point', $payout);
            }
            MiniGameLog::create([
                'user_id'    => $user->id,
                'game_type'  => 'spin',
                'bet_amount' => $amount,
                'payout'     => $payout,
                'profit'     => $profit,
                'won'        => $won,
                'details'    => ['prize_index' => $prizeIndex, 'mult' => $prize['mult']],
            ]);
        });

        $newBalance = number_format((float) $user->fresh()->balance_point, 2);

        return response()->json([
            'success'     => true,
            'prize_index' => $prizeIndex,
            'prize_label' => $prize['label'],
            'mult'        => $prize['mult'],
            'profit'      => $profit,
            'payout'      => $payout,
            'new_balance' => $newBalance,
            'message'     => $won
                ? "🎉 {$prize['label']} — Nhận về {$payout} PT!"
                : ($prize['mult'] == 0 ? "💸 Mất hết {$amount} PT. Thử lại!" : "😅 Nhận lại {$payout} PT"),
        ]);
    }

    // ─────────────────────────────────────────
    // TÀI XỈU
    // ─────────────────────────────────────────

    public function diceIndex()
    {
        if (!GameSetting::get('dice_enabled', '1')) {
            abort(503, 'Tài Xỉu đang bảo trì.');
        }
        $history = MiniGameLog::where('user_id', auth()->id())
            ->where('game_type', 'dice')
            ->latest()
            ->take(50)
            ->get();
        return view('games.dice', compact('history'));
    }

    public function doDice(Request $request)
    {
        if (!GameSetting::get('dice_enabled', '1')) {
            return response()->json(['success' => false, 'message' => 'Tài Xỉu đang tạm dừng bởi Admin.']);
        }

        $request->validate([
            'bet_amount' => 'required|numeric|min:1',
            'bet_type'   => 'required|in:tai,xiu',
        ]);

        $user    = auth()->user();
        $amount  = (float) $request->bet_amount;
        $betType = $request->bet_type;

        $maxBet = (float) GameSetting::get('dice_max_bet', '0');
        if ($maxBet > 0 && $amount > $maxBet) {
            return response()->json(['success' => false, 'message' => "Cược tối đa là {$maxBet} PT."]);
        }

        if ($user->balance_point < $amount) {
            return response()->json(['success' => false, 'message' => 'Số dư không đủ.']);
        }

        $payoutMult = (float) GameSetting::get('dice_payout_mult', '1.95');

        // House edge
        $houseEdgeEnabled = (bool)(int) GameSetting::get('dice_house_edge', '1');
        $winRateLimit     = (float) GameSetting::get('dice_win_rate_limit', '65');
        $recentWinRate    = $this->getRecentWinRate($user->id, 'dice', 8);
        $forceLoseChance  = 0;
        if ($houseEdgeEnabled && $recentWinRate > ($winRateLimit / 100)) {
            $forceLoseChance = min(45, (int)(($recentWinRate - ($winRateLimit / 100)) * 130));
        }

        $d1    = mt_rand(1, 6);
        $d2    = mt_rand(1, 6);
        $d3    = mt_rand(1, 6);
        $total = $d1 + $d2 + $d3;

        $isTriplet = ($d1 === $d2 && $d2 === $d3);
        $result    = $total >= 11 ? 'tai' : 'xiu';

        if (!$isTriplet && $forceLoseChance > 0 && mt_rand(1, 100) <= $forceLoseChance) {
            $result = ($result === 'tai') ? 'xiu' : 'tai';
        }

        $won    = !$isTriplet && ($betType === $result);
        $payout = $won ? round($amount * $payoutMult, 2) : 0;
        $profit = $won ? ($payout - $amount) : -$amount;

        DB::transaction(function () use ($user, $amount, $payout, $won, $d1, $d2, $d3, $total, $isTriplet, $result, $profit, $betType) {
            $user->decrement('balance_point', $amount);
            if ($won) {
                $user->increment('balance_point', $payout);
            }
            MiniGameLog::create([
                'user_id'    => $user->id,
                'game_type'  => 'dice',
                'bet_amount' => $amount,
                'payout'     => $payout,
                'profit'     => $profit,
                'won'        => $won,
                'details'    => ['dice' => [$d1, $d2, $d3], 'total' => $total, 'result' => $result, 'triplet' => $isTriplet, 'bet_type' => $betType],
            ]);
        });

        $newBalance  = number_format((float) $user->fresh()->balance_point, 2);
        $resultLabel = $isTriplet ? '😱 Bão! (Banker thắng)' : ($result === 'tai' ? '🔴 TÀI' : '🔵 XỈU');

        return response()->json([
            'success'      => true,
            'dice'         => [$d1, $d2, $d3],
            'total'        => $total,
            'result'       => $result,
            'result_label' => $resultLabel,
            'is_triplet'   => $isTriplet,
            'won'          => $won,
            'payout'       => $payout,
            'profit'       => $profit,
            'new_balance'  => $newBalance,
            'message'      => $won
                ? "🎉 Thắng! {$resultLabel} (tổng {$total}) — +{$profit} PT"
                : ($isTriplet ? "😱 Bão! Tổng {$total} — Banker thắng!" : "💸 Thua! {$resultLabel} (tổng {$total})"),
        ]);
    }

    // ─────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────

    private function getRecentWinRate(int $userId, string $gameType, int $last = 10): float
    {
        $logs = MiniGameLog::where('user_id', $userId)
            ->where('game_type', $gameType)
            ->latest()
            ->take($last)
            ->get();

        if ($logs->isEmpty()) return 0.0;
        return $logs->where('won', true)->count() / $logs->count();
    }

    private function weightedRandom(array $items, array $weights): array
    {
        $total = array_sum($weights);
        $rand  = mt_rand(1, max($total, 1));
        $cumul = 0;
        foreach ($items as $i => $item) {
            $cumul += $weights[$i];
            if ($rand <= $cumul) return $item;
        }
        return end($items);
    }
}
