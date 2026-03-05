<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();

            $table->string('keyword')->nullable();
            $table->boolean('is_company')->default(false);
            $table->boolean('send_notification')->default(true);
            $table->string('avatar_id')->nullable();

            $table->tinyInteger('gender')->nullable();
            $table->dateTime('birth_date')->nullable();

            $table->json('address')->nullable();
            $table->json('phones')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
