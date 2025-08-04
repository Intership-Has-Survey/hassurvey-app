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
        if ($pembayaran->payable instanceof Project) {
            $this->updateProjectPaymentStatus($pembayaran->payable);
        }
    }

    public function deleted(StatusPembayaran $pembayaran): void
    {
        if ($pembayaran->payable instanceof Project) {
            $this->updateProjectPaymentStatus($pembayaran->payable);
        }
    }

    /**
     * Logika utama untuk menghitung dan memperbarui status pembayaran proyek.
     */
    protected function updateProjectPaymentStatus(Project $project): void
    {
        if ($project->nilai_project <= 0) {
            $project->status_pembayaran = 'Nilai Kontrak Belum Ditentukan';
            $project->saveQuietly();
            return;
        }

        // Ambil total pembayaran dengan relasi morphMany
        $totalDibayar = $project->statusPembayaran()->sum('nilai');

        if ((float) $totalDibayar === 0.0) {
            $statusBaru = 'Belum Dibayar';
        } else {
            $statusBaru = ((float) $totalDibayar >= (float) $project->nilai_project)
                ? 'Lunas'
                : 'Belum Lunas';
        }

        $project->status_pembayaran = $statusBaru;
        $project->saveQuietly();
    }
}
