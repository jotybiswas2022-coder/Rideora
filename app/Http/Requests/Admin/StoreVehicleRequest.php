<?php

namespace App\Http\Requests\Admin;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
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
            'category_id' => ['required', 'integer', Rule::exists('vehicle_categories', 'id')],
            'name' => ['required', 'string', 'min:2', 'max:160'],
            'brand' => ['required', 'string', 'min:2', 'max:120'],
            'model' => ['nullable', 'string', 'max:120'],
            'registration_number' => ['required', 'string', 'max:60', 'unique:vehicles,registration_number'],
            'vehicle_type' => ['required', Rule::in(Vehicle::VEHICLE_TYPES)],
            'fuel_type' => ['required', Rule::in(Vehicle::FUEL_TYPES)],
            'transmission' => ['required', Rule::in(Vehicle::TRANSMISSIONS)],
            'seats' => ['required', 'integer', 'min:1', 'max:60'],
            'price_per_hour' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'price_per_day' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'security_deposit' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'location' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', Rule::in(Vehicle::STATUSES)],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
            'primary_index' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'registration_number.unique' => 'A vehicle with this registration number already exists.',
            'images.max' => 'You may upload up to 8 images per vehicle.',
            'images.*.max' => 'Each vehicle image may not be larger than 4 MB.',
        ];
    }
}
