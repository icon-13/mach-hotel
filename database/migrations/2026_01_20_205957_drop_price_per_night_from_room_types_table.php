<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            if (Schema::hasColumn('room_types', 'price_per_night')) {
                $table->dropColumn('price_per_night');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->unsignedInteger('price_per_night')->after('slug');
        });
    }
};
