<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_extras', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 2);
            $table->tinyInteger('price_type')->default(1);
            $table->boolean('is_global')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_extras');
    }
};
