<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_extra_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_id')->constrained('rental_extras')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('description')->nullable();

            $table->unique(['extra_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_extra_translations');
    }
};
