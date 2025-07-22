<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JenisAlat>
 */
class JenisAlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['Total Station', 'Theodolite', 'GPS Geodetik RTK', 'Waterpass', 'Drone', 'GPS Handheld']),
            'keterangan' => $this->faker->sentence(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}