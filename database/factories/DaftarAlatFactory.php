<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DaftarAlat>
 */
class DaftarAlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_seri' => $this->faker->unique()->ean8(),
            'jenis_alat_id' => \App\Models\JenisAlat::inRandomOrder()->first()->id ?? \App\Models\JenisAlat::factory()->create()->id,
            'merk_id' => \App\Models\Merk::inRandomOrder()->first()->id ?? \App\Models\Merk::factory()->create()->id,
            'kondisi' => true,
            'status' => true,
            'keterangan' => $this->faker->sentence(),
            'user_id' => \App\Models\User::inRandomOrder()->first()->id ?? \App\Models\User::factory()->create()->id,
            'pemilik_id' => \App\Models\Pemilik::inRandomOrder()->first()->id ?? \App\Models\Pemilik::factory()->create()->id,
        ];
    }
}