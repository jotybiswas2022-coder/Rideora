<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentMethodRequest extends FormRequest
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
        $methodId = $this->route('payment_method')?->id;

        return [
            'name' => ['required', 'string', 'min:2', 'max:120', Rule::unique('payment_methods', 'name')->ignore($methodId)],
            'account_name' => ['required', 'string', 'min:2', 'max:160'],
            'account_number' => ['required', 'string', 'min:4', 'max:120'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
