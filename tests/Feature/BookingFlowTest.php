<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRideoraData;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use CreatesRideoraData;
    use RefreshDatabase;

    public function test_customer_can_create_a_booking_with_server_calculated_totals(): void
    {
        $customer = $this->makeCustomer();
        $vehicle = $this->makeVehicle(['price_per_day' => 3500, 'price_per_hour' => 450, 'security_deposit' => 10000]);

        $response = $this->actingAs($customer)->post(route('bookings.store'), $this->bookingPayload($vehicle, 3, 6));

        $booking = Booking::firstOrFail();

        $response->assertRedirect(route('bookings.payment', $booking));
        $this->assertSame($customer->id, $booking->user_id);
        $this->assertSame(3, $booking->rental_days);
        $this->assertEquals(10500, (float) $booking->base_amount);
        $this->assertEquals(10000, (float) $booking->security_deposit);
        $this->assertEquals(20500, (float) $booking->total_amount);
        $this->assertSame(Booking::STATUS_PENDING, $booking->booking_status);
        $this->assertSame(Booking::PAYMENT_UNPAID, $booking->payment_status);
        $this->assertMatchesRegularExpression('/^VR-\d{8}-\d{4}$/', $booking->booking_code);
    }

    public function test_longer_rentals_receive_an_automatic_discount(): void
    {
        $customer = $this->makeCustomer();
        $vehicle = $this->makeVehicle(['price_per_day' => 3500, 'price_per_hour' => 450, 'security_deposit' => 10000]);

        $this->actingAs($customer)
            ->post(route('bookings.store'), $this->bookingPayload($vehicle, 1, 8))
            ->assertSessionHasNoErrors();

        $booking = Booking::firstOrFail();

        $this->assertSame(7, $booking->rental_days);
        $this->assertEquals(24500, (float) $booking->base_amount);
        $this->assertEquals(1225, (float) $booking->discount); // 5% for 7+ days
        $this->assertEquals(33275, (float) $booking->total_amount);
    }

    public function test_submitted_prices_are_ignored(): void
    {
        $customer = $this->makeCustomer();
        $vehicle = $this->makeVehicle(['price_per_day' => 3500, 'security_deposit' => 10000]);

        $payload = $this->bookingPayload($vehicle, 3, 6);
        $payload['total_amount'] = '1';
        $payload['base_amount'] = '1';

        $this->actingAs($customer)->post(route('bookings.store'), $payload);

        $booking = Booking::firstOrFail();
        $this->assertEquals(10500, (float) $booking->base_amount);
        $this->assertEquals(20500, (float) $booking->total_amount);
    }

    public function test_overlapping_bookings_are_prevented(): void
    {
        $vehicle = $this->makeVehicle();
        $existingCustomer = $this->makeCustomer();
        $otherCustomer = $this->makeCustomer();

        $this->makeBooking($existingCustomer, $vehicle, [
            'pickup_date' => now()->addDays(4)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
            'booking_status' => Booking::STATUS_CONFIRMED,
        ]);

        $response = $this->actingAs($otherCustomer)
            ->from(route('bookings.create', $vehicle))
            ->post(route('bookings.store'), $this->bookingPayload($vehicle, 5, 6));

        $response->assertRedirect(route('bookings.create', $vehicle));
        $response->assertSessionHas('error');
        $this->assertSame(1, Booking::count());
    }

    public function test_cancelled_bookings_do_not_block_new_dates(): void
    {
        $vehicle = $this->makeVehicle();
        $customer = $this->makeCustomer();

        $this->makeBooking($customer, $vehicle, [
            'pickup_date' => now()->addDays(4)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
            'booking_status' => Booking::STATUS_CANCELLED,
        ]);

        $this->actingAs($customer)
            ->post(route('bookings.store'), $this->bookingPayload($vehicle, 5, 6))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Booking::count());
    }

    public function test_validation_rejects_past_dates_and_reversed_ranges(): void
    {
        $customer = $this->makeCustomer();
        $vehicle = $this->makeVehicle();

        $this->actingAs($customer)
            ->post(route('bookings.store'), array_merge($this->bookingPayload($vehicle), [
                'pickup_date' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('pickup_date');

        $this->actingAs($customer)
            ->post(route('bookings.store'), array_merge($this->bookingPayload($vehicle), [
                'pickup_date' => now()->addDays(5)->toDateString(),
                'return_date' => now()->addDays(3)->toDateString(),
            ]))
            ->assertSessionHasErrors('return_date');

        $this->assertSame(0, Booking::count());
    }

    public function test_booking_cannot_be_created_for_a_maintenance_vehicle(): void
    {
        $customer = $this->makeCustomer();
        $vehicle = $this->makeVehicle(['status' => 'maintenance']);

        $response = $this->actingAs($customer)
            ->from(route('vehicles.show', $vehicle))
            ->post(route('bookings.store'), $this->bookingPayload($vehicle));

        $response->assertSessionHas('error');
        $this->assertSame(0, Booking::count());
    }

    public function test_customer_can_cancel_their_own_booking(): void
    {
        $customer = $this->makeCustomer();
        $vehicle = $this->makeVehicle();
        $booking = $this->makeBooking($customer, $vehicle, ['booking_status' => Booking::STATUS_CONFIRMED]);

        $this->actingAs($customer)
            ->post(route('bookings.cancel', $booking))
            ->assertRedirect(route('bookings.show', $booking));

        $this->assertSame(Booking::STATUS_CANCELLED, $booking->fresh()->booking_status);
        $this->assertDatabaseHas('notifications', ['user_id' => $customer->id]);
    }
}
