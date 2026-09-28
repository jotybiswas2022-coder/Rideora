<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesRideoraData;
use Tests\TestCase;

class ManualPaymentTest extends TestCase
{
    use CreatesRideoraData;
    use RefreshDatabase;

    public function test_customer_can_submit_a_manual_payment(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer();
        $admin = $this->makeAdmin();
        $vehicle = $this->makeVehicle();
        $method = $this->makePaymentMethod();
        $booking = $this->makeBooking($customer, $vehicle);

        $response = $this->actingAs($customer)->post(route('payments.submit'), [
            'booking_id' => $booking->id,
            'payment_method_id' => $method->id,
            'amount' => $booking->total_amount,
            'transaction_id' => 'BKS7H2K91A',
            'payment_proof' => $this->fakePng('bkash-receipt.png'),
            'agree_terms' => '1',
        ]);

        $payment = Payment::firstOrFail();

        $response->assertRedirect(route('payments.success', $payment));

        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame($customer->id, $payment->user_id);
        $this->assertNotNull($payment->payment_proof);
        Storage::disk('public')->assertExists($payment->payment_proof);

        $booking->refresh();
        $this->assertSame(Booking::PAYMENT_PENDING, $booking->payment_status);
        $this->assertSame(Booking::STATUS_PAYMENT_SUBMITTED, $booking->booking_status);

        $this->assertDatabaseHas('notifications', ['user_id' => $customer->id]);
        $this->assertDatabaseHas('notifications', ['user_id' => $admin->id]);
    }

    public function test_payment_page_offers_screenshot_preview_and_remove(): void
    {
        $customer = $this->makeCustomer();
        $this->makePaymentMethod();
        $booking = $this->makeBooking($customer, $this->makeVehicle());

        $response = $this->actingAs($customer)->get(route('bookings.payment', $booking));

        $response->assertOk();

        // The file input is visually hidden but still focusable, so `required` keeps working.
        $response->assertSee('class="upload-wrap', false);
        $response->assertSee('class="sr-only" required data-image-input', false);

        // Preview shell, filled in by FileReader once a file is chosen.
        $response->assertSee('data-image-preview', false);
        $response->assertSee('data-preview-img', false);
        $response->assertSee('data-preview-name', false);
        $response->assertSee('data-preview-size', false);

        // Remove action and a slot for client side validation messages.
        $response->assertSee('data-preview-remove', false);
        $response->assertSee('id="payment_proof_error"', false);

        $response->assertSee('FileReader', false);
        $response->assertSee('MAX_BYTES = 3 * 1024 * 1024', false);
        $response->assertSee('Remove', false);
    }

    public function test_payment_page_shows_the_upload_error_after_a_failed_submission(): void
    {
        $customer = $this->makeCustomer();
        $method = $this->makePaymentMethod();
        $booking = $this->makeBooking($customer, $this->makeVehicle());

        $this->actingAs($customer)->post(route('payments.submit'), [
            'booking_id' => $booking->id,
            'payment_method_id' => $method->id,
            'amount' => $booking->total_amount,
            'transaction_id' => 'BKS7777777',
            // payment_proof deliberately missing
            'agree_terms' => '1',
        ])->assertSessionHasErrors('payment_proof');

        $response = $this->actingAs($customer)->get(route('bookings.payment', $booking));

        $response->assertOk();
        $response->assertSee('has-error', false);
        $response->assertSee('Please attach the payment screenshot.', false);
    }

    public function test_uploaded_payment_proof_is_served_through_the_media_route(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer();
        $booking = $this->makeBooking($customer, $this->makeVehicle());
        $method = $this->makePaymentMethod();

        $this->actingAs($customer)->post(route('payments.submit'), [
            'booking_id' => $booking->id,
            'payment_method_id' => $method->id,
            'amount' => $booking->total_amount,
            'transaction_id' => 'MEDIA12345',
            'payment_proof' => $this->fakePng('receipt.png'),
            'agree_terms' => '1',
        ]);

        $payment = Payment::firstOrFail();

        $this->get($payment->proofUrl())->assertOk();
        $this->get(route('media.show', ['path' => 'payments/does-not-exist.png']))->assertNotFound();
    }

    public function test_payment_proof_must_be_a_valid_image(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer();
        $booking = $this->makeBooking($customer, $this->makeVehicle());
        $method = $this->makePaymentMethod();

        $response = $this->actingAs($customer)->post(route('payments.submit'), [
            'booking_id' => $booking->id,
            'payment_method_id' => $method->id,
            'amount' => $booking->total_amount,
            'transaction_id' => 'BKS0000001',
            'payment_proof' => UploadedFile::fake()->create('script.php', 5, 'application/x-php'),
            'agree_terms' => '1',
        ]);

        $response->assertSessionHasErrors('payment_proof');
        $this->assertSame(0, Payment::count());
        $this->assertSame(Booking::PAYMENT_UNPAID, $booking->fresh()->payment_status);
    }

    public function test_customer_cannot_submit_a_payment_for_someone_elses_booking(): void
    {
        Storage::fake('public');

        $owner = $this->makeCustomer();
        $intruder = $this->makeCustomer();
        $booking = $this->makeBooking($owner, $this->makeVehicle());
        $method = $this->makePaymentMethod();

        $response = $this->actingAs($intruder)->post(route('payments.submit'), [
            'booking_id' => $booking->id,
            'payment_method_id' => $method->id,
            'amount' => $booking->total_amount,
            'transaction_id' => 'HACK12345',
            'payment_proof' => $this->fakePng(),
            'agree_terms' => '1',
        ]);

        $response->assertSessionHasErrors('booking_id');
        $this->assertSame(0, Payment::count());
    }

