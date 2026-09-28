<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('rideora.seed_demo_data')) {
            return;
        }

        $service = app(BookingService::class);
        $admin = User::where('is_admin', true)->first();
        $bkash = PaymentMethod::where('name', 'bKash')->first();
        $nagad = PaymentMethod::where('name', 'Nagad')->first();
        $bank = PaymentMethod::where('name', 'Bank Transfer')->first();

        if (! $admin || ! $bkash) {
            return;
        }

        $customers = [
            ['name' => 'Rakib Hasan', 'email' => 'rakib@example.com', 'phone' => '+880 1711-100001', 'city' => 'Dhaka'],
            ['name' => 'Nusrat Jahan', 'email' => 'nusrat@example.com', 'phone' => '+880 1711-100002', 'city' => 'Chattogram'],
            ['name' => 'Tanvir Ahmed', 'email' => 'tanvir@example.com', 'phone' => '+880 1711-100003', 'city' => 'Sylhet'],
            ['name' => 'Sadia Islam', 'email' => 'sadia@example.com', 'phone' => '+880 1711-100004', 'city' => 'Dhaka'],
        ];

        $users = [];
        foreach ($customers as $customer) {
            $users[$customer['email']] = User::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'phone' => $customer['phone'],
                    'city' => $customer['city'],
                    'address' => 'House 12, Road 5, '.$customer['city'],
                    'driving_license_no' => 'DK-'.random_int(100000, 999999),
                    'password' => config('rideora.demo_customer_password'),
                    'is_admin' => false,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }

        $vehicles = Vehicle::query()->get()->keyBy('name');

        if ($vehicles->isEmpty()) {
            return;
        }

        $rakib = $users['rakib@example.com'];
        $nusrat = $users['nusrat@example.com'];
        $tanvir = $users['tanvir@example.com'];
        $sadia = $users['sadia@example.com'];

        // 1) Completed rental with a verified payment and an approved review.
        $completed = $this->makeBooking($service, $rakib, $vehicles['Toyota Axio'], 20, 17, 'Dhaka', 'Rent for a family trip.');
        $this->addPayment($completed, $rakib, $bkash, Payment::STATUS_VERIFIED, $admin->id, 'BKS7H2K91A', 18);
        Review::create([
            'user_id' => $rakib->id,
            'vehicle_id' => $completed->vehicle_id,
            'booking_id' => $completed->id,
            'rating' => 5,
            'comment' => 'Spotless car, smooth pickup and the payment verification was completed the same day. Highly recommended.',
            'status' => Review::STATUS_APPROVED,
        ]);

        // 2) Upcoming confirmed booking.
        $confirmed = $this->makeBooking($service, $rakib, $vehicles['Honda Vezel'], -3, -5, 'Dhaka', 'Weekend trip to Cox\'s Bazar plan.');
        $this->addPayment($confirmed, $rakib, $nagad, Payment::STATUS_VERIFIED, $admin->id, 'NGD91PLO22', 2);

        // 3) Payment submitted and waiting for admin verification.
        $waiting = $this->makeBooking($service, $nusrat, $vehicles['Toyota Premio'], -6, -8, 'Chattogram', 'Corporate client visit.');
        $this->addPayment($waiting, $nusrat, $bkash, Payment::STATUS_PENDING, null, 'BKS4420ZZQ', 1);

        // 4) Completed rental the customer has not reviewed yet.
        $noReview = $this->makeBooking($service, $nusrat, $vehicles['Yamaha R15 V4'], 12, 11, 'Dhaka', 'Short city ride.');
        $this->addPayment($noReview, $nusrat, $bkash, Payment::STATUS_VERIFIED, $admin->id, 'BKS55GHT10', 9);

        // 5) Pending booking with no payment yet.
        $this->makeBooking($service, $tanvir, $vehicles['Toyota Noah'], -10, -12, 'Dhaka', 'Family tour to Sylhet.');

        // 6) Cancelled booking.
        $cancelled = $this->makeBooking($service, $tanvir, $vehicles['Honda Civic'], -15, -16, 'Dhaka', 'Plan changed.');
        $cancelled->update([
            'booking_status' => Booking::STATUS_CANCELLED,
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);

        // 7) Rental in progress.
        $ongoing = $this->makeBooking($service, $sadia, $vehicles['Toyota Hiace'], 1, -2, 'Dhaka', 'Group tour in progress.');
        $this->addPayment($ongoing, $sadia, $bank, Payment::STATUS_VERIFIED, $admin->id, 'CTB778811MA', 3);
        $ongoing->update(['booking_status' => Booking::STATUS_ONGOING, 'payment_status' => Booking::PAYMENT_PAID]);

        // 8) Older completed rental with a review, gives the homepage testimonials depth.
        $older = $this->makeBooking($service, $sadia, $vehicles['Nissan X-Trail'], 40, 45, 'Chattogram', 'Hill tract tour.');
        $this->addPayment($older, $sadia, $bank, Payment::STATUS_VERIFIED, $admin->id, 'CTB112233XY', 38);
        Review::create([
            'user_id' => $sadia->id,
            'vehicle_id' => $older->vehicle_id,
            'booking_id' => $older->id,
            'rating' => 4,
            'comment' => 'Great car for the hill roads and the staff explained everything clearly before pickup.',
            'status' => Review::STATUS_APPROVED,
        ]);

        // Historical completed rentals, spread over the last few months, so the
        // admin revenue chart has meaningful data.
        $history = [
            ['vehicle' => 'Toyota Aqua', 'user' => $tanvir, 'days' => 150, 'length' => 3, 'method' => 'bKash'],
            ['vehicle' => 'Toyota Axio', 'user' => $sadia, 'days' => 120, 'length' => 2, 'method' => 'Nagad'],
            ['vehicle' => 'Honda Civic', 'user' => $rakib, 'days' => 95, 'length' => 4, 'method' => 'Bank Transfer'],
            ['vehicle' => 'Toyota Hiace', 'user' => $nusrat, 'days' => 70, 'length' => 2, 'method' => 'bKash'],
            ['vehicle' => 'Toyota Noah', 'user' => $sadia, 'days' => 45, 'length' => 5, 'method' => 'Bank Transfer'],
            ['vehicle' => 'Yamaha R15 V4', 'user' => $rakib, 'days' => 25, 'length' => 2, 'method' => 'Nagad'],
        ];

        $methods = ['bKash' => $bkash, 'Nagad' => $nagad, 'Bank Transfer' => $bank];
        $sequence = 100;

        foreach ($history as $entry) {
            if (! isset($vehicles[$entry['vehicle']])) {
                continue;
            }

            $booking = $this->makeBooking(
                $service,
                $entry['user'],
                $vehicles[$entry['vehicle']],
                $entry['days'],
                $entry['days'] - $entry['length'],
                'Dhaka',
                'Completed rental.'
            );

            $paidAt = max($entry['days'] + 1, 1);

            $this->addPayment($booking, $entry['user'], $methods[$entry['method']] ?? $bkash, Payment::STATUS_VERIFIED, $admin->id, 'TRX'.(++$sequence).'HIS'.random_int(100, 999), $paidAt);

            $booking->update([
                'booking_status' => Booking::STATUS_COMPLETED,
                'payment_status' => Booking::PAYMENT_PAID,
            ]);
        }

        // Close out the first two demo rentals properly.
        $completed->update(['booking_status' => Booking::STATUS_COMPLETED, 'payment_status' => Booking::PAYMENT_PAID]);
        $confirmed->update(['booking_status' => Booking::STATUS_CONFIRMED, 'payment_status' => Booking::PAYMENT_PAID]);
        $waiting->update(['booking_status' => Booking::STATUS_PAYMENT_SUBMITTED, 'payment_status' => Booking::PAYMENT_PENDING]);
        $noReview->update(['booking_status' => Booking::STATUS_COMPLETED, 'payment_status' => Booking::PAYMENT_PAID]);
        $older->update(['booking_status' => Booking::STATUS_COMPLETED, 'payment_status' => Booking::PAYMENT_PAID]);

        // A few notifications so the bell dropdown is not empty.
        Notification::notify($rakib->id, 'Payment verified', 'Your payment for booking '.$completed->booking_code.' has been verified.', Notification::TYPE_PAYMENT, route('bookings.show', $completed));
        Notification::notify($rakib->id, 'Booking confirmed', 'Your booking '.$confirmed->booking_code.' is confirmed. Enjoy your ride!', Notification::TYPE_BOOKING, route('bookings.show', $confirmed));
        Notification::notify($nusrat->id, 'Payment submitted', 'We received your payment for '.$waiting->booking_code.'. Verification is in progress.', Notification::TYPE_PAYMENT, route('bookings.show', $waiting));
        Notification::notify($sadia->id, 'Rental in progress', 'Your rental for booking '.$ongoing->booking_code.' is now in progress.', Notification::TYPE_BOOKING, route('bookings.show', $ongoing));
        Notification::notify($tanvir->id, 'Booking cancelled', 'Booking '.$cancelled->booking_code.' has been cancelled.', Notification::TYPE_BOOKING, route('bookings.show', $cancelled));
        Notification::notify($admin->id, 'New payment awaiting verification', 'Booking '.$waiting->booking_code.' has a payment waiting for verification.', Notification::TYPE_PAYMENT, route('admin.payments.index'));

        $this->command?->info('Demo customers seeded (password: '.config('rideora.demo_customer_password').')');
    }

    /**
     * Create a booking through the real booking service so all amounts match production logic.
     */
    private function makeBooking(BookingService $service, User $user, Vehicle $vehicle, int $startOffsetDays, int $endOffsetDays, string $location, string $note): Booking
    {
        $pickup = Carbon::today()->subDays($startOffsetDays);
        $return = Carbon::today()->subDays($endOffsetDays);

        if ($return->lessThanOrEqualTo($pickup)) {
            $return = $pickup->copy()->addDay();
        }

        $booking = $service->createBooking($vehicle, $user->id, [
            'pickup_location' => $location,
            'dropoff_location' => $location,
            'pickup_date' => $pickup->toDateString(),
            'pickup_time' => '10:00',
            'return_date' => $return->toDateString(),
            'return_time' => '18:00',
            'customer_note' => $note,
        ]);

        $booking->update(['created_at' => $pickup->copy()->subDays(2)]);

        return $booking;
    }

    /**
     * Attach a manual payment to a booking.
     */
    private function addPayment(
        Booking $booking,
        User $user,
        PaymentMethod $method,
        string $status,
        ?int $verifiedBy,
        string $transactionId,
        int $daysAgo
    ): Payment {
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'payment_method_id' => $method->id,
            'amount' => $booking->total_amount,
            'transaction_id' => $transactionId,
            'payment_proof' => null,
            'status' => $status,
            'verified_by' => $verifiedBy,
            'verified_at' => $status === Payment::STATUS_VERIFIED ? Carbon::now()->subDays(max($daysAgo, 1)) : null,
            'admin_note' => $status === Payment::STATUS_VERIFIED ? 'Payment matched the bank statement.' : null,
        ]);

        $payment->update(['created_at' => Carbon::now()->subDays(max($daysAgo, 1))]);

        if ($status === Payment::STATUS_VERIFIED) {
            $booking->update([
                'payment_status' => Booking::PAYMENT_PAID,
                'booking_status' => Booking::STATUS_CONFIRMED,
            ]);
        } elseif ($status === Payment::STATUS_PENDING) {
            $booking->update([
                'payment_status' => Booking::PAYMENT_PENDING,
                'booking_status' => Booking::STATUS_PAYMENT_SUBMITTED,
            ]);
        }

        return $payment;
    }
}
