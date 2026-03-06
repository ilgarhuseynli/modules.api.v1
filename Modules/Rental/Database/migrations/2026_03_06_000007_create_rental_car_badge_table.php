<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_car_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('rental_cars')->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained('rental_badges')->cascadeOnDelete();

            $table->unique(['car_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_car_badge');
    }
};
