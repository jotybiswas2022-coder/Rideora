<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Default settings used to seed the table and to fall back on missing keys.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'site_name' => 'Rideora',
            'site_tagline' => 'Your Ride, Your Way.',
            'support_email' => 'support@rideora.test',
            'support_phone' => '+880 1700-000000',
            'office_address' => 'House 21, Road 7, Banani, Dhaka 1213, Bangladesh',
            'currency_symbol' => '৳',
            'booking_advance_percent' => '0',
            'facebook_url' => 'https://facebook.com/',
            'instagram_url' => 'https://instagram.com/',
            'twitter_url' => 'https://twitter.com/',
            'youtube_url' => 'https://youtube.com/',
            'about_content' => 'Rideora is a vehicle rental platform built to make renting a car, bike or microbus simple, transparent and reliable.',
        ];
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = static::allCached();

        return $settings[$key] ?? $default ?? (self::defaults()[$key] ?? null);
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('rideora.settings');
    }

    /**
     * @return array<string, string|null>
     */
    public static function allCached(): array
    {
        return Cache::remember('rideora.settings', 600, function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    public static function flush(): void
    {
        Cache::forget('rideora.settings');
    }
}
