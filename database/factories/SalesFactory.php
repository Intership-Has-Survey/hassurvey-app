<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sales>
 */
class SalesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $provinceCode = '33'; // Jawa Tengah
        $cityCode = '33.27'; // Kab. Pemalang
        $districtCode = '33.27.08'; // Kec. Pemalang
        $villageCode = '33.27.08.2010'; // Kel. Pelutan

        return [
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'telepon' => $this->faker->phoneNumber(),
            'nik' => $this->faker->unique()->numerify('332708########000#'),
            'provinsi' => $provinceCode,
            'kota' => $cityCode,
            'kecamatan' => $districtCode,
            'desa' => $villageCode,
            'detail_alamat' => $this->faker->streetAddress(),
            'user_id' => $user->id,
        ];
    }
}
