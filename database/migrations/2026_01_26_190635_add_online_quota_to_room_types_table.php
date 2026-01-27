<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            if (!Schema::hasColumn('room_types', 'online_quota')) {
                $table->unsignedInteger('online_quota')->default(0)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            if (Schema::hasColumn('room_types', 'online_quota')) {
                $table->dropColumn('online_quota');
            }
        });
    }
};
