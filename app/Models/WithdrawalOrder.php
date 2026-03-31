<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalOrder extends Model
{
    protected $fillable = [
        'user_id','order_code','method','points_used','tax_rate','tax_amount','net_amount','status',
        'bank_name','bank_account','bank_holder',
        'card_type','admin_note','approved_by','approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'RUT' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('order_code', $code)->exists());
        return $code;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => '⏳ Chờ xử lý',
            'approved' => '✅ Đã hoàn thành',
            'rejected' => '❌ Từ chối',
            default    => $this->status,
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'bank_transfer' => '🏦 Chuyển khoản',
            'card'          => '🎴 Đổi thẻ cào',
            default         => $this->method,
        };
    }
}
