<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_location_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('rental_locations')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('address')->nullable();

            $table->unique(['location_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_location_translations');
    }
};
