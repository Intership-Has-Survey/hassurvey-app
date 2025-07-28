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

        Personel::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);
    }
}
