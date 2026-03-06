<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_badge_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained('rental_badges')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');

            $table->unique(['badge_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_badge_translations');
    }
};
