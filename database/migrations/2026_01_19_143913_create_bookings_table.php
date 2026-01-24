<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('bookings', function (Blueprint $table) {
      $table->id();

      $table->foreignId('room_id')->constrained()->cascadeOnDelete();
      $table->foreignId('guest_id')->constrained()->cascadeOnDelete();

      $table->date('check_in');
      $table->date('check_out');

      $table->unsignedInteger('total_amount')->default(0); // TZS integer
      $table->string('code')->unique(); // e.g. MACH-8F3K2P

      $table->enum('status', ['Confirmed','CheckedIn','CheckedOut','Cancelled'])->default('Confirmed');

      $table->text('special_requests')->nullable();

      $table->timestamps();

      $table->index(['room_id','status']);
      $table->index(['check_in','check_out']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('bookings');
  }
};
