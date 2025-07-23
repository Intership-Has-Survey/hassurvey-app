<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merk>
 */
class MerkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['TOPCON', 'SOKKIA', 'TRIMBLE', 'Nikon', 'CHCNAV', 'SOUTH', 'GARMIN']),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}