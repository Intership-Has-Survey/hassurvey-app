<?php

namespace Database\Seeders;

use App\Models\StatusPembayaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        StatusPembayaran::truncate();

        // Get first user as default
        $user = User::first();
        
        if (!$user) {
            $user = User::factory()->create();
        }

        // Sample status pembayaran data
        $statusPembayarans = [
            [
                'nama_pembayaran' => 'Pembayaran DP Project A',
                'jenis_pembayaran' => 'Down Payment',
                'nilai' => 5000000,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pembayaran' => 'Pembayaran Lunas Project B',
                'jenis_pembayaran' => 'Pelunasan',
                'nilai' => 15000000,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pembayaran' => 'Pembayaran Sewa Bulanan',
                'jenis_pembayaran' => 'Sewa',
                'nilai' => 2000000,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pembayaran' => 'Pembayaran Kalibrasi Alat',
                'jenis_pembayaran' => 'Kalibrasi',
                'nilai' => 750000,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pembayaran' => 'Pembayaran Penjualan Alat',
                'jenis_pembayaran' => 'Penjualan',
                'nilai' => 25000000,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data
        foreach ($statusPembayarans as $status) {
            StatusPembayaran::create($status);
        }

        $this->command->info('Status Pembayaran seeder created successfully!');
    }
}
