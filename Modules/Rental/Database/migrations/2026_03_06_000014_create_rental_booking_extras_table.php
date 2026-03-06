<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_booking_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('rental_bookings')->cascadeOnDelete();
            $table->foreignId('extra_id')->constrained('rental_extras');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->tinyInteger('price_type');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_booking_extras');
    }
};
