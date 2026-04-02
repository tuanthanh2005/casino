<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeedType extends Model
{
    protected $fillable = [
        'name','slug','emoji','image_path','description',
        'price_buy','price_sell_base','grow_time_mins',
        'max_waterings','lucky_chance','is_active','sort_order',
    ];

    public function farmInventories() { return $this->hasMany(FarmInventory::class); }
    public function farmCrops()       { return $this->hasMany(FarmCrop::class); }
    public function farmTransactions(){ return $this->hasMany(FarmTransaction::class); }

    /** Giá có thuế random (lưu vào session bên ngoài) */
    public function getRandomPrice(float $modifier): float
    {
        return round($this->price_sell_base * $modifier, 2);
    }

    /** Thời gian chín dạng text */
    public function getGrowTimeTextAttribute(): string
    {
        if ($this->grow_time_mins < 60) return $this->grow_time_mins . ' phút';
        $h = floor($this->grow_time_mins / 60);
        $m = $this->grow_time_mins % 60;
        return $m > 0 ? "{$h}h {$m}p" : "{$h} giờ";
    }
}
