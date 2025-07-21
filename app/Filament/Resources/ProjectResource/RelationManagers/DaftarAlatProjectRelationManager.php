<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Filament\Resources\Shared\BaseAlatSewaRelationManager;
use App\Models\Project;
use App\Models\Sewa;

/**
 * Relation Manager untuk menampilkan alat yang digunakan pada halaman Project.
 * File: app/Filament/Resources/ProjectResource/RelationManagers/DaftarAlatProjectRelationManager.php
 */
class DaftarAlatProjectRelationManager extends BaseAlatSewaRelationManager
{
    protected static string $relationship = 'daftarAlat';

    protected static ?string $title = 'Daftar Alat';

    /**
     * Mengimplementasikan metode abstract dari parent.
     * Logika ini akan mengambil data Sewa dari Project.
     * Jika data Sewa belum ada, maka akan dibuat secara otomatis.
     */
    protected function getSewaRecord(): Sewa
    {
        /** @var Project $project */
        $project = $this->getOwnerRecord();

        // Cek apakah relasi 'sewa' sudah ada dan terisi.
        // Penggunaan 'first()' untuk keamanan jika relasi didefinisikan sebagai hasMany.
        $sewa = $project->sewa()->first();

        if ($sewa) {
            return $sewa;
        }

        // Jika belum ada, buat record Sewa baru berdasarkan data dari Project.
        $newSewa = Sewa::create([
            'judul' => 'Sewa untuk Proyek: ' . ($project->nama_project ?? 'Tanpa Nama'),
            'tgl_mulai' => $project->tgl_mulai ?? now(),
            'tgl_selesai' => $project->tgl_selesai ?? now()->addDays(30), // Default 30 hari
            'provinsi' => $project->provinsi ?? '',
            'kota' => $project->kota ?? '',
            'kecamatan' => $project->kecamatan ?? '',
            'desa' => $project->desa ?? '',
            'detail_alamat' => $project->detail_alamat ?? '',
            'user_id' => auth()->id(),
            'customer_id' => $project->customer_id ?? null,
            'customer_type' => $project->customer_type ?? null,
        ]);

        // Hubungkan sewa baru dengan project saat ini.
        // Asumsi ada kolom 'sewa_id' di tabel 'projects'.
        $project->update(['sewa_id' => $newSewa->id]);

        // Muat ulang relasi agar tersedia untuk proses selanjutnya.
        return $project->fresh()->sewa;
    }
}
