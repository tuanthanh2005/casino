<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'reward_item_id',
        'points_spent',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'points_spent' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rewardItem()
    {
        return $this->belongsTo(RewardItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => '<span class="badge bg-success">Đã duyệt</span>',
            'rejected' => '<span class="badge bg-danger">Từ chối</span>',
            default => '<span class="badge bg-warning text-dark">Chờ duyệt</span>',
        };
    }
}
