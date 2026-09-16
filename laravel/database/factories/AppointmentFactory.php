<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Child;
use App\Models\Hospital;
use App\Models\User;
use App\Models\Vaccine;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $child = Child::factory()->create();
        $hospital = Hospital::factory()->create(['status' => 'active']);
        $vaccine = Vaccine::factory()->create();

        return [
            'child_id' => $child->id,
            'parent_id' => $child->user_id,
            'hospital_id' => $hospital->id,
            'vaccine_id' => $vaccine->id,
            'dose' => 'Dose 1',
            'appointment_date' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'appointment_time' => fake()->time('H:i:s'),
            'status' => 'pending',
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
