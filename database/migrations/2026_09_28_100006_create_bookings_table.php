<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 40)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->restrictOnDelete();
            $table->string('pickup_location', 160);
            $table->string('dropoff_location', 160)->nullable();
            $table->date('pickup_date');
            $table->time('pickup_time')->nullable();
            $table->date('return_date');
            $table->time('return_time')->nullable();
            $table->unsignedInteger('rental_days')->default(1);
            $table->unsignedInteger('rental_hours')->default(0);
            $table->decimal('base_amount', 12, 2)->default(0);
            $table->decimal('security_deposit', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('booking_status', 30)->default('pending');
            $table->string('payment_status', 30)->default('unpaid');
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index('booking_status');
            $table->index('payment_status');
            $table->index(['vehicle_id', 'pickup_date', 'return_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
