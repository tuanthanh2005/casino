<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'point_price',
        'image',
        'status',
    ];

    protected $casts = [
        'point_price' => 'decimal:2',
    ];

    public function exchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset($this->image);
        }
        return asset('images/gift-default.png');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
