<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('guest_name', 120)->nullable()->after('guest_id');
            $table->string('guest_phone', 25)->nullable()->after('guest_name');
            $table->string('guest_email', 120)->nullable()->after('guest_phone');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_phone', 'guest_email']);
        });
    }
};
