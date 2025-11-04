<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coopId' => fake()->unique()->numberBetween(1, 10000),
            'surname' => fake()->lastName(),
            'otherNames' => fake()->firstName(),
            'occupation' => fake()->jobTitle(),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'religion' => fake()->randomElement(['Christianity', 'Islam', 'Other']),
            'phoneNumber' => fake()->phoneNumber(),
            'bankName' => fake()->company(),
            'accountNumber' => fake()->bankAccountNumber(),
            'nextOfKinName' => fake()->name(),
            'nextOfKinPhoneNumber' => fake()->phoneNumber(),
            'yearJoined' => fake()->year(),
        ];
    }
}
