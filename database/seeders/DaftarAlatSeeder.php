<?php

namespace Database\Seeders;

use App\Models\DaftarAlat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

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

        DaftarAlat::create([
            'nama_alat' => 'Total Station',
            'jenis_alat' => 'GPS',
            'merk' => 'Topcon',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);
        DaftarAlat::create([
            'nama_alat' => 'Jetpack',
            'jenis_alat' => 'Drone',
            'merk' => 'Trimble',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);
        DaftarAlat::create([
            'nama_alat' => 'Busan',
            'jenis_alat' => 'Drone',
            'merk' => 'Trimble',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);
        DaftarAlat::create([
            'nama_alat' => 'GPS',
            'jenis_alat' => 'GPS',
            'merk' => 'Garmin',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);
        DaftarAlat::create([
            'nama_alat' => 'Kompas',
            'jenis_alat' => 'GPS',
            'merk' => 'Suunto',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);
    }
}
