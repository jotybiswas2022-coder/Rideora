<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a site setting stored by admins.
     */
    function setting(string $key, ?string $default = null): ?string
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('bdt')) {
    /**
     * Format an amount in Bangladeshi Taka, e.g. ৳12,500.00
     */
    function bdt(float|int|string|null $amount, bool $decimals = false): string
    {
        $symbol = setting('currency_symbol', '৳');

        return $symbol.number_format((float) $amount, $decimals ? 2 : 0);
    }
}

if (! function_exists('status_badge')) {
    /**
     * Render a status pill. The class comes from the model helpers.
     */
    function status_badge(?string $label, string $class = 'badge-muted'): string
    {
        return '<span class="badge '.$class.'">'.e($label).'</span>';
    }
}

if (! function_exists('bi_icon')) {
    /**
     * Render a Bootstrap Icons glyph from its name, e.g. bi_icon('car-front').
     */
    function bi_icon(string $name, string $class = ''): string
    {
        $safe = preg_replace('/[^a-z0-9-]/', '', strtolower(trim(str_replace('bi-', '', $name))));

        if ($safe === null || $safe === '') {
            $safe = 'circle';
        }

        return '<i class="bi bi-'.$safe.($class !== '' ? ' '.$class : '').'"></i>';
    }
}

if (! function_exists('category_icon')) {
    /**
     * Render a category icon. Accepts a Bootstrap Icons name and still understands
     * the HTML entities written by older records.
     */
    function category_icon(?string $icon, string $fallback = 'car-front'): string
    {
        $legacy = [
            '&#128663;' => 'car-front',
            '&#128664;' => 'car-front-fill',
            '&#128665;' => 'grid',
            '&#128666;' => 'truck-front',
            '&#128667;' => 'truck',
            '&#128652;' => 'bus-front',
            '&#127949;' => 'scooter',
            '&#128142;' => 'gem',
        ];

        $icon = $icon === null || trim($icon) === '' ? $fallback : $icon;

        return bi_icon($legacy[$icon] ?? $icon);
    }
}

if (! function_exists('star_row')) {
    /**
     * Render a row of stars for review ratings.
     */
    function star_row(float|int $rating, int $outOf = 5): string
    {
        $html = '<span class="stars" aria-label="'.e((string) $rating).' out of '.$outOf.'">';

        for ($i = 1; $i <= $outOf; $i++) {
            $html .= $i <= round($rating) ? bi_icon('star-fill') : bi_icon('star');
        }

        return $html.'</span>';
    }
}
