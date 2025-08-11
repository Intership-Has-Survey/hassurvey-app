<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Corporate; // <-- Import model Corporate
use App\Models\User; // <-- Import model Corporate
use Illuminate\Support\Str;

class CorporateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::first();

        Corporate::create([
            'id' => Str::uuid(),
            'nama' => 'CV HAS Survey',
            'nib' => '8123456789012',
            'level' => 'besar',
            'email' => 'hassurvey@gmail.com',
            'telepon' => '081234567890',
            'provinsi' => '31',
            'kota' => '31.01',
            'kecamatan' => '31.01.01',
            'desa' => '31.01.01.2001',
            'detail_alamat' => 'Jl. Teknologi No. 88, Jakarta Selatan',
            'created_at' => now(),
            'updated_at' => now(),
            'user_id' => $user->id,
        ]);
        // Membuat 25 data corporate dummy menggunakan factory.
        // Anda bisa ubah jumlahnya sesuai kebutuhan.
        Corporate::factory()->count(6)->create();
    }
}
