<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildFactory extends Factory
{
    protected $model = Child::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->parent(),
            'name' => fake()->firstName(),
            'date_of_birth' => fake()->dateTimeBetween('-5 years', '-1 year'),
            'gender' => fake()->randomElement(['male', 'female']),
            'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'relationship' => fake()->randomElement(['mother', 'father']),
            'allergies' => fake()->optional(0.3)->sentence(),
            'notes' => fake()->optional(0.2)->sentence(),
            'status' => 'active',
        ];
    }
}
