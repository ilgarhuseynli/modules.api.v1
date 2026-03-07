<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('rental_cars');
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->foreignId('pickup_location_id')->nullable()->constrained('rental_locations')->nullOnDelete();
            $table->foreignId('dropoff_location_id')->nullable()->constrained('rental_locations')->nullOnDelete();
            $table->dateTime('pickup_date');
            $table->dateTime('dropoff_date');
            $table->integer('days');
            $table->tinyInteger('price_tier');
            $table->decimal('price_per_day', 10, 2);
            $table->decimal('base_price', 10, 2);
            $table->decimal('extras_total', 10, 2)->default(0);
            $table->decimal('locations_total', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->decimal('deposit', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->string('coupon_code')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('payment_status')->default(1);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_bookings');
    }
};
