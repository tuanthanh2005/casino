<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('farm_inventory', function (Blueprint $table) {
            // Tạm thời xóa khóa ngoại để MySQL cho phép xóa index
            try {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['seed_type_id']);
            } catch (\Exception $e) {}

            // Xóa index unique gây lỗi Duplicate Entry
            try {
                $table->dropUnique('farm_inventory_user_id_seed_type_id_unique');
            } catch (\Exception $e) {}

            // Tạo lại các index thông thường để duy trì hiệu năng
            $table->index('user_id');
            $table->index('seed_type_id');

            // Tạo lại các khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('seed_type_id')->references('id')->on('seed_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farm_inventory', function (Blueprint $table) {
            try {
                $table->unique(['user_id', 'seed_type_id']);
            } catch (\Exception $e) {}
        });
    }
};
