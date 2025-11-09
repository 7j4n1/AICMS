<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SavingsType;

class SavingsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $savingsTypes = [
            [
                'name' => 'Regular Savings',
                'description' => 'Standard monthly savings contribution',
                'active' => true,
                'minimum_amount' => null,
                'maximum_amount' => null,
            ],
            [
                'name' => 'Special Savings',
                'description' => 'Additional voluntary savings',
                'active' => true,
                'minimum_amount' => null,
                'maximum_amount' => null,
            ],
            [
                'name' => 'Target Savings',
                'description' => 'Goal-oriented savings plan',
                'active' => true,
                'minimum_amount' => 1000,
                'maximum_amount' => null,
            ],
            [
                'name' => 'Emergency Savings',
                'description' => 'Emergency fund contributions',
                'active' => true,
                'minimum_amount' => 500,
                'maximum_amount' => null,
            ],
        ];

        foreach ($savingsTypes as $type) {
            SavingsType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}

