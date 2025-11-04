<?php

namespace Database\Factories;

use App\Models\PaymentCapture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentCapture>
 */
class PaymentCaptureFactory extends Factory
{
    protected $model = PaymentCapture::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loanAmount = fake()->numberBetween(0, 10000);
        $savingAmount = fake()->numberBetween(0, 5000);
        $shareAmount = fake()->numberBetween(0, 3000);
        $others = fake()->numberBetween(0, 1000);
        $adminCharge = fake()->numberBetween(0, 500);

        return [
            'coopId' => fake()->numberBetween(1, 10000),
            'loanAmount' => $loanAmount,
            'savingAmount' => $savingAmount,
            'shareAmount' => $shareAmount,
            'others' => $others,
            'adminCharge' => $adminCharge,
            'totalAmount' => $loanAmount + $savingAmount + $shareAmount + $others + $adminCharge,
            'paymentDate' => fake()->date(),
            'splitOption' => fake()->optional()->word(),
            'otherSavingsType' => fake()->optional()->word(),
        ];
    }
}
