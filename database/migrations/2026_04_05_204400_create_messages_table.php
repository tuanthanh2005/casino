<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Registered user
            $table->string('session_id')->nullable()->index(); // Guest fallback
            $table->string('guest_name')->nullable(); // For forgot password/contact
            $table->string('guest_email')->nullable();
            $table->text('message');
            $table->boolean('is_from_admin')->default(false);
            $table->boolean('is_read')->default(false);
            $table->string('type')->default('chat'); // 'chat' or 'forgot_password'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
