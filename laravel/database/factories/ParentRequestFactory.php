<?php

namespace Database\Factories;

use App\Models\ParentRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParentRequestFactory extends Factory
{
    protected $model = ParentRequest::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['vaccination_request', 'general_inquiry', 'complaint', 'feedback']),
            'subject' => fake()->sentence(4),
            'details' => fake()->paragraph(),
            'hospital_id' => null,
            'status' => 'pending',
            'admin_response' => null,
            'responded_at' => null,
        ];
    }

    public function forParent($parent): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $parent->id ?? $parent,
        ]);
    }

    public function forHospital($hospital): static
    {
        return $this->state(fn (array $attributes) => [
            'hospital_id' => $hospital->id ?? $hospital,
        ]);
    }
}
