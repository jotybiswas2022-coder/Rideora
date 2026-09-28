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
     * Only administrators verify or reject manual payments.
     */
    public function verify(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }
}
