<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavService extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'requirements',
        'price', 'appeal_deadline_days', 'icon', 'color',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'decimal:0',
    ];

    public function orders()
    {
        return $this->hasMany(NavOrder::class, 'service_id');
    }

    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . ' PT/VNĐ';
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }
}
