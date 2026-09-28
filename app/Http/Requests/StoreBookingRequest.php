<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
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
            'vehicle_id' => ['required', 'integer', Rule::exists('vehicles', 'id')],
            'pickup_location' => ['required', 'string', 'min:3', 'max:160'],
            'dropoff_location' => ['nullable', 'string', 'min:3', 'max:160'],
            'pickup_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'return_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:pickup_date'],
            'return_time' => ['required', 'date_format:H:i'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pickup_date.after_or_equal' => 'The pickup date cannot be in the past.',
            'return_date.after_or_equal' => 'The return date cannot be before the pickup date.',
        ];
    }

    protected function passedValidation(): void
    {
        $pickup = Carbon::parse($this->input('pickup_date').' '.$this->input('pickup_time'));
        $return = Carbon::parse($this->input('return_date').' '.$this->input('return_time'));

        if ($return->lessThanOrEqualTo($pickup)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'return_time' => 'The return date and time must be after the pickup date and time.',
            ]);
        }
    }

    public function vehicle(): Vehicle
    {
        return Vehicle::findOrFail($this->integer('vehicle_id'));
    }

    /**
     * Overlap window used for availability checks (end dates are exclusive).
     */
    public function pickupDate(): string
    {
        return (string) $this->input('pickup_date');
    }

    public function returnDate(): string
    {
        return (string) $this->input('return_date');
    }
}
