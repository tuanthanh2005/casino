<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $defaults = [
            ['key' => 'spin_enabled',        'value' => '1',    'description' => 'Bật/tắt Vòng Quay (1=bật, 0=tắt)'],
            ['key' => 'spin_house_edge',      'value' => '1',    'description' => 'Bật house edge Vòng Quay (1=bật, 0=tắt)'],
            ['key' => 'spin_win_rate_limit',  'value' => '60',   'description' => 'Win rate tối đa Vòng Quay (%) trước khi house edge kích hoạt'],
            ['key' => 'spin_win_rate_target', 'value' => '40',   'description' => 'Tỷ lệ thắng mục tiêu Vòng Quay (%) — dùng để set xác suất cơ bản'],
            ['key' => 'dice_enabled',         'value' => '1',    'description' => 'Bật/tắt Tài Xỉu (1=bật, 0=tắt)'],
            ['key' => 'dice_house_edge',      'value' => '1',    'description' => 'Bật house edge Tài Xỉu (1=bật, 0=tắt)'],
            ['key' => 'dice_win_rate_limit',  'value' => '65',   'description' => 'Win rate tối đa Tài Xỉu (%) trước khi house edge kích hoạt'],
            ['key' => 'dice_payout_mult',     'value' => '1.95', 'description' => 'Hệ số trả thưởng Tài Xỉu'],
            ['key' => 'spin_max_bet',         'value' => '10000','description' => 'Cược tối đa Vòng Quay (PT, 0=không giới hạn)'],
            ['key' => 'dice_max_bet',         'value' => '10000','description' => 'Cược tối đa Tài Xỉu (PT, 0=không giới hạn)'],
        ];

        foreach ($defaults as $row) {
            DB::table('game_settings')->insert($row + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('game_settings');
    }
};
