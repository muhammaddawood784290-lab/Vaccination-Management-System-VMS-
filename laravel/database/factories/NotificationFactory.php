<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'type' => 'info',
            'data' => [],
            'read_at' => null,
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => null]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => now()]);
    }

    public function type(string $type): static
    {
        return $this->state(fn (array $attributes) => ['type' => $type]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => ['user_id' => $user->id]);
    }
}
