<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class NavOrder extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'order_code', 'status', 'payment_method',
        'tiktok_username', 'registered_email', 'registered_phone',
        'violation_type', 'violation_date', 'follower_count', 'account_notes',
        'id_card_front', 'id_card_back', 'screenshot_path',
        'customer_name', 'customer_contact',
        'amount', 'transfer_content',
        'payment_confirmed_at', 'payment_verified_at',
        'appeal_deadline', 'admin_notes', 'appeal_sent_at',
    ];

    protected $casts = [
        'violation_date'       => 'date',
        'appeal_deadline'      => 'date',
        'payment_confirmed_at' => 'datetime',
        'payment_verified_at'  => 'datetime',
        'appeal_sent_at'       => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(NavService::class, 'service_id');
    }

    public static function generateCode(): string
    {
        do {
            $code = 'NAV' . strtoupper(substr(md5(uniqid()), 0, 7));
        } while (static::where('order_code', $code)->exists());
        return $code;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_payment' => '⏳ Chờ thanh toán',
            'paid'            => '💰 Đã thanh toán',
            'processing'      => '🔄 Đang xử lý',
            'completed'       => '✅ Hoàn thành',
            'cancelled'       => '❌ Đã huỷ',
            default           => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending_payment' => 'badge-warning',
            'paid'            => 'badge-primary',
            'processing'      => 'badge-primary',
            'completed'       => 'badge-success',
            'cancelled'       => 'badge-danger',
            default           => 'badge-warning',
        };
    }

    public function getDaysLeftAttribute(): ?int
    {
        if (!$this->appeal_deadline) return null;
        $diff = now()->diffInDays($this->appeal_deadline, false);
        return (int) $diff;
    }

    public function isExpired(): bool
    {
        if (!$this->appeal_deadline) return false;
        return $this->appeal_deadline->isPast();
    }

    public function getIdCardFrontUrlAttribute(): ?string
    {
        return $this->resolveUploadUrl($this->id_card_front);
    }

    public function getIdCardBackUrlAttribute(): ?string
    {
        return $this->resolveUploadUrl($this->id_card_back);
    }

    public function getScreenshotPathUrlAttribute(): ?string
    {
        return $this->resolveUploadUrl($this->screenshot_path);
    }

    private function resolveUploadUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public_uploads')->url(ltrim($path, '/'));
    }

    /** Generate appeal letter text for TikTok */
    public function generateAppealLetter(): string
    {
        $service = $this->service;
        $serviceType = $service ? $service->name : 'Account Issue';
        $violationDate = $this->violation_date ? $this->violation_date->format('F d, Y') : '[date unknown]';
        $followers = $this->follower_count ? number_format($this->follower_count) : 'N/A';

        // Subject line based on service type
        $subjects = [
            'khoa-vinh-vien'    => 'Appeal for Permanently Disabled Account',
            'khoa-tam-thoi'     => 'Appeal for Temporarily Suspended Account',
            'go-video'          => 'Appeal for Removed Content',
            'han-che-live'      => 'Appeal for LIVE Streaming Restriction',
            'han-che-shop'      => 'Appeal for TikTok Shop Payment Restriction',
        ];
        $slug = $service ? $service->slug : '';
        $subject = $subjects[$slug] ?? 'Appeal for Account Issue';

        $bodyByType = [
            'khoa-vinh-vien' => "My TikTok account @{$this->tiktok_username} has been permanently disabled. I am writing to respectfully appeal this decision.\n\nI understand there was a violation of TikTok's Community Guidelines on {$violationDate}. However, this was an ISOLATED, single incident — NOT a pattern of repeated behavior. I have never received any prior warnings or strikes on this account.",
            'khoa-tam-thoi'  => "My TikTok account @{$this->tiktok_username} has been temporarily suspended. I am writing to appeal this suspension and request early reinstatement.\n\nI acknowledge the violation that occurred on {$violationDate} and sincerely apologize. This was an isolated mistake and does not reflect my typical content.",
            'go-video'       => "A video on my TikTok account @{$this->tiktok_username} has been removed. I believe this removal may have been made in error or without full context, and I am respectfully requesting a review.\n\nThe content was removed around {$violationDate}. I was not aware that this content violated any specific Community Guidelines.",
            'han-che-live'   => "My TikTok account @{$this->tiktok_username} has been restricted from LIVE streaming. I am writing to appeal this restriction and request its removal.\n\nThe restriction appears to have been applied around {$violationDate}. I have always strived to conduct LIVE sessions in accordance with TikTok's Community Guidelines.",
            'han-che-shop'   => "My TikTok Shop account associated with @{$this->tiktok_username} has been restricted from payment features. I am writing to appeal this restriction.\n\nI have been operating my TikTok Shop in good faith and in compliance with all applicable policies. The restriction was applied around {$violationDate}.",
        ];
        $body = $bodyByType[$slug] ?? "My TikTok account @{$this->tiktok_username} is experiencing an issue. I am writing to request a review and resolution.";

        $notes = $this->account_notes ? "\nAdditional context: {$this->account_notes}\n" : '';

        return <<<LETTER
Subject: {$subject} - @{$this->tiktok_username}

Dear TikTok Trust & Safety Team,

{$body}

Account Information:
- Username: @{$this->tiktok_username}
- Registered Email: {$this->registered_email}
- Registered Phone: {$this->registered_phone}
- Follower Count: {$followers}
- Date of Issue: {$violationDate}
{$notes}
I want to be clear about my commitment going forward:
✓ I have a full understanding of TikTok's Community Guidelines
✓ I sincerely apologize for any content that violated these guidelines
✓ I commit to ensuring all future content fully complies with TikTok's policies
✓ I have been an active, good-faith member of the TikTok community

I kindly request a manual review of my account by a member of your Trust & Safety team. I am confident that upon review, the decision can be reconsidered.

Please feel free to contact me at {$this->registered_email} or {$this->registered_phone} if you require any additional information or verification.

Thank you sincerely for your time and consideration.

Best regards,
{$this->customer_name}
Contact: {$this->customer_contact}

---
Note: Identity verification documents (ID card) have been attached to this appeal.
LETTER;
    }
}
