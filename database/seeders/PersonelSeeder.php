<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Personel;
use App\Models\User;

class PersonelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama, atau buat jika tidak ada
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Data personel yang akan di-seed
        $personels = [
            [
                'nama' => 'Dzaky',
                'tipe_personel' => 'internal',
                'nik' => '3327081112990001',
                'jabatan' => 'Surveyor',
                'nomor_wa' => '081234567890',
                'tanggal_lahir' => '1999-12-11',
                'provinsi' => '33', // Jawa Tengah
                'kota' => '33.27', // Kab. Pemalang
                'kecamatan' => '33.27.08', // Kec. Pemalang
                'desa' => '33.27.08.2010', // Kel. Pelutan
                'detail_alamat' => 'Jl. Gatot Subroto No. 10',
                'keterangan' => 'Spesialis pemetaan topografi.',
                'user_id' => $user->id,
            ],
            [
                'nama' => 'Sulthon',
                'tipe_personel' => 'internal',
                'nik' => '3327081201000002',
                'jabatan' => 'Surveyor',
                'nomor_wa' => '081234567891',
                'tanggal_lahir' => '2000-01-12',
                'provinsi' => '33',
                'kota' => '33.27',
                'kecamatan' => '33.27.08',
                'desa' => '33.27.08.2011',
                'detail_alamat' => 'Jl. Jend. Sudirman No. 15',
                'keterangan' => 'Berpengalaman dalam survei batimetri.',
                'user_id' => $user->id,
            ],
            [
                'nama' => 'Athallah',
                'tipe_personel' => 'internal',
                'nik' => '3327080505010003',
                'jabatan' => 'Asisten Surveyor',
                'nomor_wa' => '081234567892',
                'tanggal_lahir' => '2001-05-05',
                'provinsi' => '33',
                'kota' => '33.27',
                'kecamatan' => '33.27.07', // Kec. Taman
                'desa' => '33.27.07.2001',
                'detail_alamat' => 'Perumahan Taman Asri Blok C1 No. 5',
                'keterangan' => 'Cekatan dan teliti dalam membantu di lapangan.',
                'user_id' => $user->id,
            ],
            [
                'nama' => 'Rizki',
                'tipe_personel' => 'freelance',
                'nik' => '3327082008020004',
                'jabatan' => 'Asisten Surveyor',
                'nomor_wa' => '081234567893',
                'tanggal_lahir' => '2002-08-20',
                'provinsi' => '33',
                'kota' => '33.27',
                'kecamatan' => '33.27.08',
                'desa' => '33.27.08.2009',
                'detail_alamat' => 'Jl. Pemuda No. 22',
                'keterangan' => 'Lorem ipsum dolor sit amet.',
                'user_id' => $user->id,
            ],
            [
                'nama' => 'Surya',
                'tipe_personel' => 'freelance',
                'nik' => '3327081503980005',
                'jabatan' => 'Drafter',
                'nomor_wa' => '081234567894',
                'tanggal_lahir' => '1998-03-15',
                'provinsi' => '33',
                'kota' => '33.27',
                'kecamatan' => '33.27.08',
                'desa' => '33.27.08.2018',
                'detail_alamat' => 'Jl. A. Yani No. 101',
                'keterangan' => 'Ahli dalam pengolahan data dan gambar.',
                'user_id' => $user->id,
            ],
        ];

        // Looping untuk membuat data personel
        foreach ($personels as $personelData) {
            Personel::create($personelData);
        }
    }
}
