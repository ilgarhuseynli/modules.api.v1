<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_car_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('rental_cars')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('description')->nullable();

            $table->unique(['car_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_car_translations');
    }
};
