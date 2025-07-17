<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kategori;
use App\Models\Sales;
use App\Models\User;
use App\Models\Perorangan;
use App\Models\Corporate;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // --- Logika untuk Relasi Polimorfik (Customer) ---
        // 1. Pilih secara acak tipe customer: Perorangan atau Corporate.
        $customerType = $this->faker->randomElement([
            Perorangan::class,
            Corporate::class
        ]);

        // 2. Ambil satu customer secara acak dari tipe yang terpilih.
        $customer = $customerType::inRandomOrder()->first();

        // Jika tabel customer (perorangan/corporate) masih kosong, buat satu.
        if (!$customer) {
            $customer = $customerType::factory()->create();
        }
        // --- Akhir Logika Polimorfik ---

        $randomVillage = DB::table('tref_regions')->inRandomOrder()->first();
        if (!$randomVillage) {
            throw new \Exception('Tabel wilayah (villages) kosong. Jalankan TrefRegionSeeder terlebih dahulu.');
        }
        $villageCode = $randomVillage->code;
        $districtCode = substr($villageCode, 0, 7);
        $cityCode = substr($villageCode, 0, 4);
        $provinceCode = substr($villageCode, 0, 2);

        return [
            'nama_project' => 'Proyek ' . $this->faker->bs(),

            // Mengambil ID dari tabel relasi secara acak.
            // Pastikan seeder untuk Kategori, Sales, dan User sudah dijalankan.
            'kategori_id' => Kategori::inRandomOrder()->first()->id ?? Kategori::factory(),
            'sales_id' => Sales::inRandomOrder()->first()->id ?? Sales::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),

            'tanggal_informasi_masuk' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'sumber' => $this->faker->randomElement(['online', 'offline']),
            'nilai_project' => $this->faker->numberBetween(5000000, 1000000000),

            'provinsi' => $provinceCode,
            'kota' => $cityCode,
            'kecamatan' => $districtCode,
            'desa' => $villageCode,
            'detail_alamat' => 'Lokasi di ' . $this->faker->streetAddress(),

            // Status-status yang mungkin terjadi
            'status' => $this->faker->randomElement(['Prospect', 'Follow up', 'Closing']),

            // Mengisi kolom polimorfik
            'customer_id' => $customer->id,
            'customer_type' => $customerType,
        ];
    }
}
