<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB; // <-- Jangan lupa import DB facade
use App\Models\User;
use App\Models\Company;

class PeroranganFactory extends Factory
{
    public function definition(): array
    {
        // 💡 1. Ambil satu data desa secara acak dari tabel 'villages'.
        // Pastikan nama tabel 'villages' sudah benar.
        $randomVillage = DB::table('tref_regions')->inRandomOrder()->first();

        // Jika data wilayah kosong, hentikan proses untuk menghindari error.
        if (!$randomVillage) {
            throw new \Exception('Tabel wilayah (villages) kosong. Jalankan TrefRegionSeeder terlebih dahulu.');
        }

        // 💡 2. Pecah kodenya untuk mendapatkan ID induk berdasarkan panjang karakter.
        $villageCode = $randomVillage->code;
        $districtCode = substr($villageCode, 0, 8);
        $cityCode = substr($villageCode, 0, 5);
        $provinceCode = substr($villageCode, 0, 2);

        $company = Company::inRandomOrder()->first();
        if (!$company) {
            throw new \Exception('No companies found in the database. Please run CompanySeeder first.');
        }

        return [
            'nama' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['Pria', 'Wanita']),
            'email' => $this->faker->unique()->safeEmail(),
            'telepon' => $this->faker->unique()->phoneNumber(),

            // ⚙️ Gunakan kode yang sudah kita ekstrak
            'provinsi' => $provinceCode,
            'kota' => $cityCode,
            'kecamatan' => $districtCode,
            'desa' => $villageCode,
            'detail_alamat' => 'Jl. ' . $this->faker->streetName() . ' No. ' . $this->faker->buildingNumber(),

            'nik' => $this->faker->unique()->numerify('################'),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'company_id' => $company->id,
            'created_at' => $this->faker->dateTimeBetween('2023-01-01', '2025-12-31'),
            'updated_at' => $this->faker->dateTimeBetween('2023-01-01', '2025-12-31'),
        ];
    }
}
