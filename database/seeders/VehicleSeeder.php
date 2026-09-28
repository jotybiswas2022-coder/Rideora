<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'name' => 'Toyota Axio', 'category' => 'Sedan', 'brand' => 'Toyota', 'model' => 'Axio Hybrid',
                'registration_number' => 'DHA-GA-11-2345', 'vehicle_type' => 'Car', 'fuel_type' => 'Hybrid',
                'transmission' => 'Automatic', 'seats' => 5, 'price_per_hour' => 450, 'price_per_day' => 3500,
                'security_deposit' => 10000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'A dependable sedan with excellent fuel economy, perfect for city commutes and airport runs. Air conditioning, reverse camera and full insurance included.',
            ],
            [
                'name' => 'Toyota Premio', 'category' => 'Sedan', 'brand' => 'Toyota', 'model' => 'F EX 1.5',
                'registration_number' => 'DHA-GA-12-7781', 'vehicle_type' => 'Car', 'fuel_type' => 'Petrol',
                'transmission' => 'Automatic', 'seats' => 5, 'price_per_hour' => 550, 'price_per_day' => 4200,
                'security_deposit' => 12000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'Executive class comfort with generous legroom and a smooth automatic gearbox. A favourite for corporate travel.',
            ],
            [
                'name' => 'Honda Vezel', 'category' => 'SUV', 'brand' => 'Honda', 'model' => 'Hybrid Z',
                'registration_number' => 'DHA-GA-13-4520', 'vehicle_type' => 'SUV', 'fuel_type' => 'Hybrid',
                'transmission' => 'CVT', 'seats' => 5, 'price_per_hour' => 700, 'price_per_day' => 5500,
                'security_deposit' => 15000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'Compact SUV with hybrid efficiency, high ground clearance and a premium interior.',
            ],
            [
                'name' => 'Nissan X-Trail', 'category' => 'SUV', 'brand' => 'Nissan', 'model' => 'X-Trail 2.0',
                'registration_number' => 'CTG-GA-15-9013', 'vehicle_type' => 'SUV', 'fuel_type' => 'Petrol',
                'transmission' => 'Automatic', 'seats' => 5, 'price_per_hour' => 750, 'price_per_day' => 6000,
                'security_deposit' => 15000, 'location' => 'Chattogram', 'status' => 'available',
                'description' => 'A rugged 4x4 ready for hill routes and beach trips. Panoramic roof and generous boot space.',
            ],
            [
                'name' => 'Toyota Noah', 'category' => 'Microbus', 'brand' => 'Toyota', 'model' => 'Noah Si',
                'registration_number' => 'DHA-GA-16-3390', 'vehicle_type' => 'Microbus', 'fuel_type' => 'Petrol',
                'transmission' => 'Automatic', 'seats' => 8, 'price_per_hour' => 800, 'price_per_day' => 6500,
                'security_deposit' => 15000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'Eight seater family van with sliding doors and dual air conditioning. Ideal for group tours.',
            ],
            [
                'name' => 'Toyota Hiace', 'category' => 'Microbus', 'brand' => 'Toyota', 'model' => 'Hiace GL',
                'registration_number' => 'DHA-GA-17-1120', 'vehicle_type' => 'Microbus', 'fuel_type' => 'Diesel',
                'transmission' => 'Manual', 'seats' => 12, 'price_per_hour' => 1000, 'price_per_day' => 8500,
                'security_deposit' => 20000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'Twelve seater tour bus for long distance trips. Maintained for highway reliability.',
            ],
            [
                'name' => 'Honda Civic', 'category' => 'Sedan', 'brand' => 'Honda', 'model' => 'Civic RS Turbo',
                'registration_number' => 'DHA-GA-18-6654', 'vehicle_type' => 'Car', 'fuel_type' => 'Petrol',
                'transmission' => 'CVT', 'seats' => 5, 'price_per_hour' => 900, 'price_per_day' => 7000,
                'security_deposit' => 20000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'Sporty turbocharged sedan with leather seats and premium audio for business travel.',
            ],
            [
                'name' => 'Toyota Aqua', 'category' => 'Hatchback', 'brand' => 'Toyota', 'model' => 'Aqua S',
                'registration_number' => 'SYL-GA-11-9087', 'vehicle_type' => 'Hatchback', 'fuel_type' => 'Hybrid',
                'transmission' => 'CVT', 'seats' => 5, 'price_per_hour' => 400, 'price_per_day' => 3000,
                'security_deposit' => 8000, 'location' => 'Sylhet', 'status' => 'available',
                'description' => 'The most economical choice in our fleet — great for tea garden tours and daily errands.',
            ],
            [
                'name' => 'Suzuki Swift', 'category' => 'Hatchback', 'brand' => 'Suzuki', 'model' => 'Swift GL',
                'registration_number' => 'DHA-GA-19-2211', 'vehicle_type' => 'Hatchback', 'fuel_type' => 'Petrol',
                'transmission' => 'Manual', 'seats' => 5, 'price_per_hour' => 380, 'price_per_day' => 2800,
                'security_deposit' => 8000, 'location' => 'Dhaka', 'status' => 'maintenance',
                'description' => 'Nimble hatchback currently in scheduled maintenance. Available again soon.',
            ],
            [
                'name' => 'Yamaha R15 V4', 'category' => 'Bike', 'brand' => 'Yamaha', 'model' => 'R15 V4',
                'registration_number' => 'DHA-LA-11-3399', 'vehicle_type' => 'Bike', 'fuel_type' => 'Petrol',
                'transmission' => 'Manual', 'seats' => 2, 'price_per_hour' => 200, 'price_per_day' => 1200,
                'security_deposit' => 5000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'A nimble sport bike with two helmets included. Perfect for quick city rides.',
            ],
            [
                'name' => 'Honda CBR 150R', 'category' => 'Bike', 'brand' => 'Honda', 'model' => 'CBR 150R',
                'registration_number' => 'DHA-LA-12-4477', 'vehicle_type' => 'Bike', 'fuel_type' => 'Petrol',
                'transmission' => 'Manual', 'seats' => 2, 'price_per_hour' => 250, 'price_per_day' => 1800,
                'security_deposit' => 8000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'Reliable sports bike for weekend highway rides, delivered with two helmets and a tank bag.',
            ],
            [
                'name' => 'Toyota Land Cruiser Prado', 'category' => 'Luxury', 'brand' => 'Toyota', 'model' => 'Prado TX',
                'registration_number' => 'DHA-GA-10-0501', 'vehicle_type' => 'Luxury', 'fuel_type' => 'Diesel',
                'transmission' => 'Automatic', 'seats' => 7, 'price_per_hour' => 2200, 'price_per_day' => 18000,
                'security_deposit' => 50000, 'location' => 'Dhaka', 'status' => 'available',
                'description' => 'Flagship SUV for VIP movement, weddings and corporate events. Chauffeur can be arranged.',
            ],
            [
                'name' => 'Nissan Navara', 'category' => 'Pickup', 'brand' => 'Nissan', 'model' => 'Navara EL',
                'registration_number' => 'CTG-GA-14-7788', 'vehicle_type' => 'Pickup', 'fuel_type' => 'Diesel',
                'transmission' => 'Manual', 'seats' => 5, 'price_per_hour' => 950, 'price_per_day' => 7500,
                'security_deposit' => 20000, 'location' => 'Chattogram', 'status' => 'available',
                'description' => 'Double-cab pickup with a 1 tonne bed, ideal for fieldwork and equipment transport.',
            ],
        ];

        foreach ($vehicles as $data) {
            $category = VehicleCategory::where('name', $data['category'])->first();

            if (! $category) {
                continue;
            }

            $slug = Str::slug($data['name'].' '.$data['registration_number']);

            $vehicle = Vehicle::updateOrCreate(
                ['registration_number' => $data['registration_number']],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'slug' => $slug,
                    'brand' => $data['brand'],
                    'model' => $data['model'],
                    'vehicle_type' => $data['vehicle_type'],
                    'fuel_type' => $data['fuel_type'],
                    'transmission' => $data['transmission'],
                    'seats' => $data['seats'],
                    'price_per_hour' => $data['price_per_hour'],
                    'price_per_day' => $data['price_per_day'],
                    'security_deposit' => $data['security_deposit'],
                    'location' => $data['location'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                ]
            );

            $this->attachPlaceholderImage($vehicle);
        }
    }

    /**
     * Generate a lightweight local SVG so every demo vehicle has an image
     * without depending on external image hosts.
     */
    private function attachPlaceholderImage(Vehicle $vehicle): void
    {
        if ($vehicle->images()->exists()) {
            return;
        }

        $path = 'vehicles/'.$vehicle->slug.'.svg';

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $this->placeholderSvg($vehicle));
        }

        $vehicle->images()->create([
            'image' => $path,
            'is_primary' => true,
        ]);
    }

    private function placeholderSvg(Vehicle $vehicle): string
    {
        $name = e($vehicle->name);
        $brand = e($vehicle->brand.' · '.$vehicle->vehicle_type);
        $category = e($vehicle->category?->name ?? 'Rideora');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500" viewBox="0 0 800 500" role="img" aria-label="{$name}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#0F172A"/>
      <stop offset="60%" stop-color="#1E3A8A"/>
      <stop offset="100%" stop-color="#2563EB"/>
    </linearGradient>
  </defs>
  <rect width="800" height="500" fill="url(#bg)"/>
  <circle cx="690" cy="80" r="130" fill="#3B82F6" opacity="0.18"/>
  <circle cx="90" cy="440" r="110" fill="#60A5FA" opacity="0.14"/>
  <g transform="translate(120,150)" fill="#E2E8F0" opacity="0.92">
    <path d="M40 130h480c26 0 40-10 40-32 0-26-18-40-52-44l-52-6-46-42c-10-9-22-14-36-14H186c-14 0-26 5-36 14l-46 42-52 6c-34 4-52 18-52 44 0 22 14 32 40 32z"/>
    <circle cx="150" cy="140" r="42" fill="#0F172A"/>
    <circle cx="150" cy="140" r="20" fill="#93C5FD"/>
    <circle cx="440" cy="140" r="42" fill="#0F172A"/>
    <circle cx="440" cy="140" r="20" fill="#93C5FD"/>
  </g>
  <text x="60" y="392" font-family="Segoe UI, Helvetica, Arial, sans-serif" font-size="42" font-weight="700" fill="#FFFFFF">{$name}</text>
  <text x="62" y="428" font-family="Segoe UI, Helvetica, Arial, sans-serif" font-size="20" fill="#BFDBFE">{$brand}</text>
  <text x="62" y="464" font-family="Segoe UI, Helvetica, Arial, sans-serif" font-size="17" fill="#93C5FD" letter-spacing="3">{$category}</text>
</svg>
SVG;
    }
}
