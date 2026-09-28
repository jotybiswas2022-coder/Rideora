<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const LEGACY_ICONS = [
        '&#128663;' => 'car-front',
        '&#128664;' => 'car-front-fill',
        '&#128665;' => 'grid',
        '&#128666;' => 'truck-front',
        '&#128667;' => 'truck',
        '&#128652;' => 'bus-front',
        '&#127949;' => 'scooter',
        '&#128142;' => 'gem',
    ];

    public function up(): void
    {
        foreach (self::LEGACY_ICONS as $legacy => $name) {
            DB::table('vehicle_categories')
                ->where('icon', $legacy)
                ->update(['icon' => $name]);
        }
    }

    public function down(): void
    {
        foreach (self::LEGACY_ICONS as $legacy => $name) {
            DB::table('vehicle_categories')
                ->where('icon', $name)
                ->update(['icon' => $legacy]);
        }
    }
};
