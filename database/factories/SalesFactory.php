<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;

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

        $randomVillage = DB::table('tref_regions')->inRandomOrder()->first();

        if (!$randomVillage) {
            throw new \Exception('Tabel wilayah (villages) kosong. Jalankan TrefRegionSeeder terlebih dahulu.');
        }

        $villageCode = $randomVillage->code;
        $districtCode = substr($villageCode, 0, 8);
        $cityCode = substr($villageCode, 0, 5);
        $provinceCode = substr($villageCode, 0, 2);

        return [
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'telepon' => $this->faker->phoneNumber(),
            'nik' => $this->faker->unique()->numerify('332708########000#'),
            'provinsi' => $provinceCode,
            'kota' => $cityCode,
            'kecamatan' => $districtCode,
            'desa' => $villageCode,
            'detail_alamat' => 'Jl. ' . $this->faker->streetName() . ' No. ' . $this->faker->buildingNumber(),
            'user_id' => $user->id,
        ];
    }
}
