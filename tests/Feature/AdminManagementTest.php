<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesRideoraData;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use CreatesRideoraData;
    use RefreshDatabase;

    public function test_admin_can_create_a_vehicle_with_images(): void
    {
        Storage::fake('public');

        $admin = $this->makeAdmin();
        $category = $this->makeCategory();

        $response = $this->actingAs($admin)->post(route('admin.vehicles.store'), [
            'category_id' => $category->id,
            'name' => 'Honda Vezel',
            'brand' => 'Honda',
            'model' => 'Hybrid Z',
            'registration_number' => 'DHA-GA-99-1234',
            'vehicle_type' => 'SUV',
            'fuel_type' => 'Hybrid',
            'transmission' => 'CVT',
            'seats' => 5,
            'price_per_hour' => 700,
            'price_per_day' => 5500,
            'security_deposit' => 15000,
            'location' => 'Dhaka',
            'description' => 'Compact hybrid SUV.',
            'status' => 'available',
            'images' => [$this->fakePng('vezel-front.png'), $this->fakePng('vezel-back.png')],
            'primary_index' => 0,
        ]);

        $response->assertRedirect(route('admin.vehicles.index'));

        $vehicle = Vehicle::where('registration_number', 'DHA-GA-99-1234')->firstOrFail();

        $this->assertSame('honda-vezel', $vehicle->slug);
        $this->assertSame(2, $vehicle->images()->count());
        $this->assertSame(1, $vehicle->images()->where('is_primary', true)->count());
        $this->assertNotNull($vehicle->images()->first()->image);
        Storage::disk('public')->assertExists($vehicle->images()->first()->image);
    }

    public function test_vehicle_validation_rejects_duplicate_registration_numbers(): void
    {
        Storage::fake('public');

        $admin = $this->makeAdmin();
        $existing = $this->makeVehicle(['registration_number' => 'DHA-GA-11-2345']);

        $this->actingAs($admin)->post(route('admin.vehicles.store'), [
            'category_id' => $existing->category_id,
            'name' => 'Toyota Axio Copy',
            'brand' => 'Toyota',
            'registration_number' => 'DHA-GA-11-2345',
            'vehicle_type' => 'Car',
            'fuel_type' => 'Petrol',
            'transmission' => 'Automatic',
            'seats' => 5,
            'price_per_hour' => 400,
            'price_per_day' => 3000,
            'security_deposit' => 5000,
            'status' => 'available',
        ])->assertSessionHasErrors('registration_number');

        $this->assertSame(1, Vehicle::count());
    }

    public function test_admin_can_update_a_vehicle(): void
    {
        $admin = $this->makeAdmin();
        $vehicle = $this->makeVehicle();

        $this->actingAs($admin)->put(route('admin.vehicles.update', $vehicle), [
            'category_id' => $vehicle->category_id,
            'name' => 'Toyota Axio Facelift',
            'brand' => 'Toyota',
            'model' => 'Axio Hybrid G',
            'registration_number' => $vehicle->registration_number,
            'vehicle_type' => 'Car',
            'fuel_type' => 'Hybrid',
            'transmission' => 'Automatic',
            'seats' => 5,
            'price_per_hour' => 500,
            'price_per_day' => 4000,
            'security_deposit' => 12000,
            'location' => 'Dhaka',
            'status' => 'maintenance',
        ])->assertRedirect(route('admin.vehicles.edit', $vehicle));

        $vehicle->refresh();

        $this->assertSame('Toyota Axio Facelift', $vehicle->name);
        $this->assertEquals(4000, (float) $vehicle->price_per_day);
        $this->assertSame('maintenance', $vehicle->status);
    }

    public function test_admin_can_change_vehicle_status(): void
    {
        $admin = $this->makeAdmin();
        $vehicle = $this->makeVehicle();

        $this->actingAs($admin)
            ->post(route('admin.vehicles.status', $vehicle), ['status' => 'maintenance'])
            ->assertSessionHasNoErrors();

        $this->assertSame('maintenance', $vehicle->fresh()->status);
    }

    public function test_admin_cannot_delete_a_vehicle_with_active_bookings(): void
    {
        $admin = $this->makeAdmin();
        $vehicle = $this->makeVehicle();
        $booking = $this->makeBooking($this->makeCustomer(), $vehicle, ['booking_status' => Booking::STATUS_CONFIRMED]);

        $this->actingAs($admin)
            ->from(route('admin.vehicles.index'))
            ->delete(route('admin.vehicles.destroy', $vehicle))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id]);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    public function test_admin_can_manage_categories(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Luxury',
            'description' => 'Premium vehicles.',
            'status' => 'active',
        ])->assertRedirect(route('admin.categories.index'));

        $category = VehicleCategory::where('slug', 'luxury')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Luxury Cars',
            'status' => 'inactive',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Luxury Cars', $category->fresh()->name);
        $this->assertSame('inactive', $category->fresh()->status);
    }

    public function test_category_with_vehicles_cannot_be_deleted(): void
    {
        $admin = $this->makeAdmin();
        $vehicle = $this->makeVehicle();

        $this->actingAs($admin)
            ->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $vehicle->category))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('vehicle_categories', ['id' => $vehicle->category_id]);
    }

    public function test_admin_can_manage_payment_methods(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.payment-methods.store'), [
            'name' => 'Rocket',
            'account_name' => 'Rideora Rentals',
            'account_number' => '01911-000003',
            'instructions' => "Send money then upload the receipt.",
            'status' => 'active',
        ])->assertRedirect(route('admin.payment-methods.index'));

        $this->assertDatabaseHas('payment_methods', ['name' => 'Rocket', 'status' => 'active']);
    }

    public function test_admin_can_update_website_settings(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site_name' => 'Rideora Rentals',
            'site_tagline' => 'Your Ride, Your Way.',
            'support_email' => 'hello@rideora.test',
            'support_phone' => '+880 1900-000000',
            'office_address' => 'Gulshan, Dhaka',
            'currency_symbol' => '৳',
            'booking_advance_percent' => 10,
            'about_content' => 'Rideora is a rental platform.',
        ])->assertRedirect(route('admin.settings.index'));

        $this->assertSame('Rideora Rentals', Setting::get('site_name'));
        $this->assertSame('+880 1900-000000', Setting::get('support_phone'));
    }

    public function test_settings_validation_rejects_invalid_email(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site_name' => 'Rideora',
            'support_email' => 'not-an-email',
            'support_phone' => '+880 1900-000000',
            'currency_symbol' => '৳',
            'booking_advance_percent' => 0,
        ])->assertSessionHasErrors('support_email');
    }

    public function test_admin_can_approve_and_reject_reviews(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $vehicle = $this->makeVehicle();
        $booking = $this->makeBooking($customer, $vehicle, ['booking_status' => Booking::STATUS_COMPLETED]);

        $review = Review::create([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'booking_id' => $booking->id,
            'rating' => 5,
            'comment' => 'Excellent service and a spotless car.',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($admin)->post(route('admin.reviews.approve', $review))->assertSessionHasNoErrors();
        $this->assertSame(Review::STATUS_APPROVED, $review->fresh()->status);
        $this->assertDatabaseHas('notifications', ['user_id' => $customer->id, 'title' => 'Review published']);

        // Approved reviews are visible on the public vehicle page.
        $this->get(route('vehicles.show', $vehicle))->assertOk()->assertSee('Excellent service and a spotless car.');

        $this->actingAs($admin)->post(route('admin.reviews.reject', $review))->assertSessionHasNoErrors();
        $this->assertSame(Review::STATUS_REJECTED, $review->fresh()->status);
    }

    public function test_admin_can_update_booking_status_and_the_customer_is_notified(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $booking = $this->makeBooking($customer, $this->makeVehicle(), [
            'booking_status' => Booking::STATUS_PAYMENT_SUBMITTED,
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);

        $this->actingAs($admin)->put(route('admin.bookings.update', $booking), [
            'booking_status' => Booking::STATUS_CONFIRMED,
            'payment_status' => Booking::PAYMENT_PAID,
            'admin_note' => 'Payment verified at the branch.',
        ])->assertRedirect(route('admin.bookings.show', $booking));

        $booking->refresh();

        $this->assertSame(Booking::STATUS_CONFIRMED, $booking->booking_status);
        $this->assertSame(Booking::PAYMENT_PAID, $booking->payment_status);
        $this->assertSame('Payment verified at the branch.', $booking->admin_note);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'title' => 'Booking Confirmed',
        ]);
    }

    public function test_admin_can_deactivate_a_customer(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();

        $this->actingAs($admin)->post(route('admin.users.toggle', $customer))->assertSessionHasNoErrors();

        $this->assertSame('inactive', $customer->fresh()->status);
    }

    public function test_admin_can_set_a_primary_vehicle_image(): void
    {
        $admin = $this->makeAdmin();
        $vehicle = $this->makeVehicle();

        $first = VehicleImage::create(['vehicle_id' => $vehicle->id, 'image' => 'vehicles/a.svg', 'is_primary' => true]);
        $second = VehicleImage::create(['vehicle_id' => $vehicle->id, 'image' => 'vehicles/b.svg', 'is_primary' => false]);

        $this->actingAs($admin)
            ->post(route('admin.vehicles.image.primary', [$vehicle, $second]))
            ->assertSessionHasNoErrors();

        $this->assertFalse($first->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);
    }
}
