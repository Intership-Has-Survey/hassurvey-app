<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisAlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        $jenisAlat = [
            ['Total Station', 'Total Station adalah alat ukur sudut dan jarak elektronik yang terintegrasi.'],
            ['Theodolite', 'Theodolite adalah instrumen presisi untuk mengukur sudut di bidang horizontal dan vertikal.'],
            ['GPS Geodetik RTK', 'GPS Geodetik RTK (Real-Time Kinematic) adalah sistem penentuan posisi satelit yang memberikan akurasi sentimeter secara real-time.'],
            ['Waterpass', 'Waterpass atau level adalah alat untuk mengukur atau menentukan sebuah benda atau garis dalam posisi rata baik pengukuran secara vertikal maupun horizontal.'],
            ['Drone', 'Drone atau Pesawat Tanpa Awak (PETA) adalah sebuah mesin terbang yang berfungsi dengan kendali jarak jauh oleh pilot atau mampu mengendalikan dirinya sendiri.'],
            ['GPS Handheld', 'GPS Handheld adalah perangkat GPS portabel yang digunakan untuk navigasi dan pemetaan di lapangan.'],
        ];

        foreach ($jenisAlat as $alat) {
            \App\Models\JenisAlat::create([
                'nama' => $alat[0],
                'keterangan' => $alat[1],
                'user_id' => $user->id,
            ]);
        }
    }
}