<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'paystack',
                'enabled' => false,
                'public_key' => null,
                'secret_key' => null,
                'merchant_email' => null,
                'additional_config' => null,
            ],
            [
                'name' => 'flutterwave',
                'enabled' => false,
                'public_key' => null,
                'secret_key' => null,
                'merchant_email' => null,
                'additional_config' => null,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['name' => $gateway['name']],
                $gateway
            );
        }
    }
}

