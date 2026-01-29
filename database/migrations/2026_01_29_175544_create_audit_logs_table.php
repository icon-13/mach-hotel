<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Actor (staff)
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name', 120)->nullable(); // snapshot
            $table->string('actor_email', 190)->nullable(); // snapshot
            $table->string('actor_role', 50)->nullable();

            // Action
            $table->string('action', 120); // e.g. booking.checkin, staff.update
            $table->string('severity', 20)->default('info'); // info|warning|danger

            // Entity
            $table->string('entity_type', 120)->nullable();  // Booking, PhysicalRoom, User
            $table->unsignedBigInteger('entity_id')->nullable();

            // Request context
            $table->string('route', 190)->nullable(); // route name
            $table->string('url', 255)->nullable();
            $table->string('method', 10)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            // Meta context
            $table->json('meta')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['action']);
            $table->index(['severity']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['actor_id']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
