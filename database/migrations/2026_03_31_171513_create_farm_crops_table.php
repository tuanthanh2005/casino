<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('farm_crops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seed_type_id')->constrained('seed_types')->cascadeOnDelete();
            $table->tinyInteger('slot_number');       // 1–20
            $table->enum('status', ['growing','ripe','dead'])->default('growing');
            $table->timestamp('planted_at');
            $table->timestamp('ripe_at');             // tính khi trồng, giảm mỗi lần tưới
            $table->timestamp('last_watered_at');
            $table->tinyInteger('watering_count')->default(0); // 0–5
            $table->tinyInteger('harvest_qty')->nullable();    // set khi thu hoạch
            $table->timestamp('died_at')->nullable();
            $table->timestamp('delete_at')->nullable();        // died_at + 24h
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('farm_crops'); }
};
