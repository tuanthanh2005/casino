<?php

namespace App\Http\Controllers;

use App\Models\GameSetting;
use App\Models\MiniGameLog;
use App\Services\GameRiskAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MiniGameController extends Controller
{
    private const RPS_CHOICES = ['keo', 'bua', 'bao'];

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

        GameRiskAlertService::notifyIfNeeded($user, 'spin', $amount, $payout, $profit, $won);

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

        $rollDice = static function (): array {
            $r1 = mt_rand(1, 6);
            $r2 = mt_rand(1, 6);
            $r3 = mt_rand(1, 6);
            $sum = $r1 + $r2 + $r3;

            return [
                'd1' => $r1,
                'd2' => $r2,
                'd3' => $r3,
                'total' => $sum,
                'isTriplet' => ($r1 === $r2 && $r2 === $r3),
                'result' => $sum >= 11 ? 'tai' : 'xiu',
            ];
        };

        $rolled = $rollDice();
        $d1 = $rolled['d1'];
        $d2 = $rolled['d2'];
        $d3 = $rolled['d3'];
        $total = $rolled['total'];
        $isTriplet = $rolled['isTriplet'];
        $result = $rolled['result'];

        if (!$isTriplet && $forceLoseChance > 0 && mt_rand(1, 100) <= $forceLoseChance) {
            for ($i = 0; $i < 30; $i++) {
                $candidate = $rollDice();

                if ($candidate['isTriplet']) {
                    continue;
                }

                if ($candidate['result'] === $betType) {
                    continue;
                }

                $d1 = $candidate['d1'];
                $d2 = $candidate['d2'];
                $d3 = $candidate['d3'];
                $total = $candidate['total'];
                $isTriplet = $candidate['isTriplet'];
                $result = $candidate['result'];
                break;
            }
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

        GameRiskAlertService::notifyIfNeeded($user, 'dice', $amount, $payout, $profit, $won);

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
    // KÉO BÚA BAO
    // ─────────────────────────────────────────

    public function rpsIndex()
    {
        if (!GameSetting::get('rps_enabled', '1')) {
            abort(503, 'Kéo Búa Bao đang bảo trì.');
        }

        $history = MiniGameLog::where('user_id', auth()->id())
            ->where('game_type', 'rps')
            ->latest()
            ->take(50)
            ->get();

        $today = now()->startOfDay();
        $week = now()->startOfWeek();

        $leaderboardToday = MiniGameLog::with('user')
            ->where('game_type', 'rps')
            ->where('created_at', '>=', $today)
            ->selectRaw('user_id, COUNT(*) as total_games, SUM(won) as total_wins, ROUND(SUM(won) * 100.0 / COUNT(*), 1) as win_rate, SUM(profit) as total_profit')
            ->groupBy('user_id')
            ->orderByDesc('total_profit')
            ->take(20)
            ->get();

        $leaderboardWeek = MiniGameLog::with('user')
            ->where('game_type', 'rps')
            ->where('created_at', '>=', $week)
            ->selectRaw('user_id, COUNT(*) as total_games, SUM(won) as total_wins, ROUND(SUM(won) * 100.0 / COUNT(*), 1) as win_rate, SUM(profit) as total_profit')
            ->groupBy('user_id')
            ->orderByDesc('total_profit')
            ->take(20)
            ->get();

        return view('games.rps', compact('history', 'leaderboardToday', 'leaderboardWeek'));
    }

    public function doRps(Request $request)
    {
        if (!GameSetting::get('rps_enabled', '1')) {
            return response()->json(['success' => false, 'message' => 'Kéo Búa Bao đang tạm dừng bởi Admin.']);
        }

        $request->validate([
            'bet_amount' => 'required|numeric|min:1',
            'choice' => 'required|in:keo,bua,bao',
            'mode' => 'nullable|in:single,bo3',
        ]);

        $user = auth()->user();
        $amount = (float) $request->bet_amount;
        $choice = (string) $request->choice;
        $mode = (string) ($request->mode ?: 'single');

        $maxBet = (float) GameSetting::get('rps_max_bet', '0');
        if ($maxBet > 0 && $amount > $maxBet) {
            return response()->json(['success' => false, 'message' => "Cược tối đa là {$maxBet} PT."]);
        }

        if ($user->balance_point < $amount) {
            return response()->json(['success' => false, 'message' => 'Số dư không đủ.']);
        }

        $winRateTarget = (float) GameSetting::get('rps_win_rate_target', '45');
        $winRateTarget = max(5, min(90, $winRateTarget));
        $monthlyWinRateTarget = (float) GameSetting::get('rps_monthly_win_rate_target', (string) $winRateTarget);
        $monthlyWinRateTarget = max(5, min(90, $monthlyWinRateTarget));
        $drawRate = (float) GameSetting::get('rps_draw_rate', '10');
        $drawRate = max(0, min(40, $drawRate));

        $houseEdgeEnabled = (bool) (int) GameSetting::get('rps_house_edge', '1');
        $winRateLimit = (float) GameSetting::get('rps_win_rate_limit', '65');
        $recentWinRate = $this->getRecentWinRate($user->id, 'rps', 12);
        if ($houseEdgeEnabled && $recentWinRate > ($winRateLimit / 100)) {
            $over = $recentWinRate - ($winRateLimit / 100);
            $winRateTarget = max(5, $winRateTarget - min(25, $over * 100));
        }

        // Monthly target control to keep long-run RPS rate close to admin setting.
        $monthStart = now()->startOfMonth();
        $monthBase = MiniGameLog::where('game_type', 'rps')
            ->where('created_at', '>=', $monthStart);
        $monthGames = (int) (clone $monthBase)->count();
        if ($monthGames >= 30) {
            $monthWins = (int) (clone $monthBase)->where('won', true)->count();
            $monthWinRate = $monthWins * 100 / max(1, $monthGames);
            $delta = $monthWinRate - $monthlyWinRateTarget;

            if ($delta > 0) {
                $winRateTarget = max(5, $winRateTarget - min(20, $delta * 0.6));
            } elseif ($delta < 0) {
                $winRateTarget = min(90, $winRateTarget + min(10, abs($delta) * 0.3));
            }
        }

        $singlePayoutMult = (float) GameSetting::get('rps_single_payout_mult', '1.95');
        $bo3PayoutMult = (float) GameSetting::get('rps_bo3_payout_mult', '2.70');

        $rounds = [];
        $userWins = 0;
        $botWins = 0;
        $draws = 0;

        $targetWins = $mode === 'bo3' ? 2 : 1;
        $maxRounds = $mode === 'bo3' ? 9 : 1;

        for ($round = 1; $round <= $maxRounds; $round++) {
            if ($userWins >= $targetWins || $botWins >= $targetWins) {
                break;
            }

            $roll = random_int(1, 10000);
            $winThreshold = (int) round($winRateTarget * 100);
            $drawThreshold = (int) round(($winRateTarget + $drawRate) * 100);

            if ($roll <= $winThreshold) {
                $expected = 'win';
            } elseif ($roll <= $drawThreshold) {
                $expected = 'draw';
            } else {
                $expected = 'lose';
            }

            $botChoice = $this->buildRpsBotChoice($choice, $expected);
            $result = $this->judgeRpsRound($choice, $botChoice);

            if ($result === 'win') {
                $userWins++;
            } elseif ($result === 'lose') {
                $botWins++;
            } else {
                $draws++;
            }

            $rounds[] = [
                'round' => $round,
                'player' => $choice,
                'bot' => $botChoice,
                'result' => $result,
            ];

            if ($mode === 'single') {
                break;
            }
        }

        $final = 'draw';
        if ($userWins > $botWins) {
            $final = 'win';
        } elseif ($botWins > $userWins) {
            $final = 'lose';
        }

        if ($mode === 'single') {
            $payout = $final === 'win'
                ? round($amount * $singlePayoutMult, 2)
                : ($final === 'draw' ? round($amount, 2) : 0);
        } else {
            $payout = $final === 'win' ? round($amount * $bo3PayoutMult, 2) : 0;
        }

        $profit = $payout - $amount;
        $won = $profit > 0;

        DB::transaction(function () use ($user, $amount, $payout, $profit, $won, $mode, $choice, $rounds, $final, $userWins, $botWins, $draws) {
            $user->decrement('balance_point', $amount);
            if ($payout > 0) {
                $user->increment('balance_point', $payout);
            }

            MiniGameLog::create([
                'user_id' => $user->id,
                'game_type' => 'rps',
                'bet_amount' => $amount,
                'payout' => $payout,
                'profit' => $profit,
                'won' => $won,
                'details' => [
                    'mode' => $mode,
                    'choice' => $choice,
                    'rounds' => $rounds,
                    'final' => $final,
                    'score' => [
                        'user' => $userWins,
                        'bot' => $botWins,
                        'draw' => $draws,
                    ],
                ],
            ]);
        });

        GameRiskAlertService::notifyIfNeeded($user, 'rps', $amount, $payout, $profit, $won);

        $newBalance = number_format((float) $user->fresh()->balance_point, 2);

        return response()->json([
            'success' => true,
            'mode' => $mode,
            'choice' => $choice,
            'rounds' => $rounds,
            'final' => $final,
            'score' => ['user' => $userWins, 'bot' => $botWins, 'draw' => $draws],
            'payout' => $payout,
            'profit' => $profit,
            'won' => $won,
            'new_balance' => $newBalance,
            'message' => $final === 'win'
                ? "🎉 Bạn thắng! +{$profit} PT"
                : ($final === 'draw' ? "🤝 Hòa! Hoàn cược {$payout} PT" : "💸 Bạn thua!"),
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

    private function judgeRpsRound(string $player, string $bot): string
    {
        if ($player === $bot) {
            return 'draw';
        }

        $beats = [
            'keo' => 'bao',
            'bao' => 'bua',
            'bua' => 'keo',
        ];

        return ($beats[$player] ?? '') === $bot ? 'win' : 'lose';
    }

    private function buildRpsBotChoice(string $player, string $expected): string
    {
        if ($expected === 'draw') {
            return $player;
        }

        if ($expected === 'win') {
            return match ($player) {
                'keo' => 'bao',
                'bua' => 'keo',
                default => 'bua',
            };
        }

        return match ($player) {
            'keo' => 'bua',
            'bua' => 'bao',
            default => 'keo',
        };
    }

}
