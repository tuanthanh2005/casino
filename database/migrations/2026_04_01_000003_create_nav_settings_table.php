<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nav_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed mặc định VietQR MB Bank
        DB::table('nav_settings')->insert([
            ['key' => 'bank_name',      'value' => 'MB Bank',           'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bank_account',   'value' => '0783704196',        'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bank_owner',     'value' => 'TRAN THANH TUAN',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bank_bin',       'value' => '970422',            'created_at' => now(), 'updated_at' => now()], // MB Bank BIN
            ['key' => 'pt_enabled',     'value' => '1',                 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bank_enabled',   'value' => '1',                 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('nav_settings');
    }
};
