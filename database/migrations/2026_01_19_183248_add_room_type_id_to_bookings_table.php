<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('bookings', function (Blueprint $table) {
      $table->foreignId('room_type_id')->nullable()->after('room_id')->constrained('room_types')->nullOnDelete();
      $table->index(['room_type_id','status']);
    });
  }

  public function down(): void {
    Schema::table('bookings', function (Blueprint $table) {
      $table->dropIndex(['room_type_id','status']);
      $table->dropConstrainedForeignId('room_type_id');
    });
  }
};
