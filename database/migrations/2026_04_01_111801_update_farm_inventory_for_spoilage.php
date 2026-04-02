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
        // Drop unique index if it exists
        try {
            Schema::table('farm_inventory', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'seed_type_id']);
            });
        } catch (\Exception $e) {
            // Index might not exist or already dropped, ignore
        }

        // Add columns if they don't exist
        Schema::table('farm_inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('farm_inventory', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('farm_inventory', 'warned_at')) {
                $table->timestamp('warned_at')->nullable()->after('expires_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farm_inventory', function (Blueprint $table) {
            if (Schema::hasColumn('farm_inventory', 'expires_at')) {
                $table->dropColumn(['expires_at', 'warned_at']);
            }
            try {
                $table->unique(['user_id', 'seed_type_id']);
            } catch (\Exception $e) {}
        });
    }
};
