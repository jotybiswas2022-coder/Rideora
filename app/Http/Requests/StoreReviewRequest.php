<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer', Rule::exists('bookings', 'id')],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    protected function passedValidation(): void
    {
        /** @var Booking|null $booking */
        $booking = Booking::with('review')->find($this->input('booking_id'));

        if (! $booking || $booking->user_id !== $this->user()->id) {
            throw ValidationException::withMessages([
                'booking_id' => 'You can only review your own completed bookings.',
            ]);
        }

        if ($booking->booking_status !== Booking::STATUS_COMPLETED) {
            throw ValidationException::withMessages([
                'booking_id' => 'Reviews are only allowed after the rental is completed.',
            ]);
        }

        if ($booking->review) {
            throw ValidationException::withMessages([
                'booking_id' => 'You have already reviewed this rental.',
            ]);
        }
    }
}
