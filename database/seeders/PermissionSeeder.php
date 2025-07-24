<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat daftar permissions
        $permissions = [
            'kelola layanan sewa',
            'kelola layanan pemetaan',
            'kelola akun pengguna',
            'kelola pengajuan dana',
            'kelola daftar alat',
            'kelola sales',
            'kelola personel',
            'kelola hak akses',
            'kelola jabatan',
            'kelola pembayaran',
            'kelola transaksi',
            'kelola customer',
            'kelola tingkatan pengajuan',
            'kelola investor',
            'kelola log activity',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Contoh: assign permissions ke role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        $adminRole->givePermissionTo(Permission::all()); // Admin punya semua
    }
}
