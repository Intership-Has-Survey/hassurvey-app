<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345'),
        ]);
        User::create([
            'name' => 'Karel',
            'email' => 'karelriyan@gmail.com',
            'password' => bcrypt('12345'),
        ]);
        User::create([
            'name' => 'Diaz',
            'email' => 'diaz@gmail.com',
            'password' => bcrypt('12345'),
        ]);
        User::create([
            'name' => 'Syahrial',
            'email' => 'syahrial@gmail.com',
            'password' => bcrypt('12345'),
        ]);
        User::create([
            'name' => 'Syaiful',
            'email' => 'syaiful@gmail.com',
            'password' => bcrypt('12345'),
        ]);
    }
}
