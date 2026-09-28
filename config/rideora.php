<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default administrator account
    |--------------------------------------------------------------------------
    |
    | Credentials are read from the environment so no real password is ever
    | committed. The fallback values only exist for local development.
    |
    */

    'admin' => [
        'name' => env('RIDEORA_ADMIN_NAME', 'Rideora Admin'),
        'email' => env('RIDEORA_ADMIN_EMAIL', 'admin@rideora.test'),
        'password' => env('RIDEORA_ADMIN_PASSWORD', 'admin12345'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo data
    |--------------------------------------------------------------------------
    |
    | The demo seeder creates sample customers, bookings and payments so the
    | admin dashboard is not empty on a fresh install. Turn it off in
    | production by setting RIDEORA_SEED_DEMO=false.
    |
    */

    'seed_demo_data' => env('RIDEORA_SEED_DEMO', true),

    'demo_customer_password' => env('RIDEORA_DEMO_PASSWORD', 'password123'),

];
