<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        if (!$this->image) {
            return asset('images/gift-default.png');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return Storage::disk('public_uploads')->url(ltrim($this->image, '/'));
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
