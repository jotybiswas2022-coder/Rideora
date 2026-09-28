<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRideoraData;
use Tests\TestCase;

class VehicleBrowsingTest extends TestCase
{
    use CreatesRideoraData;
    use RefreshDatabase;

    public function test_homepage_loads_with_fleet(): void
    {
        $vehicle = $this->makeVehicle();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Your Ride')
            ->assertSee($vehicle->name)
            ->assertSee('Vehicle categories');
    }

    public function test_vehicle_listing_applies_filters(): void
    {
        $sedan = $this->makeVehicle([
            'name' => 'Toyota Axio', 'slug' => 'axio', 'registration_number' => 'DHA-1',
            'brand' => 'Toyota', 'fuel_type' => 'Petrol', 'price_per_day' => 3500, 'seats' => 5,
        ]);

        $suv = $this->makeVehicle([
            'name' => 'Nissan X-Trail', 'slug' => 'xtrail', 'registration_number' => 'DHA-2',
            'brand' => 'Nissan', 'fuel_type' => 'Diesel', 'price_per_day' => 6000, 'seats' => 5,
        ]);

        $this->get(route('vehicles.index', ['brand' => 'Toyota']))
            ->assertOk()
            ->assertSee($sedan->name)
            ->assertDontSee($suv->name);

        $this->get(route('vehicles.index', ['q' => 'X-Trail']))
            ->assertOk()
            ->assertSee($suv->name)
            ->assertDontSee($sedan->name);

        $this->get(route('vehicles.index', ['max_price' => 4000]))
            ->assertOk()
            ->assertSee($sedan->name)
            ->assertDontSee($suv->name);
    }

    public function test_unavailable_statuses_are_hidden_from_the_listing(): void
    {
        $available = $this->makeVehicle(['name' => 'Honda Vezel', 'slug' => 'vezel', 'registration_number' => 'DHA-3']);
        $maintenance = $this->makeVehicle([
            'name' => 'Suzuki Swift', 'slug' => 'swift', 'registration_number' => 'DHA-4', 'status' => 'maintenance',
        ]);

        $this->get(route('vehicles.index'))
            ->assertOk()
            ->assertSee($available->name)
            ->assertDontSee($maintenance->name);
    }

    public function test_vehicle_details_page_shows_specifications_and_reviews_section(): void
    {
        $vehicle = $this->makeVehicle();

        $this->get(route('vehicles.show', $vehicle))
            ->assertOk()
            ->assertSee($vehicle->name)
            ->assertSee($vehicle->registration_number)
            ->assertSee('Customer reviews');
    }

    public function test_inactive_vehicle_returns_not_found_for_guests(): void
    {
        $vehicle = $this->makeVehicle(['status' => 'inactive']);

        $this->get(route('vehicles.show', $vehicle))->assertNotFound();
        $this->actingAs($this->makeAdmin())->get(route('vehicles.show', $vehicle))->assertOk();
    }

    public function test_availability_endpoint_returns_a_server_side_quote(): void
    {
        $vehicle = $this->makeVehicle(['price_per_day' => 3500, 'price_per_hour' => 450, 'security_deposit' => 10000]);

        $response = $this->getJson(route('vehicles.availability', $vehicle).'?'.http_build_query([
            'pickup_date' => now()->addDays(3)->toDateString(),
            'pickup_time' => '10:00',
            'return_date' => now()->addDays(6)->toDateString(),
            'return_time' => '10:00',
        ]));

        $response->assertOk()
            ->assertJson(['available' => true])
            ->assertJsonPath('quote.rental_days', 3)
            ->assertJsonPath('quote.base_amount', 10500)
            ->assertJsonPath('quote.total_amount', 20500);
    }

    public function test_availability_endpoint_flags_overlapping_bookings(): void
    {
        $vehicle = $this->makeVehicle();
        $customer = $this->makeCustomer();

        $this->makeBooking($customer, $vehicle, [
            'pickup_date' => now()->addDays(4)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
            'booking_status' => Booking::STATUS_CONFIRMED,
        ]);

        $response = $this->getJson(route('vehicles.availability', $vehicle).'?'.http_build_query([
            'pickup_date' => now()->addDays(5)->toDateString(),
            'pickup_time' => '10:00',
            'return_date' => now()->addDays(6)->toDateString(),
            'return_time' => '10:00',
        ]));

        $response->assertOk()->assertJson(['available' => false]);
    }

    public function test_availability_endpoint_rejects_invalid_dates(): void
    {
        $vehicle = $this->makeVehicle();

        $this->getJson(route('vehicles.availability', $vehicle).'?'.http_build_query([
            'pickup_date' => now()->addDays(6)->toDateString(),
            'pickup_time' => '10:00',
            'return_date' => now()->addDays(3)->toDateString(),
            'return_time' => '10:00',
        ]))->assertStatus(422);
    }
}
