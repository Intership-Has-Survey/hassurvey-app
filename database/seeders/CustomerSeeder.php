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
            'nama_pic' => 'PT. Maju Mundur',
            'email' => 'maju@gmail.com',
            'telepon' => '085161648718',
            'alamat' => 'Jl. Raya No. 1, Jakarta',
            'user_id' => $user->id,
        ]);
        Customer::create([
            'nama_pic' => 'PT. Jaya Abadi',
            'email' => 'jaya@gmail.com',
            'telepon' => '085161648719',
            'alamat' => 'Jl. Merdeka No. 2, Bandung',
            'user_id' => $user->id,
        ]);
        Customer::create([
            'nama_pic' => 'CV. Sukses Selalu',
            'email' => 'sukses@gmail.com',
            'telepon' => '085161648720',
            'alamat' => 'Jl. Kebangsaan No. 3, Surabaya',
            'user_id' => $user->id,
        ]);
    }
}
