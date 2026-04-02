<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nav_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('nav_services');
            $table->string('order_code')->unique();
            $table->enum('status', [
                'pending_payment', // chờ thanh toán
                'paid',            // đã thanh toán, chờ xử lý
                'processing',      // admin đang xử lý
                'completed',       // hoàn thành
                'cancelled',       // huỷ
            ])->default('pending_payment');
            $table->enum('payment_method', ['points', 'bank'])->default('bank');

            // Thông tin TikTok
            $table->string('tiktok_username');
            $table->string('registered_email')->nullable();
            $table->string('registered_phone')->nullable();
            $table->string('violation_type')->nullable(); // loại vi phạm
            $table->date('violation_date')->nullable();   // ngày bị khóa
            $table->integer('follower_count')->nullable();
            $table->text('account_notes')->nullable();    // ghi chú thêm

            // Upload files
            $table->string('id_card_front')->nullable();  // CCCD mặt trước
            $table->string('id_card_back')->nullable();   // CCCD mặt sau
            $table->string('screenshot_path')->nullable(); // ảnh màn hình bị khóa

            // Thông tin khách hàng
            $table->string('customer_name')->nullable();
            $table->string('customer_contact')->nullable();

            // Thanh toán
            $table->decimal('amount', 12, 0)->default(0);
            $table->string('transfer_content')->nullable(); // Nội dung chuyển khoản
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->timestamp('payment_verified_at')->nullable(); // admin xác nhận

            // Thời hạn kháng cáo
            $table->date('appeal_deadline')->nullable();

            // Admin
            $table->text('admin_notes')->nullable();
            $table->timestamp('appeal_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nav_orders');
    }
};
