<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ SQLite: drop the index that still references the column
        if (DB::getDriverName() === 'sqlite') {
            // index name from your error
            DB::statement('DROP INDEX IF EXISTS room_types_is_active_price_per_night_index');
        }

        // ✅ Now drop the column
        Schema::table('room_types', function (Blueprint $table) {
            if (Schema::hasColumn('room_types', 'price_per_night')) {
                $table->dropColumn('price_per_night');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            // restore column if rollback
            if (!Schema::hasColumn('room_types', 'price_per_night')) {
                $table->integer('price_per_night')->nullable();
            }
        });

        // Recreate index only if you really need it (optional)
        // (Most likely you don't need this index anymore.)
    }
};
