<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained('game_sessions')->onDelete('cascade');
            $table->enum('bet_type', ['long', 'short']);
            $table->decimal('bet_amount', 15, 2);
            $table->enum('status', ['pending', 'won', 'lost'])->default('pending');
            $table->decimal('profit', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
