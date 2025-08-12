<?php

namespace Database\Seeders;

use App\Models\PengajuanDana;
use App\Models\User;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Project;
use App\Models\Sewa;
use App\Models\Penjualan;
use App\Models\Kalibrasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengajuanDanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate related tables in correct order
        DB::table('detail_pengajuans')->truncate();
        PengajuanDana::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get required data
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Get the first company associated with the user
        $company = $user->companies()->first();
        if (!$company) {
            $company = \App\Models\Company::first();
            if (!$company) {
                $company = \App\Models\Company::factory()->create();
            }
            // Associate the company with the user
            $user->companies()->attach($company->id);
        }

        // Create sample banks and bank accounts if not exists
        $bank = Bank::first();
        if (!$bank) {
            $bank = Bank::create([
                'name' => 'Bank Mandiri',
                'code' => 'BMRI',
                'status' => 'active',
            ]);
        }

        $bankAccount = BankAccount::first();
        if (!$bankAccount) {
            $bankAccount = BankAccount::create([
                'bank_id' => $bank->id,
                'no_rek' => '1234567890',
                'nama_pemilik' => 'PT Sample Company',
            ]);
        }

        // Create sample projects, sewa, penjualan, kalibrasi if not exists
        $project = Project::first();
        if (!$project) {
            $project = Project::factory()->create();
        }

        $sewa = Sewa::first();
        if (!$sewa) {
            $sewa = Sewa::factory()->create();
        }

        $penjualan = Penjualan::first();
        if (!$penjualan) {
            $penjualan = Penjualan::factory()->create();
        }

        $kalibrasi = Kalibrasi::first();
        if (!$kalibrasi) {
            $kalibrasi = Kalibrasi::factory()->create();
        }

        // Sample pengajuan dana data
        $pengajuanDanas = [
    [
        'judul_pengajuan' => 'Pengajuan Dana Project Website Development',
        'deskripsi_pengajuan' => 'Pengajuan dana untuk pembangunan website e-commerce baru',
        'bank_id' => $bank->id,
        'bank_account_id' => $bankAccount->id,
        'nilai' => 15000000,
        'dalam_review' => 'manager',
        'user_id' => $user->id,
        'level_id' => null,
        'disetujui' => null,
        'ditolak' => null,
        'alasan' => null,
        'project_id' => $project->id,
        'sewa_id' => null,
        'penjualan_id' => null,
        'kalibrasi_id' => null,
        'company_id' => $company->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'judul_pengajuan' => 'Pengajuan Dana Sewa Gedung Kantor',
        'deskripsi_pengajuan' => 'Pengajuan dana untuk pembayaran sewa gedung kantor periode Januari-Maret 2024',
        'bank_id' => $bank->id,
        'bank_account_id' => $bankAccount->id,
        'nilai' => 7500000,
        'dalam_review' => 'approved',
        'user_id' => $user->id,
        'level_id' => null,
        'disetujui' => 'Finance Manager',
        'ditolak' => null,
        'alasan' => null,
        'project_id' => null,
        'sewa_id' => $sewa->id,
        'penjualan_id' => null,
        'kalibrasi_id' => null,
        'company_id' => $company->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'judul_pengajuan' => 'Pengajuan Dana Kalibrasi Alat Laboratorium',
        'deskripsi_pengajuan' => 'Pengajuan dana untuk kalibrasi alat-alat laboratorium sesuai jadwal maintenance',
        'bank_id' => $bank->id,
        'bank_account_id' => $bankAccount->id,
        'nilai' => 2500000,
        'dalam_review' => 'rejected',
        'user_id' => $user->id,
        'level_id' => null,
        'disetujui' => null,
        'ditolak' => 'Finance Manager',
        'alasan' => 'Budget belum tersedia untuk periode ini',
        'project_id' => null,
        'sewa_id' => null,
        'penjualan_id' => null,
        'kalibrasi_id' => $kalibrasi->id,
        'company_id' => $company->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'judul_pengajuan' => 'Pengajuan Dana Penjualan Equipment Baru',
        'deskripsi_pengajuan' => 'Pengajuan dana untuk pembelian equipment baru untuk project expansion',
        'bank_id' => $bank->id,
        'bank_account_id' => $bankAccount->id,
        'nilai' => 30000000,
        'dalam_review' => 'approved',
        'user_id' => $user->id,
        'level_id' => null,
        'disetujui' => 'Director',
        'ditolak' => null,
        'alasan' => null,
        'project_id' => null,
        'sewa_id' => null,
        'penjualan_id' => $penjualan->id,
        'kalibrasi_id' => null,
        'company_id' => $company->id,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

        // Insert the data
        foreach ($pengajuanDanas as $pengajuan) {
            PengajuanDana::create($pengajuan);
        }

        $this->command->info('PengajuanDanaSeeder executed successfully!');
    }
}
