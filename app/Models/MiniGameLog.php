<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiniGameLog extends Model
{
    protected $fillable = [
        'user_id', 'game_type', 'bet_amount', 'payout', 'profit', 'won', 'details',
    ];

    protected $casts = [
        'won'     => 'boolean',
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
