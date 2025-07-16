<?php

namespace App\Observers;

use App\Models\StatusPembayaran;
use App\Models\Project;

class PembayaranObserver
{
    /**
     * Handle events after a Pembayaran is created, updated, or deleted.
     */
    public function saved(StatusPembayaran $pembayaran): void
    {
        $this->updateProjectPaymentStatus($pembayaran->project);
    }

    public function deleted(StatusPembayaran $pembayaran): void
    {
        $this->updateProjectPaymentStatus($pembayaran->project);
    }

    /**
     * Logika utama untuk menghitung dan memperbarui status pembayaran proyek.
     */
    protected function updateProjectPaymentStatus(Project $project): void
    {
        // Jika proyek tidak punya nilai kontrak, jangan lakukan apa-apa
        if ($project->nilai_project <= 0) {
            $project->status_pembayaran = 'Nilai Kontrak Belum Ditentukan';
            $project->saveQuietly();
            return;
        }

        // Hitung total pembayaran yang sudah masuk untuk proyek ini.
        $totalDibayar = $project->statuspembayaran()->sum('nilai');

        // Kita secara eksplisit mengubah kedua nilai menjadi angka (float) sebelum membandingkan
        // untuk memastikan perbandingan numerik yang benar.
        $statusBaru = '';
        if ((float)$totalDibayar >= (float)$project->nilai_project) {
            $statusBaru = 'Lunas';
        } else {
            $statusBaru = 'Belum Lunas';
        }

        // Simpan status baru ke tabel proyek
        $project->status_pembayaran = $statusBaru;
        $project->saveQuietly();
    }
}
