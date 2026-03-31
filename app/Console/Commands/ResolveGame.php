<?php

namespace App\Console\Commands;

use App\Models\Bet;
use App\Models\GameSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResolveGame extends Command
{
    protected $signature = 'game:resolve';
    protected $description = 'Chốt kết quả phiên cược BTC đang pending và tạo phiên mới';

    public function handle()
    {
        $this->info('=== Bắt đầu chốt phiên cược ===');

        // Lấy giá BTC hiện tại từ Binance
        $currentPrice = $this->getBtcPrice();

        if ($currentPrice <= 0) {
            $this->error('Không thể lấy giá từ Binance API. Bỏ qua.');
            return 1;
        }

        $this->info("Giá BTC hiện tại: $" . number_format($currentPrice, 2));

        // Lấy tất cả session đang pending VÀ đã qua end_time
        $sessions = GameSession::where('status', 'pending')
            ->where('end_time', '<=', now())
            ->get();

        if ($sessions->isEmpty()) {
            $this->info('Không có phiên nào cần chốt lúc này.');
        }

        foreach ($sessions as $session) {
            $this->resolveSession($session, $currentPrice);
        }

        // Tạo phiên mới nếu không còn phiên pending
        $hasPending = GameSession::where('status', 'pending')->exists();
        if (!$hasPending) {
            $newSession = GameSession::create([
                'start_time' => now(),
                'end_time' => now()->addMinutes(1),
                'start_price' => $currentPrice,
                'status' => 'pending',
            ]);
            $this->info("Tạo phiên mới #{$newSession->id} với giá mở $" . number_format($currentPrice, 2));
        }

        $this->info('=== Hoàn tất ===');
        return 0;
    }

    private function resolveSession(GameSession $session, float $endPrice): void
    {
        DB::transaction(function () use ($session, $endPrice) {
            $session->update([
                'end_price' => $endPrice,
                'end_time' => now(),
                'status' => 'completed',
            ]);

            $direction = $endPrice > $session->start_price ? 'long' : 'short';
            $this->line("Phiên #{$session->id}: {$session->start_price} → {$endPrice} | Hướng: " . strtoupper($direction));

            $bets = Bet::where('session_id', $session->id)
                ->where('status', 'pending')
                ->with('user')
                ->get();

            $wins = 0;
            $losses = 0;

            foreach ($bets as $bet) {
                if ($bet->bet_type === $direction) {
                    // THẮNG: hoàn vốn + x1.95 (phí sàn 5%)
                    $profit = round($bet->bet_amount * 1.95, 2);
                    $bet->update(['status' => 'won', 'profit' => $profit]);
                    $bet->user->increment('balance_point', $profit);
                    $wins++;
                } else {
                    // THUA: mất vốn (đã trừ lúc đặt)
                    $bet->update(['status' => 'lost', 'profit' => 0]);
                    $losses++;
                }
            }

            $this->info("  ✓ {$wins} người thắng, {$losses} người thua");
        });
    }

    private function getBtcPrice(): float
    {
        try {
            $ch = curl_init('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
            return (float) ($data['price'] ?? 0);
        } catch (\Exception $e) {
            $this->error('Lỗi API Binance: ' . $e->getMessage());
            return 0;
        }
    }
}
