<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FarmCrop extends Model
{
    protected $fillable = [
        'user_id','seed_type_id','slot_number','status',
        'planted_at','ripe_at','last_watered_at',
        'watering_count','harvest_qty','died_at','delete_at',
    ];

    protected $casts = [
        'planted_at'      => 'datetime',
        'ripe_at'         => 'datetime',
        'last_watered_at' => 'datetime',
        'died_at'         => 'datetime',
        'delete_at'       => 'datetime',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function seedType() { return $this->belongsTo(SeedType::class); }

    /** Còn trong cooldown tưới không? */
    public function isWaterOnCooldown(): bool
    {
        if (!$this->last_watered_at) return false;
        return $this->last_watered_at->diffInSeconds(now()) < 600; // 10 phút
    }

    public function waterCooldownRemainingSeconds(): int
    {
        if (!$this->last_watered_at) return 0;
        return max(0, 600 - (int) $this->last_watered_at->diffInSeconds(now()));
    }

    public function canWater(): bool
    {
        return $this->status === 'growing'
            && $this->watering_count < $this->seedType->max_waterings
            && !$this->isWaterOnCooldown();
    }

    public function isRipe(): bool
    {
        if ($this->isDead()) return false;
        return $this->status === 'ripe' || ($this->status === 'growing' && $this->ripe_at->lte(now()));
    }

    public function isDead(): bool
    {
        if ($this->status === 'dead') return true;
        if (($this->status === 'ripe' || $this->status === 'growing') && $this->ripe_at) {
            // Nới thêm 1 phút ân hạn cho người chơi
            if ($this->ripe_at->lte(now()) && $this->ripe_at->copy()->addHours(12)->addMinutes(1)->isPast()) {
                return true;
            }
        }
        return false;
    }

    public function progressPercent(): int
    {
        if ($this->isRipe()) return 100;
        if ($this->isDead()) return 0;
        $total   = max(1, $this->planted_at->diffInSeconds($this->ripe_at));
        $elapsed = $this->planted_at->diffInSeconds(now());
        return min(100, (int)(($elapsed / $total) * 100));
    }

    public function timeRemainingSeconds(): int
    {
        if ($this->status !== 'growing') return 0;
        return max(0, (int) now()->diffInSeconds($this->ripe_at, false) * -1);
    }

    public function secondsUntilRipe(): int
    {
        if ($this->status !== 'growing') return 0;
        $diff = now()->diffInSeconds($this->ripe_at, false);
        return $diff < 0 ? 0 : (int) $diff;
    }

    /** Ô này có thể trồng (không có cây active) */
    public static function isSlotFree(int $userId, int $slot): bool
    {
        return !static::where('user_id', $userId)
            ->where('slot_number', $slot)
            ->exists();
    }
}
