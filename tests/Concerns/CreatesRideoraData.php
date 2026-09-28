<?php

namespace Tests\Concerns;

use App\Models\Booking;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Http\UploadedFile;

trait CreatesRideoraData
{
    /**
     * A minimal 1x1 PNG so upload validation passes without requiring GD.
     */
    protected function fakePng(string $name = 'proof.png', int $kilobytes = 10): UploadedFile
    {
        $binary = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8DwHwAFAAH/q842iQAAAABJRU5ErkJggg=='
        );

        // Pad the file so size based assertions stay realistic.
        $binary .= str_repeat("\0", max(0, $kilobytes * 1024 - strlen($binary)));

        $path = tempnam(sys_get_temp_dir(), 'rideora').'.png';
        file_put_contents($path, $binary);

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    protected function makeCategory(array $attributes = []): VehicleCategory
    {
        $attributes = array_merge([
            'name' => 'Sedan',
            'slug' => 'sedan',
            'description' => 'Comfortable four door cars.',
            'status' => 'active',
        ], $attributes);

        return VehicleCategory::firstOrCreate(['slug' => $attributes['slug']], $attributes);
    }

    protected function makeVehicle(array $attributes = []): Vehicle
    {
        $category = $this->makeCategory();

        return Vehicle::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Toyota Axio',
            'slug' => 'toyota-axio',
            'brand' => 'Toyota',
            'model' => 'Axio Hybrid',
            'registration_number' => 'DHA-GA-11-2345',
            'vehicle_type' => 'Car',
            'fuel_type' => 'Petrol',
            'transmission' => 'Automatic',
            'seats' => 5,
            'price_per_hour' => 450,
            'price_per_day' => 3500,
            'security_deposit' => 10000,
            'location' => 'Dhaka',
            'description' => 'Well maintained sedan.',
            'status' => 'available',
        ], $attributes));
    }

    protected function makePaymentMethod(array $attributes = []): PaymentMethod
    {
        return PaymentMethod::create(array_merge([
            'name' => 'bKash',
            'account_name' => 'Rideora Rentals',
            'account_number' => '01711-000001',
            'instructions' => 'Send money and upload the screenshot.',
            'status' => 'active',
        ], $attributes));
    }

    protected function makeCustomer(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Rakib Hasan',
            'email' => 'customer'.random_int(1000, 9999).'@example.com',
            'phone' => '+880 1711-100001',
        ], $attributes));
    }

    protected function makeAdmin(array $attributes = []): User
    {
        return User::factory()->admin()->create(array_merge([
            'name' => 'Rideora Admin',
            'email' => 'admin'.random_int(1000, 9999).'@example.com',
        ], $attributes));
    }

    protected function makeBooking(User $user, Vehicle $vehicle, array $attributes = []): Booking
    {
        return Booking::create(array_merge([
            'booking_code' => 'VR-20260928-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'pickup_location' => 'Dhaka',
            'dropoff_location' => 'Dhaka',
            'pickup_date' => now()->addDays(3)->toDateString(),
            'pickup_time' => '10:00:00',
            'return_date' => now()->addDays(5)->toDateString(),
            'return_time' => '18:00:00',
            'rental_days' => 2,
            'rental_hours' => 8,
            'base_amount' => 7000,
            'security_deposit' => $vehicle->security_deposit,
            'discount' => 0,
            'total_amount' => 17000,
            'booking_status' => Booking::STATUS_PENDING,
            'payment_status' => Booking::PAYMENT_UNPAID,
        ], $attributes));
    }

    /**
     * Future rental window as request payload.
     *
     * @return array<string, string>
     */
    protected function bookingPayload(Vehicle $vehicle, int $startIn = 3, int $endIn = 5): array
    {
        return [
            'vehicle_id' => (string) $vehicle->id,
            'pickup_location' => 'Dhaka',
            'dropoff_location' => 'Chattogram',
            'pickup_date' => now()->addDays($startIn)->toDateString(),
            'pickup_time' => '10:00',
            'return_date' => now()->addDays($endIn)->toDateString(),
            'return_time' => '18:00',
            'customer_note' => 'Please deliver to the airport.',
        ];
    }
}
