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
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create();
        }

        // 1. Ambil SEMUA ID user dari database, HANYA SEKALI.
        // pluck() sangat efisien karena hanya mengambil kolom 'id'.
        $customerIds = Customer::pluck('id');

        // Jika tidak ada Customer sama sekali, buat satu sebagai fallback.
        if ($customerIds->isEmpty()) {
            // Sebaiknya jalankan customerSeeder dulu, tapi ini untuk pengaman.
            $customerIds->push(Customer::factory()->create()->id);
        }
        // 1. Ambil SEMUA ID user dari database, HANYA SEKALI.
        // pluck() sangat efisien karena hanya mengambil kolom 'id'.
        $salesIds = sales::pluck('id');

        // Jika tidak ada sales sama sekali, buat satu sebagai fallback.
        if ($salesIds->isEmpty()) {
            // Sebaiknya jalankan salesSeeder dulu, tapi ini untuk pengaman.
            $salesIds->push(sales::factory()->create()->id);
        }

        // 1. Ambil SEMUA ID user dari database, HANYA SEKALI.
        // pluck() sangat efisien karena hanya mengambil kolom 'id'.
        $kategoriIds = kategori::pluck('id');

        // Jika tidak ada kategori sama sekali, buat satu sebagai fallback.
        if ($kategoriIds->isEmpty()) {
            // Sebaiknya jalankan kategoriSeeder dulu, tapi ini untuk pengaman.
            $kategoriIds->push(kategori::factory()->create()->id);
        }

        Project::create([
            'nama_project' => 'Proyek A',
            'kategori_id' => $kategoriIds->random(), // Ganti dengan ID kategori yang sesua
            'sales_id' => $salesIds->random(), // Ganti dengan ID sales yang sesuai,
            'sumber' => 'Online', // Ganti dengan ID customer yang sesuai
            'customer_id' => $customerIds->random(), // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'Corporate',
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
            'kategori_id' => $kategoriIds->random(), // Ganti dengan ID kategori yang sesuai
            'sales_id' => $salesIds->random(), // Ganti dengan ID sales yang sesuai
            'sumber' => 'Offline', // Ganti dengan ID customer yang sesuai
            'customer_id' => $customerIds->random(), // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'Perseorangan',
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
            'kategori_id' => $kategoriIds->random(), // Ganti dengan ID kategori yang sesuai
            'sales_id' => $salesIds->random(), // Ganti dengan ID sales yang sesuai
            'sumber' => 'Online', // Ganti dengan ID customer yang sesuai                       
            'customer_id' => $customerIds->random(), // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'Corporate',
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
            'kategori_id' => $kategoriIds->random(), // Ganti dengan ID kategori yang sesuai
            'sales_id' => $salesIds->random(), // Ganti dengan ID sales yang sesuai
            'sumber' => 'Offline', // Ganti dengan ID customer yang sesuai
            'customer_id' => $customerIds->random(), // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'Perseorangan',
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
            'kategori_id' => $kategoriIds->random(), // Ganti dengan ID kategori yang sesuai
            'sales_id' => $salesIds->random(), // Ganti dengan ID sales yang sesuai
            'sumber' => 'Online', // Ganti dengan ID customer yang sesuai
            'customer_id' => $customerIds->random(), // Ganti dengan ID customer yang sesuai
            'jenis_penjualan' => 'Corporate',
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
