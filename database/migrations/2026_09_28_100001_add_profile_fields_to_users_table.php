<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('city', 120)->nullable()->after('address');
            $table->string('driving_license_no', 60)->nullable()->after('city');
            $table->string('avatar_path')->nullable()->after('driving_license_no');
            $table->string('status', 20)->default('active')->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'city',
                'driving_license_no',
                'avatar_path',
                'status',
            ]);
        });
    }
};
