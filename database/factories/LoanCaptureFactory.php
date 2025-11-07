<?php

namespace Database\Factories;

use App\Models\LoanCapture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanCapture>
 */
class LoanCaptureFactory extends Factory
{
    protected $model = LoanCapture::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loanDate = fake()->dateTimeBetween('-2 years', 'now');
        $repaymentDate = (clone $loanDate)->modify('+540 days');

        return [
            'coopId' => fake()->numberBetween(1, 10000),
            'loanAmount' => fake()->numberBetween(10000, 100000),
            'loanDate' => $loanDate->format('Y-m-d'),
            'repaymentDate' => $repaymentDate->format('Y-m-d'),
            'guarantor1' => fake()->optional()->numberBetween(1, 10000),
            'guarantor2' => fake()->optional()->numberBetween(1, 10000),
            'guarantor3' => fake()->optional()->numberBetween(1, 10000),
            'guarantor4' => fake()->optional()->numberBetween(1, 10000),
            'status' => 1,
        ];
    }
}
