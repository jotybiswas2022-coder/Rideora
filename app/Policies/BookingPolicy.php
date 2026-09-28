<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Admins may see everything, customers only their own bookings.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->user_id === $user->id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return ! $user->isAdmin() && $booking->user_id === $user->id && $booking->canBeCancelled();
    }

    public function pay(User $user, Booking $booking): bool
    {
        return ! $user->isAdmin() && $booking->user_id === $user->id && $booking->canBePaid();
    }

    public function review(User $user, Booking $booking): bool
    {
        return ! $user->isAdmin() && $booking->user_id === $user->id && $booking->canBeReviewed();
    }
}
