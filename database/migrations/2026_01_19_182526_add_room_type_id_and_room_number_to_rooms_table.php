<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('rooms', function (Blueprint $table) {
      $table->foreignId('room_type_id')->nullable()->after('id')->constrained('room_types')->nullOnDelete();
      $table->string('room_number')->nullable()->after('room_type_id'); // 200, 201, A1 etc
      $table->unsignedInteger('floor')->nullable()->after('room_number');
    });

    Schema::table('rooms', function (Blueprint $table) {
      $table->index(['room_type_id', 'status', 'is_active']);
      $table->unique(['room_type_id', 'room_number']);
    });
  }

  public function down(): void {
    Schema::table('rooms', function (Blueprint $table) {
      $table->dropUnique(['room_type_id', 'room_number']);
      $table->dropIndex(['room_type_id', 'status', 'is_active']);

      $table->dropConstrainedForeignId('room_type_id');
      $table->dropColumn(['room_number', 'floor']);
    });
  }
};
