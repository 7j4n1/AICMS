<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoanEligibilitySetting;

class LoanEligibilitySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'formula_key' => 'default',
                'name' => 'Default Formula',
                'description' => 'Total savings plus shares multiplied by 2',
                'formula' => '(savings + shares) * 2',
                'active' => true,
            ],
            [
                'formula_key' => 'savings_only',
                'name' => 'Savings Only',
                'description' => 'Total savings multiplied by 2',
                'formula' => 'savings * 2',
                'active' => false,
            ],
            [
                'formula_key' => 'conservative',
                'name' => 'Conservative',
                'description' => 'Total savings plus shares multiplied by 1.5',
                'formula' => '(savings + shares) * 1.5',
                'active' => false,
            ],
            [
                'formula_key' => 'aggressive',
                'name' => 'Aggressive',
                'description' => 'Total savings plus shares multiplied by 3',
                'formula' => '(savings + shares) * 3',
                'active' => false,
            ],
        ];

        foreach ($settings as $setting) {
            LoanEligibilitySetting::updateOrCreate(
                ['formula_key' => $setting['formula_key']],
                $setting
            );
        }
    }
}

