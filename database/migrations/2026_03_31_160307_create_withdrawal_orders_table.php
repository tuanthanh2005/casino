<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code', 20)->unique();
            $table->enum('method', ['bank_transfer', 'card']);
            $table->decimal('points_used', 15, 2);          // xu trừ (trước thuế)
            $table->decimal('tax_rate', 5, 4)->default(0.02); // 2%
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);           // tiền/xu thực nhận (sau thuế)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            // Bank
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();
            // Card
            $table->string('card_type')->nullable();        // muốn đổi loại thẻ gì
            // Admin
            $table->text('admin_note')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_orders');
    }
};
