<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('rooms', function (Blueprint $table) {
      $table->id();
      $table->string('name');              // e.g. Deluxe Queen
      $table->string('slug')->unique();    // e.g. deluxe-queen
      $table->string('type')->nullable();  // e.g. Deluxe, Standard
      $table->unsignedInteger('capacity')->default(2);
      $table->unsignedInteger('price_per_night'); // store as integer TZS
      $table->enum('status', ['Available','Booked','Occupied'])->default('Available');
      $table->text('short_description')->nullable();
      $table->json('amenities')->nullable(); // ["Wi-Fi","A/C"]
      $table->boolean('is_active')->default(true);

      $table->timestamps();

      $table->index(['is_active', 'status']);
      $table->index(['capacity']);
      $table->index(['price_per_night']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('rooms');
  }
};
