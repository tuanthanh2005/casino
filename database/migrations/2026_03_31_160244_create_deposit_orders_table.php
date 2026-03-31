<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code', 20)->unique();   // CTB + random, dùng làm nội dung CK
            $table->enum('method', ['bank_qr', 'card']);
            $table->decimal('amount', 15, 2);             // VNĐ
            $table->decimal('points_credited', 15, 2)->default(0); // xu được cấp
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            // Thẻ cào
            $table->string('card_type')->nullable();       // viettel, vinaphone, mobi...
            $table->string('card_serial')->nullable();
            $table->string('card_pin')->nullable();
            $table->string('card_amount')->nullable();     // mệnh giá trên thẻ
            // Admin
            $table->text('admin_note')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_orders');
    }
};
