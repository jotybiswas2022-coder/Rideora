<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('vehicle_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name', 160);
            $table->string('slug', 190)->unique();
            $table->string('brand', 120);
            $table->string('model', 120)->nullable();
            $table->string('registration_number', 60)->unique();
            $table->string('vehicle_type', 60)->default('Car');
            $table->string('fuel_type', 40)->default('Petrol');
            $table->string('transmission', 40)->default('Manual');
            $table->unsignedTinyInteger('seats')->default(4);
            $table->decimal('price_per_hour', 10, 2)->default(0);
            $table->decimal('price_per_day', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->string('location', 160)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('available');
            $table->timestamps();

            $table->index('status');
            $table->index('brand');
            $table->index('fuel_type');
            $table->index('transmission');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
