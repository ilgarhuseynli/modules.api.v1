<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('rental_car_categories')->nullOnDelete();
            $table->string('brand');
            $table->string('model');
            $table->smallInteger('year');
            $table->string('plate_number')->nullable();
            $table->string('color')->nullable();
            $table->tinyInteger('transmission');
            $table->tinyInteger('fuel_type');
            $table->tinyInteger('body_type');
            $table->tinyInteger('seats')->default(5);
            $table->tinyInteger('doors')->default(4);
            $table->string('engine')->nullable();
            $table->decimal('price_daily', 10, 2);
            $table->decimal('price_weekly', 10, 2)->nullable();
            $table->decimal('price_monthly', 10, 2)->nullable();
            $table->decimal('deposit', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->foreignId('avatar_id')->nullable()->constrained('files')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_cars');
    }
};
