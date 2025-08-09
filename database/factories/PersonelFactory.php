<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personel>
 */
class PersonelFactory extends Factory
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

        $tipePersonelOptions = ['internal', 'freelance'];
        $jabatanOptions = ['Surveyor', 'Asisten Surveyor', 'Drafter'];

        return [
            'nama' => $this->faker->name(),
            'tipe_personel' => $this->faker->randomElement($tipePersonelOptions),
            'nik' => $this->faker->unique()->numerify('332708########000#'),
            'jabatan' => $this->faker->randomElement($jabatanOptions),
            'nomor_wa' => $this->faker->phoneNumber(),
            'provinsi' => $provinceCode,
            'kota' => $cityCode,
            'kecamatan' => $districtCode,
            'desa' => $villageCode,
            'detail_alamat' => $this->faker->streetAddress(),
            'keterangan' => $this->faker->sentence(),
            'user_id' => $user->id,
        ];
    }
}
