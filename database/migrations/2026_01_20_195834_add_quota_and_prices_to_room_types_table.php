<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->unsignedInteger('price_tzs')->after('slug');
            $table->unsignedInteger('price_usd')->after('price_tzs');
            $table->unsignedInteger('online_quota')->default(0)->after('capacity'); // DDR=4, DSR=8
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn(['price_tzs','price_usd','online_quota']);
        });
    }
};
