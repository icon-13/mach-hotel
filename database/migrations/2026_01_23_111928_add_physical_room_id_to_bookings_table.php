<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // nullable because old bookings may not have a physical room assigned
            $table->foreignId('physical_room_id')
                ->nullable()
                ->after('room_id')
                ->constrained('physical_rooms')
                ->nullOnDelete();

            $table->index(['status', 'physical_room_id']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['physical_room_id']);
            $table->dropIndex(['status', 'physical_room_id']);
            $table->dropColumn('physical_room_id');
        });
    }
};
