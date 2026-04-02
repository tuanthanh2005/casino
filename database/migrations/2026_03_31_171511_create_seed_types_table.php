<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('seed_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('emoji', 10)->default('🌱');
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price_buy', 12, 2);        // PT mua 1 hạt
            $table->decimal('price_sell_base', 12, 2);  // PT base/trái khi bán
            $table->integer('grow_time_mins')->default(60); // phút đến chín
            $table->integer('max_waterings')->default(5);    // tối đa 5 lần tưới
            $table->decimal('lucky_chance', 4, 2)->default(0.20); // 20% = 2 trái
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('seed_types'); }
};
