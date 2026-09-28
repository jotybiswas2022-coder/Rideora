<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->isAdmin() || $payment->user_id === $user->id;
    }

    /**
     * The customer facing payment screens are owner only. Administrators review
     * payments from the admin panel instead.
     */
    public function owns(User $user, Payment $payment): bool
    {
        return $payment->user_id === $user->id;
    }

    /**
     * Only administrators verify or reject manual payments.
     */
    public function verify(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }
}
