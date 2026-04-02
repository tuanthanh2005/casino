<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('farm_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seed_type_id')->constrained('seed_types')->cascadeOnDelete();
            $table->enum('type', ['buy_seed','harvest','sell_fruit']);
            $table->integer('quantity');
            $table->decimal('unit_price_pt', 12, 2)->nullable();
            $table->decimal('total_pt', 12, 2)->nullable();
            $table->decimal('price_modifier', 6, 4)->nullable(); // hệ số random
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('farm_transactions'); }
};
