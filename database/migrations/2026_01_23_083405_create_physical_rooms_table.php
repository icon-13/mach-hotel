<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('physical_rooms', function (Blueprint $table) {
            $table->id();

            // e.g. "101", "102", "A1" — keep flexible
            $table->string('room_number')->unique();

            // Nullable => TBD rooms stay unassigned until admin sets them
            $table->foreignId('room_type_id')
                ->nullable()
                ->constrained('room_types')
                ->nullOnDelete();

            // This is the operational status used by reception
            $table->enum('status', ['Available', 'Booked', 'Occupied', 'OutOfService'])
                ->default('Available');

            // Admin can mark whether this room is allowed to be booked online (future switch)
            $table->boolean('is_bookable_online')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['room_type_id', 'status']);
            $table->index(['is_bookable_online']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_rooms');
    }
};
