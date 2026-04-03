<?php

namespace App\Services;

use App\Models\Bet;
use App\Models\GameSetting;
use App\Models\MiniGameLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class GameRiskAlertService
{
    public static function notifyIfNeeded(User $user, string $gameType, float $amount, float $payout, float $profit, bool $won): void
    {
        if (! $won || $profit <= 0) {
            return;
        }

        $windowRounds = max(10, min(100, (int) GameSetting::get('tg_alert_window_rounds', '30')));
        $streakThreshold = max(3, min(20, (int) GameSetting::get('tg_alert_win_streak', '5')));
        $highMultThreshold = max(2.0, min(20.0, (float) GameSetting::get('tg_alert_high_mult', '4')));
        $highMultHitsThreshold = max(2, min(30, (int) GameSetting::get('tg_alert_high_mult_hits', '5')));
        $profitThreshold = max(100, (float) GameSetting::get('tg_alert_profit_threshold', '5000'));
        $cooldownMinutes = max(1, min(120, (int) GameSetting::get('tg_alert_cooldown_minutes', '15')));

        $summary = self::buildSummary($user->id, $gameType, $windowRounds, $highMultThreshold);
        if ($summary === null || $summary['windowCount'] <= 0) {
            return;
        }

        $windowCount = $summary['windowCount'];
        $windowWins = $summary['windowWins'];
        $windowWinRate = $summary['windowWinRate'];
        $windowProfit = $summary['windowProfit'];
        $streak = $summary['streak'];
        $highMultHits = $summary['highMultHits'];
        $maxMult = $summary['maxMult'];
        $currentMult = $amount > 0 ? round($payout / $amount, 2) : 0.0;

        $reasons = [];
        if ($streak >= $streakThreshold) {
            $reasons[] = "thang lien tiep {$streak} van";
        }

        if ($currentMult >= $highMultThreshold && $highMultHits >= $highMultHitsThreshold) {
            $reasons[] = "an >= x{$highMultThreshold} {$highMultHits} lan / {$windowCount} van gan nhat";
        }

        if ($windowProfit >= $profitThreshold && $windowWinRate >= 65) {
            $reasons[] = "loi nhuan {$windowProfit} PT voi ti le thang {$windowWinRate}% / {$windowCount} van";
        }

        if (empty($reasons)) {
            return;
        }

        $cooldownKey = 'tg-risk-alert:user:' . $user->id . ':' . $gameType . ':' . md5(implode('|', $reasons));
        if (Cache::has($cooldownKey)) {
            return;
        }

        Cache::put($cooldownKey, true, now()->addMinutes($cooldownMinutes));

        $name = htmlspecialchars((string) ($user->name ?? 'Unknown'), ENT_QUOTES, 'UTF-8');
        $game = strtoupper($gameType);
        $reasonsText = implode('; ', $reasons);

        $message = "🚨 <b>CANH BAO NGUOI CHOI DANG AN MANH</b>\n"
            . "👤 User: <b>{$name}</b> (ID: {$user->id})\n"
            . "🎮 Game: <b>{$game}</b>\n"
            . "🧾 Van moi: cuoc <b>{$amount}</b> PT, tra <b>{$payout}</b> PT (x{$currentMult})\n"
            . "📊 {$windowCount} van gan nhat: win {$windowWins}/{$windowCount} ({$windowWinRate}%), profit {$windowProfit} PT, max x{$maxMult}\n"
            . "⚠️ Ly do: {$reasonsText}\n"
            . "⏱️ " . now()->format('H:i d/m/Y');

        TelegramNotifier::send($message);
    }

    private static function buildSummary(int $userId, string $gameType, int $windowRounds, float $highMultThreshold): ?array
    {
        if (in_array($gameType, ['spin', 'dice', 'rps'], true)) {
            $recent = MiniGameLog::where('user_id', $userId)
                ->where('game_type', $gameType)
                ->latest('id')
                ->take($windowRounds)
                ->get(['bet_amount', 'payout', 'profit', 'won']);

            return self::summarizeRows($recent, $highMultThreshold, static function ($row): bool {
                return (bool) $row->won;
            }, static function ($row): float {
                return (float) $row->bet_amount;
            }, static function ($row): float {
                return (float) $row->payout;
            }, static function ($row): float {
                return (float) $row->profit;
            });
        }

        if ($gameType === 'btc') {
            $recent = Bet::where('user_id', $userId)
                ->whereIn('status', ['won', 'lost'])
                ->latest('id')
                ->take($windowRounds)
                ->get(['bet_amount', 'profit', 'status']);

            return self::summarizeRows($recent, $highMultThreshold, static function ($row): bool {
                return $row->status === 'won';
            }, static function ($row): float {
                return (float) $row->bet_amount;
            }, static function ($row): float {
                return $row->status === 'won' ? (float) $row->profit : 0.0;
            }, static function ($row): float {
                $bet = (float) $row->bet_amount;
                if ($row->status === 'won') {
                    return ((float) $row->profit) - $bet;
                }

                return -$bet;
            });
        }

        return null;
    }

    private static function summarizeRows($rows, float $highMultThreshold, callable $isWon, callable $betAmount, callable $payout, callable $profit): array
    {
        $windowCount = (int) $rows->count();
        if ($windowCount === 0) {
            return [
                'windowCount' => 0,
                'windowWins' => 0,
                'windowWinRate' => 0.0,
                'windowProfit' => 0.0,
                'streak' => 0,
                'highMultHits' => 0,
                'maxMult' => 0.0,
            ];
        }

        $windowWins = 0;
        $windowProfit = 0.0;
        $streak = 0;
        $highMultHits = 0;
        $maxMult = 0.0;
        $streakBroken = false;

        foreach ($rows as $row) {
            $won = (bool) $isWon($row);

            if (! $streakBroken) {
                if ($won) {
                    $streak++;
                } else {
                    $streakBroken = true;
                }
            }

            if ($won) {
                $windowWins++;
            }

            $windowProfit += (float) $profit($row);

            $bet = (float) $betAmount($row);
            if ($bet <= 0) {
                continue;
            }

            $mult = round(((float) $payout($row)) / $bet, 2);
            $maxMult = max($maxMult, $mult);

            if ($won && $mult >= $highMultThreshold) {
                $highMultHits++;
            }
        }

        return [
            'windowCount' => $windowCount,
            'windowWins' => $windowWins,
            'windowWinRate' => round(($windowWins * 100) / max(1, $windowCount), 1),
            'windowProfit' => round($windowProfit, 2),
            'streak' => $streak,
            'highMultHits' => $highMultHits,
            'maxMult' => $maxMult,
        ];
    }
}
