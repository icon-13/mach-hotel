<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('room_types', function (Blueprint $table) {
      $table->id();
      $table->string('name');               // Deluxe Room
      $table->string('slug')->unique();     // deluxe-room
      $table->unsignedInteger('price_per_night'); // TZS integer
      $table->unsignedInteger('capacity')->default(2);
      $table->json('amenities')->nullable();
      $table->text('description')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index(['is_active', 'price_per_night']);
      $table->index(['capacity']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('room_types');
  }
};
