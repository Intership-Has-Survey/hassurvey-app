<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB; // <-- Jangan lupa import DB facade
use App\Models\User;

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
        $districtCode = substr($villageCode, 0, 7); // Ambil 7 digit pertama
        $cityCode = substr($villageCode, 0, 4); // Ambil 4 digit pertama
        $provinceCode = substr($villageCode, 0, 2); // Ambil 2 digit pertama

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
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
        ];
    }
}