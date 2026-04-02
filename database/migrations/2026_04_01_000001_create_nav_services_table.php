<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nav_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable(); // Yêu cầu cần cung cấp
            $table->decimal('price', 12, 0)->default(0); // VNĐ
            $table->integer('appeal_deadline_days')->default(30); // số ngày kháng cáo
            $table->string('icon')->default('bi-shield-check'); // Bootstrap icon
            $table->string('color')->default('#6366f1'); // màu hiển thị
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nav_services');
    }
};
