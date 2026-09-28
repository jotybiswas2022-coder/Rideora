<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'site_name' => ['required', 'string', 'max:120'],
            'site_tagline' => ['nullable', 'string', 'max:190'],
            'support_email' => ['required', 'email:filter', 'max:180'],
            'support_phone' => ['required', 'string', 'max:40'],
            'office_address' => ['nullable', 'string', 'max:255'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'booking_advance_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'facebook_url' => ['nullable', 'url', 'max:190'],
            'instagram_url' => ['nullable', 'url', 'max:190'],
            'twitter_url' => ['nullable', 'url', 'max:190'],
            'youtube_url' => ['nullable', 'url', 'max:190'],
            'about_content' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
