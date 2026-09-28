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

if (! function_exists('star_row')) {
    /**
     * Render a row of stars for review ratings.
     */
    function star_row(float|int $rating, int $outOf = 5): string
    {
        $html = '<span class="stars" aria-label="'.e((string) $rating).' out of '.$outOf.'">';

        for ($i = 1; $i <= $outOf; $i++) {
            $html .= $i <= round($rating) ? '&#9733;' : '&#9734;';
        }

        return $html.'</span>';
    }
}
