<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sales;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sales::create([
            'name' => 'Syahrial',
            'email' => 'syahrial@gmail.com',
            'telephone' => '085161648713',
        ]);

        Sales::create([
            'name' => 'Hipdi',
            'email' => 'hipdi@gmail.com',
            'telephone' => '085161648714',
        ]);

        Sales::create([
            'name' => 'Ahmad',
            'email' => 'ahmad@gmail.com',
            'telephone' => '085161648715',
        ]);

        Sales::create([
            'name' => 'Diaz',
            'email' => 'diaz@gmail.com',
            'telephone' => '085161648716',
        ]);

        Sales::create([
            'name' => 'Karel',
            'email' => 'karel@gmail.com',
            'telephone' => '085161648717',
        ]);
    }
}
