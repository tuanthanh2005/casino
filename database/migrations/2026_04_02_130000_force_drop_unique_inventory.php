<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('farm_inventory')) {
            return;
        }

        $databaseName = DB::getDatabaseName();
        $foreignKeyNames = collect(DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND CONSTRAINT_TYPE = ?'
            , [$databaseName, 'farm_inventory', 'FOREIGN KEY']
        ))->pluck('CONSTRAINT_NAME')->all();

        $indexNames = collect(DB::select(
            'SELECT DISTINCT INDEX_NAME
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?'
            , [$databaseName, 'farm_inventory']
        ))->pluck('INDEX_NAME')->all();

        Schema::table('farm_inventory', function (Blueprint $table) {
            // noop - migration actions are performed conditionally below
        });

        Schema::table('farm_inventory', function (Blueprint $table) use ($foreignKeyNames, $indexNames) {
            if (in_array('farm_inventory_user_id_foreign', $foreignKeyNames, true)) {
                $table->dropForeign('farm_inventory_user_id_foreign');
            }
            if (in_array('farm_inventory_seed_type_id_foreign', $foreignKeyNames, true)) {
                $table->dropForeign('farm_inventory_seed_type_id_foreign');
            }

            if (in_array('farm_inventory_user_id_seed_type_id_unique', $indexNames, true)) {
                $table->dropUnique('farm_inventory_user_id_seed_type_id_unique');
            }
        });

        $indexNamesAfterDrop = collect(DB::select(
            'SELECT DISTINCT INDEX_NAME
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?'
            , [$databaseName, 'farm_inventory']
        ))->pluck('INDEX_NAME')->all();

        $foreignKeyNamesAfterDrop = collect(DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND CONSTRAINT_TYPE = ?'
            , [$databaseName, 'farm_inventory', 'FOREIGN KEY']
        ))->pluck('CONSTRAINT_NAME')->all();

        Schema::table('farm_inventory', function (Blueprint $table) use ($indexNamesAfterDrop, $foreignKeyNamesAfterDrop) {
            if (!in_array('farm_inventory_user_id_index', $indexNamesAfterDrop, true)) {
                $table->index('user_id');
            }
            if (!in_array('farm_inventory_seed_type_id_index', $indexNamesAfterDrop, true)) {
                $table->index('seed_type_id');
            }

            if (!in_array('farm_inventory_user_id_foreign', $foreignKeyNamesAfterDrop, true)) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
            if (!in_array('farm_inventory_seed_type_id_foreign', $foreignKeyNamesAfterDrop, true)) {
                $table->foreign('seed_type_id')->references('id')->on('seed_types')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('farm_inventory')) {
            return;
        }

        $databaseName = DB::getDatabaseName();
        $indexNames = collect(DB::select(
            'SELECT DISTINCT INDEX_NAME
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?'
            , [$databaseName, 'farm_inventory']
        ))->pluck('INDEX_NAME')->all();

        Schema::table('farm_inventory', function (Blueprint $table) {
            // noop - rollback action is conditionally applied below
        });

        Schema::table('farm_inventory', function (Blueprint $table) use ($indexNames) {
            if (!in_array('farm_inventory_user_id_seed_type_id_unique', $indexNames, true)) {
                $table->unique(['user_id', 'seed_type_id']);
            }
        });
    }
};
