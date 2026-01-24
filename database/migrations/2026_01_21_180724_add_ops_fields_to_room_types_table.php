<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    // Decide safe "after" columns based on what exists in YOUR table
    $afterPrice = Schema::hasColumn('room_types', 'price_tzs')
      ? 'price_tzs'
      : (Schema::hasColumn('room_types', 'price_per_night') ? 'price_per_night' : 'slug');

    $afterQuota = Schema::hasColumn('room_types', 'capacity')
      ? 'capacity'
      : (Schema::hasColumn('room_types', 'price_usd') ? 'price_usd' : $afterPrice);

    Schema::table('room_types', function (Blueprint $table) use ($afterPrice, $afterQuota) {

      // Add ONLY if missing (prevents duplicate-column errors)
      if (!Schema::hasColumn('room_types', 'code')) {
        $table->string('code', 10)->nullable()->unique()->after('id'); // DDR / DSR
        $table->index(['code']);
      }

      if (!Schema::hasColumn('room_types', 'price_usd')) {
        $table->decimal('price_usd', 10, 2)->nullable()->after($afterPrice);
      }

      if (!Schema::hasColumn('room_types', 'online_quota')) {
        $table->unsignedInteger('online_quota')->default(0)->after($afterQuota);
        $table->index(['is_active', 'online_quota']);
      }
    });
  }

  public function down(): void
  {
    Schema::table('room_types', function (Blueprint $table) {
      // Drop in reverse, only if exists
      if (Schema::hasColumn('room_types', 'online_quota')) {
        $table->dropIndex(['is_active', 'online_quota']);
        $table->dropColumn('online_quota');
      }

      if (Schema::hasColumn('room_types', 'price_usd')) {
        $table->dropColumn('price_usd');
      }

      if (Schema::hasColumn('room_types', 'code')) {
        $table->dropIndex(['code']);
        $table->dropColumn('code');
      }
    });
  }
};
