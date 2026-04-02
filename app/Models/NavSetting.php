<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NavSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null): mixed
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function all_settings(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /** Tạo VietQR URL */
    public static function vietQrUrl(string $orderCode, float $amount): string
    {
        $bin     = static::get('bank_bin', '970422');
        $account = static::get('bank_account', '0783704196');
        $name    = urlencode(static::get('bank_owner', 'TRAN THANH TUAN'));
        $content = urlencode($orderCode);
        $amt     = (int) $amount;
        return "https://img.vietqr.io/image/{$bin}-{$account}-compact2.png?amount={$amt}&addInfo={$content}&accountName={$name}";
    }
}
