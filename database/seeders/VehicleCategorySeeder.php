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
            ['name' => 'Sedan', 'icon' => 'car-front', 'description' => 'Comfortable four-door cars for city and highway driving.'],
            ['name' => 'SUV', 'icon' => 'truck-front', 'description' => 'Spacious vehicles built for rough roads and family trips.'],
            ['name' => 'Hatchback', 'icon' => 'car-front-fill', 'description' => 'Compact, fuel efficient cars ideal for daily commutes.'],
            ['name' => 'Microbus', 'icon' => 'bus-front', 'description' => 'Group transport for tours, events and airport transfers.'],
            ['name' => 'Bike', 'icon' => 'scooter', 'description' => 'Motorcycles and sport bikes for quick solo rides.'],
            ['name' => 'Pickup', 'icon' => 'truck', 'description' => 'Utility vehicles for cargo and countryside trips.'],
            ['name' => 'Luxury', 'icon' => 'gem', 'description' => 'Premium vehicles for weddings and corporate travel.'],
            ['name' => 'Other', 'icon' => 'grid', 'description' => 'Speciality vehicles that do not fit another category.'],
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
