<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'bKash',
                'account_name' => 'Rideora Rentals Ltd.',
                'account_number' => '01711-000001',
                'instructions' => "1. Open bKash and choose 'Send Money'.\n"
                    ."2. Enter the account number above.\n"
                    ."3. Send the exact booking total.\n"
                    ."4. Copy the TrxID from the confirmation SMS and upload a screenshot of the receipt here.",
            ],
            [
                'name' => 'Nagad',
                'account_name' => 'Rideora Rentals Ltd.',
                'account_number' => '01811-000002',
                'instructions' => "1. Open Nagad and choose 'Send Money'.\n"
                    ."2. Enter the account number above.\n"
                    ."3. Send the exact booking total.\n"
                    ."4. Copy the transaction ID and upload the payment receipt screenshot.",
            ],
            [
                'name' => 'Bank Transfer',
                'account_name' => 'Rideora Rentals Ltd.',
                'account_number' => '1234 5678 9012 (City Bank, Banani Branch)',
                'instructions' => "1. Transfer the exact total to the account above.\n"
                    ."2. Use your booking code as the transfer reference.\n"
                    ."3. Upload the bank deposit slip or app screenshot showing the transaction reference.",
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                $method + ['status' => 'active']
            );
        }
    }
}
