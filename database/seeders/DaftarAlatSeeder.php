<?php

namespace Database\Seeders;

use App\Models\DaftarAlat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pemilik;

class DaftarAlatSeeder extends Seeder
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

        $pemilik = Pemilik::first();
        if (!$pemilik) {
            $pemilik = Pemilik::factory()->create();
        }

        // Menggunakan updateOrCreate untuk menghindari error duplikat
        // Argumen pertama: kunci untuk mencari data
        // Argumen kedua: data yang akan dibuat atau diperbarui
        DaftarAlat::updateOrCreate(
            ['nomor_seri' => 'GPS-001'],
            [
                'jenis_alat' => 'GPS',
                'merk' => 'Topcon',
                'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
                'user_id' => $user->id,
                'pemilik_id' => $pemilik->id,
            ]
        );

        DaftarAlat::updateOrCreate(
            ['nomor_seri' => 'DRN-001'],
            [
                'jenis_alat' => 'Drone',
                'merk' => 'Trimble',
                'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
                'user_id' => $user->id,
                'pemilik_id' => $pemilik->id,
            ]
        );

        DaftarAlat::updateOrCreate(
            ['nomor_seri' => 'DRN-002'],
            [
                'jenis_alat' => 'Drone',
                'merk' => 'Trimble',
                'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
                'user_id' => $user->id,
                'pemilik_id' => $pemilik->id,
            ]
        );

        DaftarAlat::updateOrCreate(
            ['nomor_seri' => 'GPS-002'],
            [
                'jenis_alat' => 'GPS',
                'merk' => 'Garmin',
                'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
                'user_id' => $user->id,
                'pemilik_id' => $pemilik->id,
            ]
        );

        DaftarAlat::updateOrCreate(
            ['nomor_seri' => 'GPS-003'],
            [
                'jenis_alat' => 'GPS',
                'merk' => 'Suunto',
                'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
                'user_id' => $user->id,
                'pemilik_id' => $pemilik->id,
            ]
        );
    }
}
