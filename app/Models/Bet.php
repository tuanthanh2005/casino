<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'bet_type',
        'bet_amount',
        'status',
        'profit',
    ];

    protected $casts = [
        'bet_amount' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(GameSession::class, 'session_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'won' => '<span class="badge bg-success">Thắng</span>',
            'lost' => '<span class="badge bg-danger">Thua</span>',
            default => '<span class="badge bg-warning text-dark">Chờ kết quả</span>',
        };
    }

    public function getBetTypeLabelAttribute(): string
    {
        return $this->bet_type === 'long'
            ? '<span class="badge bg-success">▲ LONG</span>'
            : '<span class="badge bg-danger">▼ SHORT</span>';
    }
}
