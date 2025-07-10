<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Personel;

class PersonelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Personel::create([
            'nama' => 'Dzaky',
            'jenis_personel' => 'Surveyor',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
        ]);

        Personel::create([
            'nama' => 'Sulthon',
            'jenis_personel' => 'Surveyor',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
        ]);

        Personel::create([
            'nama' => 'Athallah',
            'jenis_personel' => 'Asisten',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
        ]);

        Personel::create([
            'nama' => 'Rizki',
            'jenis_personel' => 'Asisten',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
        ]);

        Personel::create([
            'nama' => 'Surya',
            'jenis_personel' => 'Asisten',
            'keterangan' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, nostrum!',
        ]);
    }
}
