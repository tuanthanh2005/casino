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
            // Ép buộc xóa index unique bằng tên cụ thể từ lỗi MySQL cung cấp
            // Tên: farm_inventory_user_id_seed_type_id_unique
            try {
                $table->dropUnique('farm_inventory_user_id_seed_type_id_unique');
            } catch (\Exception $e) {
                // Nếu đã xóa rồi thì bỏ qua
            }
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
