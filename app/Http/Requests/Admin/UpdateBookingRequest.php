<?php

namespace App\Http\Requests\Admin;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'booking_status' => ['required', Rule::in(Booking::STATUSES)],
            'payment_status' => ['required', Rule::in(Booking::PAYMENT_STATUSES)],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
