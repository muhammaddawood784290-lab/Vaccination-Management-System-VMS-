<?php

namespace Database\Factories;

use App\Models\Vaccine;
use Illuminate\Database\Eloquent\Factories\Factory;

class VaccineFactory extends Factory
{
    protected $model = Vaccine::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' Vaccine',
            'code' => strtoupper(fake()->bothify('??-####')),
            'doses' => fake()->numberBetween(1, 5),
            'age_range' => fake()->randomElement(['At birth', '6 weeks', '9 months', '2 months', '9-14 years']),
            'type' => fake()->randomElement(['Live attenuated', 'Inactivated', 'Recombinant', 'Conjugate', 'Toxoid']),
            'manufacturer' => fake()->company(),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
