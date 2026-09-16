<?php

namespace Database\Factories;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

class HospitalFactory extends Factory
{
    protected $model = Hospital::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Hospital',
            'code' => strtoupper(fake()->bothify('??-####')),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('+1-555-####'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'contact_person' => 'Dr. ' . fake()->lastName(),
            'designation' => fake()->randomElement(['Director', 'Chief Medical Officer', 'Administrator']),
            'total_beds' => fake()->numberBetween(50, 500),
            'description' => fake()->sentence(),
            'status' => 'active',
            'rating' => fake()->randomFloat(1, 3.0, 5.0),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }
}
