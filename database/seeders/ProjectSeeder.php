<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Sales;
use App\Models\Customer;



class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil satu user secara acak dari database
        $user = User::inRandomOrder()->first();

        // Jika tidak ada user sama sekali di database, buat satu user baru
        // Logika ini tetap berguna jika seeder ini dijalankan pada database kosong
        if (!$user) {
            $user = User::factory()->create();
        }

        $customer = customer::inRandomOrder()->first();

        // Jika tidak ada customer sama sekali di database, buat satu customer baru
        // Logika ini tetap berguna jika seeder ini dijalankan pada database kosong
        if (!$customer) {
            $customer = customer::factory()->create();
        }

        // Ambil satu user secara acak dari database
        $sales = Sales::inRandomOrder()->first();

        // Jika tidak ada sales sama sekali di database, buat satu sales baru
        // Logika ini tetap berguna jika seeder ini dijalankan pada database kosong
        if (!$sales) {
            $sales = Sales::factory()->create();
        }

        // Ambil satu user secara acak dari database
        $kategori = Kategori::inRandomOrder()->first();

        // Jika tidak ada kategori sama sekali di database, buat satu kategori baru
        // Logika ini tetap berguna jika seeder ini dijalankan pada database kosong
        if (!$kategori) {
            $kategori = Kategori::factory()->create();
        }

        Project::create([
            'nama_project' => 'Proyek A',
            'kategori_id' => $kategori->id, // Ganti dengan ID kategori yang sesua
            'sales_id' => $sales->id, // Ganti dengan ID sales yang sesuai,
            'sumber' => 'Online', // Ganti dengan ID customer yang sesuai
            'customer_id' => $customer->id, // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'B2B',
            'lokasi' => 'Jakarta',
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'status' => 'Prospect',
            'nilai_project' => 100000000, // Ganti dengan nilai proyek yang sesuai
            'status_pembayaran' => 'Belum Lunas',
            'tanggal_informasi_masuk' => now(),
            'status_pekerjaan' => 'Belum Selesai',
            'user_id' => $user->id, // Ganti dengan ID user yang sesuai
        ]);
        Project::create([
            'nama_project' => 'Proyek B',
            'kategori_id' => $kategori->id, // Ganti dengan ID kategori yang sesuai
            'sales_id' => $sales->id, // Ganti dengan ID sales yang sesuai
            'sumber' => 'Offline', // Ganti dengan ID customer yang sesuai
            'customer_id' => $customer->id, // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'B2C',
            'lokasi' => 'Bandung',
            'alamat' => 'Jl. Merdeka No. 2, Bandung',
            'status' => 'Follow up',
            'nilai_project' => 200000000, // Ganti dengan nilai proyek yang sesuai
            'status_pembayaran' => 'Belum Lunas',
            'tanggal_informasi_masuk' => now(),
            'status_pekerjaan' => 'Belum Selesai',
            'user_id' => $user->id, // Ganti dengan ID user yang sesuai
        ]);
        Project::create([
            'nama_project' => 'Proyek C',
            'kategori_id' => $kategori->id, // Ganti dengan ID kategori yang sesuai
            'sales_id' => $sales->id, // Ganti dengan ID sales yang sesuai
            'sumber' => 'Online', // Ganti dengan ID customer yang sesuai                       
            'customer_id' => $customer->id, // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'B2B',
            'lokasi' => 'Surabaya',
            'alamat' => 'Jl. Merdeka No. 3, Surabaya',
            'status' => 'Closing',
            'nilai_project' => 300000000, // Ganti dengan nilai proyek yang
            'status_pembayaran' => 'Belum Lunas',
            'tanggal_informasi_masuk' => now(),
            'status_pekerjaan' => 'Belum Selesai',
            'user_id' => $user->id, // Ganti dengan ID user yang sesuai
        ]);
        Project::create([
            'nama_project' => 'Proyek D',
            'kategori_id' => $kategori->id, // Ganti dengan ID kategori yang sesuai
            'sales_id' => $sales->id, // Ganti dengan ID sales yang sesuai
            'sumber' => 'Offline', // Ganti dengan ID customer yang sesuai
            'customer_id' => $customer->id, // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'B2C',
            'lokasi' => 'Yogyakarta',
            'alamat' => 'Jl. Merdeka No. 4, Yogyakarta',
            'status' => 'Prospect',
            'nilai_project' => 150000000, // Ganti dengan nilai proyek yang sesuai
            'status_pembayaran' => 'Belum Lunas',
            'tanggal_informasi_masuk' => now(),
            'status_pekerjaan' => 'Belum Selesai',
            'user_id' => $user->id, // Ganti dengan ID user yang sesuai
        ]);
        Project::create([
            'nama_project' => 'Proyek E',
            'kategori_id' => $kategori->id, // Ganti dengan ID kategori yang sesuai
            'sales_id' => $sales->id, // Ganti dengan ID sales yang sesuai
            'sumber' => 'Online', // Ganti dengan ID customer yang sesuai
            'customer_id' => $customer->id, // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'B2B',
            'lokasi' => 'Medan',
            'alamat' => 'Jl. Merdeka No. 5, Medan',
            'status' => 'Follow up',
            'nilai_project' => 250000000, // Ganti dengan nilai proyek yang sesuai
            'status_pembayaran' => 'Belum Lunas',
            'tanggal_informasi_masuk' => now(),
            'status_pekerjaan' => 'Belum Selesai',
            'user_id' => $user->id, // Ganti dengan ID user yang sesuai
        ]);
    }
}
