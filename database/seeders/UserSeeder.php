<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles
        $superadmin    = Role::firstOrCreate(['name' => 'Super Admin']);
        $financeRole  = Role::firstOrCreate(['name' => 'Keuangan']);
        $directorRole = Role::firstOrCreate(['name' => 'Direktur Utama']);
        $operasionalRole = Role::firstOrCreate(['name' => 'Operasional']);

        // Buat users & assign role
        $admin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('12345')]
        );
        $admin->assignRole($superadmin);

        // $karel = User::firstOrCreate(
        //     ['email' => 'karelriyan@gmail.com'],
        //     ['name' => 'Karel Riyan', 'password' => Hash::make('12345')]
        // );
        // $karel->assignRole($operasionalRole);

        // $hipdi = User::firstOrCreate(
        //     ['email' => 'syahrial@gmail.com'],
        //     ['name' => 'Syahrial Hipdi', 'password' => Hash::make('12345')]
        // );
        // $hipdi->assignRole($operasionalRole);

        // $diaz = User::firstOrCreate(
        //     ['email' => 'diaz@gmail.com'],
        //     ['name' => 'Diaz', 'password' => Hash::make('12345')]
        // );
        // $diaz->assignRole($financeRole);

        // $dirops = User::firstOrCreate(
        //     ['email' => 'dirops@gmail.com'],
        //     ['name' => 'Direktur Operasional', 'password' => Hash::make('12345')]
        // );
        // $dirops->assignRole($directorRole);

        // $keuangan = User::firstOrCreate(
        //     ['email' => 'keuangan@gmail.com'],
        //     ['name' => 'Keuangan', 'password' => Hash::make('12345')]
        // );
        // $keuangan->assignRole($financeRole);

        // $direktur = User::firstOrCreate(
        //     ['email' => 'direktur@gmail.com'],
        //     ['name' => 'Direktur Utama', 'password' => Hash::make('12345')]
        // );
        // $direktur->assignRole($directorRole);
    }
}
