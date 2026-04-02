<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE mini_game_logs MODIFY game_type ENUM('spin','dice','rps') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE mini_game_logs MODIFY game_type ENUM('spin','dice') NOT NULL");
    }
};
