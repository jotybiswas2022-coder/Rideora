<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('rideora.admin.email');

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('rideora.admin.name'),
                'phone' => '+880 1700-000000',
                'city' => 'Dhaka',
                'address' => 'Banani, Dhaka',
                'password' => config('rideora.admin.password'),
                'is_admin' => true,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('Admin account ready: '.$admin->email);
        $this->command?->warn('Change RIDEORA_ADMIN_PASSWORD in your .env before deploying.');
    }
}
