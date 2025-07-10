<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Customer::create([
            'nama' => 'Ronaldo',
            'email' => 'ronaldo@gmail.com',
            'telephone' => '085161648717',
            'alamat' => 'Jl. Cihideung Ilir',
        ]);

        Customer::create([
            'nama' => 'Messi',
            'email' => 'messi@gmail.com',
            'telephone' => '085161648717',
            'alamat' => 'Jl. Cihideung Ilir',
        ]);

        Customer::create([
            'nama' => 'Aguero',
            'email' => 'aguero@gmail.com',
            'telephone' => '085161648717',
            'alamat' => 'Jl. Cihideung udik',
        ]);

        Customer::create([
            'nama' => 'Neymar',
            'email' => 'neymar@gmail.com',
            'telephone' => '085161648717',
            'alamat' => 'Jl. Cihideung udik',
        ]);
    }
}
