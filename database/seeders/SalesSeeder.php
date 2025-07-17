<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sales;
use App\Models\User;

class SalesSeeder extends Seeder
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
        Sales::create([
            'nama' => 'Syahrial',
            'email' => 'syahrial@gmail.com',
            'telepon' => '085161648713',
            'provinsi' => '33', // Jawa Tengah
            'kota' => '33.27', // Kab. Pemalang
            'kecamatan' => '33.27.08', // Kec. Pemalang
            'desa' => '33.27.08.2010', // Kel. Pelutan
            'detail_alamat' => 'Jl. Gatot Subroto No. 10',
            'user_id' => $user->id,
        ]);

        Sales::create([
            'nama' => 'Hipdi',
            'email' => 'hipdi@gmail.com',
            'telepon' => '085161648714',
            'provinsi' => '33', // Jawa Tengah
            'kota' => '33.27', // Kab. Pemalang
            'kecamatan' => '33.27.08', // Kec. Pemalang
            'desa' => '33.27.08.2010', // Kel. Pelutan
            'detail_alamat' => 'Jl. Gatot Subroto No. 10',
            'user_id' => $user->id,
        ]);

        Sales::create([
            'nama' => 'Ahmad',
            'email' => 'ahmad@gmail.com',
            'telepon' => '085161648715',
            'provinsi' => '33', // Jawa Tengah
            'kota' => '33.27', // Kab. Pemalang
            'kecamatan' => '33.27.08', // Kec. Pemalang
            'desa' => '33.27.08.2010', // Kel. Pelutan
            'detail_alamat' => 'Jl. Gatot Subroto No. 10',
            'user_id' => $user->id,
        ]);

        Sales::create([
            'nama' => 'Diaz',
            'email' => 'diaz@gmail.com',
            'telepon' => '085161648716',
            'provinsi' => '33', // Jawa Tengah
            'kota' => '33.27', // Kab. Pemalang
            'kecamatan' => '33.27.08', // Kec. Pemalang
            'desa' => '33.27.08.2010', // Kel. Pelutan
            'detail_alamat' => 'Jl. Gatot Subroto No. 10',
            'user_id' => $user->id,
        ]);

        Sales::create([
            'nama' => 'Karel',
            'email' => 'karel@gmail.com',
            'telepon' => '085161648717',
            'provinsi' => '33', // Jawa Tengah
            'kota' => '33.27', // Kab. Pemalang
            'kecamatan' => '33.27.08', // Kec. Pemalang
            'desa' => '33.27.08.2010', // Kel. Pelutan
            'detail_alamat' => 'Jl. Gatot Subroto No. 10',
            'user_id' => $user->id,
        ]);
    }
}
