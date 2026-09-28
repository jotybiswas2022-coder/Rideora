<?php

namespace Database\Seeders;

use App\Models\VehicleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Sedan', 'icon' => '&#128663;', 'description' => 'Comfortable four-door cars for city and highway driving.'],
            ['name' => 'SUV', 'icon' => '&#128666;', 'description' => 'Spacious vehicles built for rough roads and family trips.'],
            ['name' => 'Hatchback', 'icon' => '&#128664;', 'description' => 'Compact, fuel efficient cars ideal for daily commutes.'],
            ['name' => 'Microbus', 'icon' => '&#128652;', 'description' => 'Group transport for tours, events and airport transfers.'],
            ['name' => 'Bike', 'icon' => '&#127949;', 'description' => 'Motorcycles and sport bikes for quick solo rides.'],
            ['name' => 'Pickup', 'icon' => '&#128667;', 'description' => 'Utility vehicles for cargo and countryside trips.'],
            ['name' => 'Luxury', 'icon' => '&#128142;', 'description' => 'Premium vehicles for weddings and corporate travel.'],
            ['name' => 'Other', 'icon' => '&#128665;', 'description' => 'Speciality vehicles that do not fit another category.'],
        ];

        foreach ($categories as $category) {
            VehicleCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'description' => $category['description'],
                    'status' => 'active',
                ]
            );
        }
    }
}
