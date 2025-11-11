<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemConfiguration;

class SystemConfigurationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'key' => 'app_name',
                'value' => 'AICMS',
                'type' => 'string',
                'description' => 'Application name',
            ],
            [
                'key' => 'cooperative_name',
                'value' => 'Cooperative Society',
                'type' => 'string',
                'description' => 'Cooperative full name',
            ],
            [
                'key' => 'cooperative_initials',
                'value' => 'CS',
                'type' => 'string',
                'description' => 'Cooperative initials/abbreviation',
            ],
            [
                'key' => 'admin_charge',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Monthly admin charge for every payment capture',
            ],
            [
                'key' => 'cooperative_account_name',
                'value' => '',
                'type' => 'string',
                'description' => 'Cooperative bank account name',
            ],
            [
                'key' => 'cooperative_account_number',
                'value' => '',
                'type' => 'string',
                'description' => 'Cooperative bank account number',
            ],
            [
                'key' => 'cooperative_bank_name',
                'value' => '',
                'type' => 'string',
                'description' => 'Cooperative bank name',
            ],
            [
                'key' => 'payment_approval_required',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Whether payment notifications require admin approval',
            ],
            [
                'key' => 'loan_approval_required',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Whether loan captures require admin approval',
            ],
        ];

        foreach ($configurations as $config) {
            SystemConfiguration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}

