<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmTransaction extends Model
{
    protected $fillable = [
        'user_id','seed_type_id','type','quantity',
        'unit_price_pt','total_pt','price_modifier','note',
    ];
    public function seedType() { return $this->belongsTo(SeedType::class); }
    public function user()     { return $this->belongsTo(User::class); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'buy_seed'   => '🛒 Mua hạt',
            'harvest'    => '🌾 Thu hoạch',
            'sell_fruit' => '💰 Bán trái',
            default      => $this->type,
        };
    }
}