    public function test_duplicate_transaction_ids_are_rejected_while_pending(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer();
        $vehicle = $this->makeVehicle();
        $method = $this->makePaymentMethod();

        $first = $this->makeBooking($customer, $vehicle);
        $second = $this->makeBooking($customer, $vehicle, [
            'booking_code' => 'VR-20260928-9999',
            'pickup_date' => now()->addDays(10)->toDateString(),
            'return_date' => now()->addDays(12)->toDateString(),
        ]);

        $payload = [
            'payment_method_id' => $method->id,
            'amount' => 1000,
            'transaction_id' => 'DUP1234567',
            'payment_proof' => $this->fakePng(),
            'agree_terms' => '1',
        ];

        $this->actingAs($customer)->post(route('payments.submit'), $payload + ['booking_id' => $first->id]);
        $this->assertSame(1, Payment::count());

        $response = $this->actingAs($customer)->post(route('payments.submit'), $payload + ['booking_id' => $second->id]);

        $response->assertSessionHasErrors('transaction_id');
        $this->assertSame(1, Payment::count());
    }

    public function test_admin_can_verify_a_payment_and_confirm_the_booking(): void
    {
        $customer = $this->makeCustomer();
        $admin = $this->makeAdmin();
        $booking = $this->makeBooking($customer, $this->makeVehicle(), [
            'booking_status' => Booking::STATUS_PAYMENT_SUBMITTED,
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $customer->id,
            'payment_method_id' => $this->makePaymentMethod()->id,
            'amount' => $booking->total_amount,
            'transaction_id' => 'VERIFY1234',
            'status' => Payment::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.verify', $payment), ['admin_note' => 'Matched bank statement'])
            ->assertRedirect(route('admin.payments.show', $payment));

        $payment->refresh();
        $booking->refresh();

        $this->assertSame(Payment::STATUS_VERIFIED, $payment->status);
        $this->assertSame($admin->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);
        $this->assertSame(Booking::PAYMENT_PAID, $booking->payment_status);
        $this->assertSame(Booking::STATUS_CONFIRMED, $booking->booking_status);
        $this->assertDatabaseHas('notifications', ['user_id' => $customer->id, 'title' => 'Payment verified']);
    }

    public function test_admin_can_reject_a_payment_with_a_reason(): void
    {
        $customer = $this->makeCustomer();
        $admin = $this->makeAdmin();
        $booking = $this->makeBooking($customer, $this->makeVehicle(), [
            'booking_status' => Booking::STATUS_PAYMENT_SUBMITTED,
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $customer->id,
            'payment_method_id' => $this->makePaymentMethod()->id,
            'amount' => 500,
            'transaction_id' => 'REJECT1234',
            'status' => Payment::STATUS_PENDING,
        ]);

        // A reason is required so the customer knows what to fix.
        $this->actingAs($admin)
            ->post(route('admin.payments.reject', $payment), [])
            ->assertSessionHasErrors('admin_note');

        $this->actingAs($admin)
            ->post(route('admin.payments.reject', $payment), ['admin_note' => 'Amount did not match the booking total.'])
            ->assertRedirect(route('admin.payments.show', $payment));

        $payment->refresh();
        $booking->refresh();

        $this->assertSame(Payment::STATUS_REJECTED, $payment->status);
        $this->assertSame(Booking::PAYMENT_REJECTED, $booking->payment_status);
        $this->assertSame(Booking::STATUS_PENDING, $booking->booking_status);
        $this->assertSame(1, $booking->payments()->where('status', Payment::STATUS_REJECTED)->count());
    }

    public function test_customer_can_resubmit_after_a_rejection(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer();
        $booking = $this->makeBooking($customer, $this->makeVehicle(), [
            'booking_status' => Booking::STATUS_PENDING,
            'payment_status' => Booking::PAYMENT_REJECTED,
        ]);
        $method = $this->makePaymentMethod();

        Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $customer->id,
            'payment_method_id' => $method->id,
            'amount' => 500,
            'transaction_id' => 'FIRST12345',
            'status' => Payment::STATUS_REJECTED,
        ]);

        $this->actingAs($customer)->post(route('payments.submit'), [
            'booking_id' => $booking->id,
            'payment_method_id' => $method->id,
            'amount' => $booking->total_amount,
            'transaction_id' => 'SECOND6789',
            'payment_proof' => $this->fakePng(),
            'agree_terms' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertSame(2, Payment::count());
        $this->assertSame(Booking::PAYMENT_PENDING, $booking->fresh()->payment_status);
    }

    public function test_paid_bookings_reject_further_payments(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer();
        $booking = $this->makeBooking($customer, $this->makeVehicle(), [
            'booking_status' => Booking::STATUS_CONFIRMED,
            'payment_status' => Booking::PAYMENT_PAID,
        ]);
        $method = $this->makePaymentMethod();

        $this->actingAs($customer)->post(route('payments.submit'), [
            'booking_id' => $booking->id,
            'payment_method_id' => $method->id,
            'amount' => 1000,
            'transaction_id' => 'ALREADYPAID',
            'payment_proof' => $this->fakePng(),
            'agree_terms' => '1',
        ])->assertSessionHasErrors('booking_id');

        $this->assertSame(0, Payment::count());
    }
}
