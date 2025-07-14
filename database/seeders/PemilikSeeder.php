<?php

namespace Database\Seeders;

use App\Models\Pemilik;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class PemilikSeeder extends Seeder
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

        pemilik::create([
            'nama' => 'Tung tung tung tung tung tung sahoorr',
            'NIK' => '3300000000000000',
            'email' => 'soekarno@gmail.com',
            'telepon' => '085161648718',
            'alamat' => 'Jl. Raya No. 1, Jakarta',
            'user_id' => $user->id,
        ]);

        pemilik::create([
            'nama' => 'tralalero tralala',
            'NIK' => '3300000000000001',
            'email' => 'tralala@gmail.com',
            'telepon' => '085161648718',
            'alamat' => 'Jl. Raya No. 1, Banh',
            'user_id' => $user->id,
        ]);

        pemilik::create([
            'nama' => 'simpanzini bananini',
            'NIK' => '3300000000000002',
            'email' => 'Imo@gmail.com',
            'telepon' => '085161648718',
            'alamat' => 'Jl. Teri No. 34, Jakarta',
            'user_id' => $user->id,
        ]);

        pemilik::create([
            'nama' => 'Bombardiro Crocodillo',
            'NIK' => '3300000000000005',
            'email' => 'soetarni@gmail.com',
            'telepon' => '085161648718',
            'alamat' => 'Jl. Raya No. 1, Jakarta',
            'user_id' => $user->id,
        ]);

        pemilik::create([
            'nama' => 'Brbr Patapim',
            'NIK' => '3300000000000009',
            'email' => 'brbr@gmail.com',
            'telepon' => '085161648718',
            'alamat' => 'Jl. Raya No. 1, Jakarta',
            'user_id' => $user->id,
        ]);



    }
}
