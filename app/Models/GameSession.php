<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'start_price',
        'end_price',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'start_price' => 'decimal:2',
        'end_price' => 'decimal:2',
    ];

    public function bets()
    {
        return $this->hasMany(Bet::class, 'session_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Determine if price went UP (LONG wins) or DOWN (SHORT wins)
     */
    public function getDirectionAttribute(): ?string
    {
        if ($this->start_price && $this->end_price) {
            return $this->end_price > $this->start_price ? 'long' : 'short';
        }
        return null;
    }
}
