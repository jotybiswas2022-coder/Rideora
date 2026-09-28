<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;

class NotificationService
{
    public function bookingCreated(Booking $booking): void
    {
        Notification::notify(
            $booking->user_id,
            'Booking created',
            'Your booking '.$booking->booking_code.' for '.$booking->vehicle->name.' is pending payment.',
            Notification::TYPE_BOOKING,
            route('bookings.show', $booking)
        );
    }

    public function paymentSubmitted(Booking $booking, Payment $payment): void
    {
        Notification::notify(
            $booking->user_id,
            'Payment submitted',
            'We received your payment for '.$booking->booking_code.'. Our team will verify it shortly.',
            Notification::TYPE_PAYMENT,
            route('payments.success', $payment)
        );

        $this->notifyAdmins(
            'New payment awaiting verification',
            'Booking '.$booking->booking_code.' has a payment of '.bdt($payment->amount).' waiting for verification.',
            Notification::TYPE_PAYMENT,
            route('admin.payments.show', $payment)
        );
    }

    public function paymentVerified(Booking $booking, Payment $payment): void
    {
        Notification::notify(
            $booking->user_id,
            'Payment verified',
            'Your payment for booking '.$booking->booking_code.' has been verified. The booking is confirmed.',
            Notification::TYPE_PAYMENT,
            route('bookings.show', $booking)
        );
    }

    public function paymentRejected(Booking $booking, Payment $payment): void
    {
        Notification::notify(
            $booking->user_id,
            'Payment rejected',
            'Your payment for booking '.$booking->booking_code.' was rejected. '
            .($payment->admin_note ? 'Reason: '.$payment->admin_note : 'Please submit a valid payment proof.'),
            Notification::TYPE_PAYMENT,
            route('bookings.payment', $booking)
        );
    }

    public function bookingStatusChanged(Booking $booking, string $oldStatus): void
    {
        $label = $booking->statusLabel();

        $message = match ($booking->booking_status) {
            Booking::STATUS_CONFIRMED => 'Your booking '.$booking->booking_code.' has been confirmed. Enjoy your ride!',
            Booking::STATUS_ONGOING => 'Your rental for booking '.$booking->booking_code.' is now in progress.',
            Booking::STATUS_COMPLETED => 'Your rental for booking '.$booking->booking_code.' is complete. '
                .'We would love to hear your feedback.',
            Booking::STATUS_CANCELLED => 'Booking '.$booking->booking_code.' has been cancelled.',
            Booking::STATUS_REJECTED => 'Booking '.$booking->booking_code.' has been rejected.'
                .($booking->admin_note ? ' Reason: '.$booking->admin_note : ''),
            default => 'Booking '.$booking->booking_code.' is now marked as '.$label.'.',
        };

        Notification::notify(
            $booking->user_id,
            'Booking '.$label,
            $message,
            Notification::TYPE_BOOKING,
            route('bookings.show', $booking)
        );
    }

    /**
     * Admin facing notification: every administrator receives a copy.
     */
    public function notifyAdmins(string $title, string $message, string $type = Notification::TYPE_GENERAL, ?string $link = null): void
    {
        $adminIds = \App\Models\User::query()->where('is_admin', true)->pluck('id');

        foreach ($adminIds as $adminId) {
            Notification::notify($adminId, $title, $message, $type, $link);
        }
    }
}
