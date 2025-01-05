<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('active_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token_id');
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable();
            $table->string('ip_address');
            $table->string('location')->nullable();
            $table->timestamp('last_active_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('active_devices');
    }
}; 