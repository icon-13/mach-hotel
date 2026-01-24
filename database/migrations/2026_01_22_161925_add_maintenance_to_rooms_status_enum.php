<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ SQLite doesn't support MODIFY / ENUM
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('rooms', function (Blueprint $table) {
                // Convert to string so "Maintenance" can be stored
                $table->string('status')->default('Available')->change();
            });

            return;
        }

        // ✅ MySQL (or others): use native ENUM change
        DB::statement("
            ALTER TABLE rooms
            MODIFY status ENUM('Available','Booked','Occupied','Maintenance')
            NOT NULL DEFAULT 'Available'
        ");
    }

    public function down(): void
    {
        // ✅ SQLite rollback: keep it as string (safe)
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('rooms', function (Blueprint $table) {
                $table->string('status')->default('Available')->change();
            });

            return;
        }

        // ✅ MySQL rollback: remove Maintenance
        DB::statement("
            ALTER TABLE rooms
            MODIFY status ENUM('Available','Booked','Occupied')
            NOT NULL DEFAULT 'Available'
        ");
    }
};
