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
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create();
        }

        Personel::create([
            'nama_personel' => 'Dzaky',
            'jenis_personel' => 'Surveyor',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);

        Personel::create([
            'nama_personel' => 'Sulthon',
            'jenis_personel' => 'Surveyor',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);

        Personel::create([
            'nama_personel' => 'Athallah',
            'jenis_personel' => 'Asisten',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);

        Personel::create([
            'nama_personel' => 'Rizki',
            'jenis_personel' => 'Asisten',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);

        Personel::create([
            'nama_personel' => 'Surya',
            'jenis_personel' => 'Asisten',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
            'user_id' => $user->id,
        ]);
    }
}
