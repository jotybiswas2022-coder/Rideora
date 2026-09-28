<?php

namespace Tests\Feature;

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRideoraData;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use CreatesRideoraData;
    use RefreshDatabase;

    /**
     * @return array<int, string>
     */
    private function adminRoutes(): array
    {
        return [
            route('admin.dashboard'),
            route('admin.vehicles.index'),
            route('admin.vehicles.create'),
            route('admin.categories.index'),
            route('admin.bookings.index'),
            route('admin.payments.index'),
            route('admin.users.index'),
            route('admin.reviews.index'),
            route('admin.payment-methods.index'),
            route('admin.settings.index'),
        ];
    }

    public function test_guests_are_redirected_to_login_from_protected_pages(): void
    {
        foreach (array_merge($this->adminRoutes(), [
            route('customer.dashboard'),
            route('bookings.index'),
            route('profile.index'),
            route('reviews.index'),
            route('notifications.index'),
        ]) as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_customers_cannot_reach_admin_routes(): void
    {
        $customer = $this->makeCustomer();

        foreach ($this->adminRoutes() as $url) {
            $this->actingAs($customer)
                ->get($url)
                ->assertRedirect(route('customer.dashboard'));
        }
    }

    public function test_administrators_are_redirected_away_from_customer_routes(): void
    {
        $admin = $this->makeAdmin();

        foreach ([
            route('customer.dashboard'),
            route('bookings.index'),
            route('profile.index'),
            route('reviews.index'),
            route('notifications.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertRedirect(route('admin.dashboard'));
        }
    }

    public function test_customers_cannot_view_or_cancel_another_customers_booking(): void
    {
        $owner = $this->makeCustomer();
        $intruder = $this->makeCustomer();
        $booking = $this->makeBooking($owner, $this->makeVehicle());

        $this->actingAs($intruder)->get(route('bookings.show', $booking))->assertForbidden();
        $this->actingAs($intruder)->post(route('bookings.cancel', $booking))->assertForbidden();
        $this->actingAs($intruder)->get(route('bookings.payment', $booking))->assertForbidden();

        $this->assertSame('pending', $booking->fresh()->booking_status);
    }

    public function test_customers_cannot_view_another_customers_payment(): void
    {
        $owner = $this->makeCustomer();
        $intruder = $this->makeCustomer();
        $booking = $this->makeBooking($owner, $this->makeVehicle());

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $owner->id,
            'payment_method_id' => $this->makePaymentMethod()->id,
            'amount' => 1000,
            'transaction_id' => 'PRIVATE123',
            'status' => Payment::STATUS_PENDING,
        ]);

        $this->actingAs($intruder)->get(route('payments.success', $payment))->assertForbidden();
        $this->actingAs($owner)->get(route('payments.success', $payment))->assertOk();
    }

    public function test_administrators_cannot_pay_or_cancel_customer_bookings(): void
    {
        $customer = $this->makeCustomer();
        $admin = $this->makeAdmin();
        $booking = $this->makeBooking($customer, $this->makeVehicle());

        // Admins are redirected out of the customer area before reaching the action.
        $this->actingAs($admin)->post(route('bookings.cancel', $booking))->assertRedirect(route('admin.dashboard'));
        $this->assertSame('pending', $booking->fresh()->booking_status);
    }

    public function test_admin_can_view_the_admin_panel(): void
    {
        $this->actingAs($this->makeAdmin())->get(route('admin.dashboard'))->assertOk();
    }

    public function test_customer_can_view_their_own_booking(): void
    {
        $customer = $this->makeCustomer();
        $booking = $this->makeBooking($customer, $this->makeVehicle());

        $this->actingAs($customer)->get(route('bookings.show', $booking))->assertOk()->assertSee($booking->booking_code);
    }
}
