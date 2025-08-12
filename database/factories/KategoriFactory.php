<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kategori>
 */
class KategoriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get or create a valid user
        $user = User::first() ?? User::factory()->create();
        
        return [
            'nama' => $this->faker->word(),
            'keterangan' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
            'user_id' => $user->id,
        ];
    }
}
