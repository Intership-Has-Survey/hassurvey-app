<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pemilik>
 */
class PemilikFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'NIK' => $this->faker->unique()->numerify('################'), // 16 digits
            'email' => $this->faker->unique()->safeEmail(),
            'telepon' => $this->faker->phoneNumber(),
            'provinsi' => function () {
                return \App\Models\TrefRegion::whereRaw('LENGTH(code) = 2')->inRandomOrder()->first()->code ?? null;
            },
            'kota' => function (array $attributes) {
                return \App\Models\TrefRegion::whereRaw('LENGTH(code) = 5')
                    ->where('code', 'like', $attributes['provinsi'] . '%')
                    ->inRandomOrder()
                    ->first()->code ?? null;
            },
            'kecamatan' => function (array $attributes) {
                return \App\Models\TrefRegion::whereRaw('LENGTH(code) = 8')
                    ->where('code', 'like', $attributes['kota'] . '%')
                    ->inRandomOrder()
                    ->first()->code ?? null;
            },
            'desa' => function (array $attributes) {
                return \App\Models\TrefRegion::whereRaw('LENGTH(code) = 13')
                    ->where('code', 'like', $attributes['kecamatan'] . '%')
                    ->inRandomOrder()
                    ->first()->code ?? null;
            },
            'detail_alamat' => $this->faker->address(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}