<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->boolean('is_bookable')->default(true)->after('is_active');
            $table->index('is_bookable');
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropIndex(['is_bookable']);
            $table->dropColumn('is_bookable');
        });
    }
};
