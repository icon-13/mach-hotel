<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change status column to VARCHAR so we can store lowercase statuses safely.
        Schema::table('bookings', function (Blueprint $table) {
            // Laravel needs doctrine/dbal for ->change() on some setups.
            // If it fails, we fall back to raw SQL below.
        });

        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('status', 30)->default('confirmed')->change();
            });
        } catch (\Throwable $e) {
            // Fallback if DBAL not installed
            DB::statement("ALTER TABLE bookings MODIFY status VARCHAR(30) NOT NULL DEFAULT 'confirmed'");
        }

        // Normalize existing values to lowercase (safe)
        DB::table('bookings')->update([
            'status' => DB::raw('LOWER(status)')
        ]);
    }

    public function down(): void
    {
        // Rollback: turn it back to ENUM (only if you really need it)
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'confirmed'");
    }
};
