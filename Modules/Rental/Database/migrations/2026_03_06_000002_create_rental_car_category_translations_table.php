<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_car_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('rental_car_categories')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug');

            $table->unique(['category_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_car_category_translations');
    }
};
