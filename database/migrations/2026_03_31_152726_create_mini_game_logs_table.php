<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mini_game_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('game_type', ['spin', 'dice']);
            $table->decimal('bet_amount', 15, 2);
            $table->decimal('payout', 15, 2)->default(0);
            $table->decimal('profit', 15, 2);   // âm = thua, dương = thắng
            $table->boolean('won')->default(false);
            $table->json('details')->nullable();  // dice values, prize index...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mini_game_logs');
    }
};
