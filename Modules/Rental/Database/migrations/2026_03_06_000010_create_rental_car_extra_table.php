<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_car_extra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('rental_cars')->cascadeOnDelete();
            $table->foreignId('extra_id')->constrained('rental_extras')->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->tinyInteger('price_type')->nullable();

            $table->unique(['car_id', 'extra_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_car_extra');
    }
};
