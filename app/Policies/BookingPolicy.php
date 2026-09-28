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

    /**
     * Managing a booking is always limited to its owner, administrators included.
     */
    public function owns(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $this->owns($user, $booking) && $booking->canBeCancelled();
    }

    public function pay(User $user, Booking $booking): bool
    {
        return $this->owns($user, $booking) && $booking->canBePaid();
    }

    public function review(User $user, Booking $booking): bool
    {
        return $this->owns($user, $booking) && $booking->canBeReviewed();
    }
}
