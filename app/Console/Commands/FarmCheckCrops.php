<?php

namespace App\Console\Commands;

use App\Models\FarmCrop;
use App\Models\FarmNotification;
use Illuminate\Console\Command;

class FarmCheckCrops extends Command
{
    protected $signature   = 'farm:check-crops';
    protected $description = 'Kiểm tra cây chết (12h không tưới) và cây chín, xóa cây đã hết 24h chết';

    public function handle(): int
    {
        $now = now();

        // 1. XÓA cây đã chết và hết 24h (delete_at <= now)
        $deleted = FarmCrop::where('status', 'dead')
            ->where('delete_at', '<=', $now)
            ->delete();
        if ($deleted) $this->info("[⬛] Đã xóa {$deleted} cây hết hạn chờ.");

        // 2. GIẾT cây chưa được tưới 12 giờ
        $deadThreshold = $now->copy()->subHours(12);
        $dyingCrops    = FarmCrop::where('status', 'growing')
            ->where('last_watered_at', '<=', $deadThreshold)
            ->with('seedType')
            ->get();

        foreach ($dyingCrops as $crop) {
            $crop->update([
                'status'    => 'dead',
                'died_at'   => $now,
                'delete_at' => $now->copy()->addHours(24),
            ]);
            FarmNotification::create([
                'user_id'     => $crop->user_id,
                'farm_crop_id'=> $crop->id,
                'type'        => 'dead',
                'message'     => "💀 Cây {$crop->seedType->emoji} {$crop->seedType->name} (ô #{$crop->slot_number}) đã chết vì không được tưới trong 12 tiếng! Sẽ biến mất sau 24h.",
            ]);
            $this->warn("[💀] Cây #{$crop->id} (user {$crop->user_id}) đã chết.");
        }

        // 3. ĐÁNH DẤU CHÍN các cây đã đến ripe_at
        $ripeCrops = FarmCrop::where('status', 'growing')
            ->where('ripe_at', '<=', $now)
            ->with('seedType')
            ->get();

        foreach ($ripeCrops as $crop) {
            $crop->update(['status' => 'ripe']);
            // Chỉ tạo 1 thông báo chín
            $alreadyNotified = FarmNotification::where('farm_crop_id', $crop->id)
                ->where('type', 'ripe')->exists();
            if (!$alreadyNotified) {
                FarmNotification::create([
                    'user_id'     => $crop->user_id,
                    'farm_crop_id'=> $crop->id,
                    'type'        => 'ripe',
                    'message'     => "✅ Cây {$crop->seedType->emoji} {$crop->seedType->name} (ô #{$crop->slot_number}) đã chín! Vào thu hoạch ngay nhé!",
                ]);
            }
            $this->info("[✅] Cây #{$crop->id} đã chín.");
        }

        $this->info("Farm check xong: {$deleted} xóa, {$dyingCrops->count()} chết, {$ripeCrops->count()} chín.");
        return Command::SUCCESS;
    }
}
