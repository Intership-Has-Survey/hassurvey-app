<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        $merks = ['TOPCON', 'SOKKIA', 'TRIMBLE', 'Nikon', 'CHCNAV', 'SOUTH', 'GARMIN'];

        foreach ($merks as $merk) {
            \App\Models\Merk::create([
                'nama' => $merk,
                'user_id' => $user->id,
            ]);
        }
    }
}