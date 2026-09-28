<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StorePaymentRequest extends FormRequest
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
            'payment_method_id' => [
                'required',
                'integer',
                Rule::exists('payment_methods', 'id')->where('status', 'active'),
            ],
            'amount' => ['required', 'numeric', 'min:1', 'max:10000000'],
            'transaction_id' => ['required', 'string', 'min:4', 'max:120', 'regex:/^[A-Za-z0-9\-\/]+$/'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:3072'],
            'agree_terms' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_method_id.exists' => 'The selected payment method is not available.',
            'transaction_id.regex' => 'Transaction ID may only contain letters, numbers, dashes and slashes.',
            'payment_proof.required' => 'Please attach the payment screenshot.',
            'payment_proof.max' => 'The payment screenshot may not be larger than 3 MB.',
            'payment_proof.mimes' => 'The payment screenshot must be a JPG, PNG or WEBP image.',
            'agree_terms.accepted' => 'Please confirm the submitted information is accurate.',
        ];
    }

    protected function passedValidation(): void
    {
        /** @var Booking|null $booking */
        $booking = Booking::find($this->input('booking_id'));

        if (! $booking || $booking->user_id !== $this->user()->id) {
            throw ValidationException::withMessages([
                'booking_id' => 'The selected booking does not belong to your account.',
            ]);
        }

        if ($booking->payment_status === Booking::PAYMENT_PAID) {
            throw ValidationException::withMessages([
                'booking_id' => 'This booking is already paid.',
            ]);
        }

        // Block duplicate transaction IDs that are still pending or already verified.
        $duplicate = Payment::query()
            ->where('transaction_id', $this->input('transaction_id'))
            ->where('payment_method_id', $this->input('payment_method_id'))
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_VERIFIED])
            ->whereKeyNot($this->input('payment_id'))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'transaction_id' => 'This transaction ID has already been submitted for this payment method.',
            ]);
        }
    }
}
