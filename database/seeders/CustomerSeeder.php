<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create();
        }

        Customer::create([
            'nama' => 'PT. Maju Mundur',
            'email' => 'maju@gmail.com',
            'telepon' => '085161648718',
            'province_code' => '31', // DKI Jakarta
            'regency_code' => '31.71', // Kota Jakarta Pusat
            'district_code' => '31.71.01', // Gambir
            'village_code' => '31.71.01.1001', // Gambir
            'detail_alamat' => 'Jl. Raya No. 1',
            'user_id' => $user->id,
        ]);
        Customer::create([
            'nama' => 'PT. Jaya Abadi',
            'email' => 'jaya@gmail.com',
            'telepon' => '085161648719',
            'province_code' => '32', // Jawa Barat
            'regency_code' => '32.73', // Kota Bandung
            'district_code' => '32.73.01', // Bandung Kulon
            'village_code' => '32.73.01.1001', // Cigondewah Kaler
            'detail_alamat' => 'Jl. Merdeka No. 2',
            'user_id' => $user->id,
        ]);
        Customer::create([
            'nama' => 'CV. Sukses Selalu',
            'email' => 'sukses@gmail.com',
            'telepon' => '085161648720',
            'province_code' => '35', // Jawa Timur
            'regency_code' => '35.78', // Kota Surabaya
            'district_code' => '35.78.01', // Asemrowo
            'village_code' => '35.78.01.1001', // Asemrowo
            'detail_alamat' => 'Jl. Kebangsaan No. 3',
            'user_id' => $user->id,
        ]);
        Customer::create([
            'nama' => 'PT. Bersama Kita',
            'email' => 'bersama@gmail.com',
            'telepon' => '085161648721',
            'province_code' => '34', // DI Yogyakarta
            'regency_code' => '34.71', // Kota Yogyakarta
            'district_code' => '34.71.01', // Danurejan
            'village_code' => '34.71.01.1001', // Bausasran
            'detail_alamat' => 'Jl. Kebangsaan No. 4',
            'user_id' => $user->id,
        ]);
        Customer::create([
            'nama' => 'PT. Sejahtera Bersama',
            'email' => 'sejahtera@gmail.com',
            'telepon' => '085161648722',
            'province_code' => '12', // Sumatera Utara
            'regency_code' => '12.71', // Kota Medan
            'district_code' => '12.71.01', // Medan Amplas
            'village_code' => '12.71.01.1001', // Amplasa
            'detail_alamat' => 'Jl. Kebangsaan No. 5',
            'user_id' => $user->id,
        ]);
    }
}
