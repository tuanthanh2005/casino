<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmInventory extends Model
{
    protected $table = 'farm_inventory';
    protected $fillable = ['user_id', 'seed_type_id', 'quantity', 'expires_at', 'warned_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'warned_at'  => 'datetime',
    ];

    public function seedType() { return $this->belongsTo(SeedType::class); }
    public function user()     { return $this->belongsTo(User::class); }
}
