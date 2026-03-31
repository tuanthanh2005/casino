<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GameSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = ['key', 'value', 'description'];

    /** Lấy giá trị setting, có cache 60s */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::find($key);
        return $row ? $row->value : $default;
    }

    /** Cập nhật hoặc tạo mới setting */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Lấy nhiều settings một lúc dưới dạng key→value array */
    public static function getMany(array $keys): array
    {
        return static::whereIn('key', $keys)->pluck('value', 'key')->toArray();
    }

    /** Lấy tất cả settings */
    public static function all($columns = ['*'])
    {
        return parent::all($columns)->keyBy('key');
    }
}
