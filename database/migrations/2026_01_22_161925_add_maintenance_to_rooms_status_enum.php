<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE rooms
            MODIFY status ENUM('Available','Booked','Occupied','Maintenance')
            NOT NULL DEFAULT 'Available'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE rooms
            MODIFY status ENUM('Available','Booked','Occupied')
            NOT NULL DEFAULT 'Available'
        ");
    }
};
