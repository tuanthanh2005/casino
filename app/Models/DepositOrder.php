<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositOrder extends Model
{
    protected $fillable = [
        'user_id','order_code','method','amount','points_credited','status',
        'card_type','card_serial','card_pin','card_amount',
        'admin_note','approved_by','approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 1 PT = 1 VNĐ (admin có thể tuỳ chỉnh sau) */
    public static function calcPoints(float $amount): float
    {
        return $amount; // 1:1
    }

    public static function generateCode(): string
    {
        do {
            $code = 'CTB' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('order_code', $code)->exists());
        return $code;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => '⏳ Chờ duyệt',
            'approved' => '✅ Đã duyệt',
            'rejected' => '❌ Từ chối',
            default    => $this->status,
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'bank_qr' => '🏦 QR Bank',
            'card'    => '🎴 Thẻ cào',
            default   => $this->method,
        };
    }
}
