<?php

namespace Database\Factories;

use App\Models\CampusEmailVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CampusEmailVerification>
 */
class CampusEmailVerificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'email' => Str::lower(Str::random(12)).'@student.unsri.ac.id',
            'status' => 'pending',
            'verified_at' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => [
            'status' => 'verified',
            'verified_at' => now(),
        ]);
    }
}
