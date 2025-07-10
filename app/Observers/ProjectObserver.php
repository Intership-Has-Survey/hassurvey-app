<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    /**
     * Handle the Project "updated" event.
     * Method ini akan berjalan secara otomatis SETELAH sebuah data proyek di-update.
     */
    public function updated(Project $project): void
    {
        if ($project->isDirty('nilai_project')) {
            $this->updatePaymentStatus($project);
        }

        if ($project->status === 'Selesai') {
            $project->daftarAlat()->updateExistingPivot(
                $project->daftarAlat->pluck('id'),
                ['status' => 'Tersedia']
            );
        }
    }

    /**
     * Ini adalah salinan dari logika yang ada di PembayaranObserver
     * untuk menghitung status pembayaran.
     */
    protected function updatePaymentStatus(Project $project): void
    {
        if ($project->nilai_project <= 0) {
            $project->status_pembayaran = 'Nilai Proyek Belum Ditentukan';
            $project->saveQuietly();
            return;
        }

        $totalDibayar = $project->statuspembayaran()->sum('nilai');

        $statusBaru = '';
        if ((float) $totalDibayar >= (float) $project->nilai_project) {
            $statusBaru = 'Lunas';
        } else {
            $statusBaru = 'Belum Lunas';
        }

        $project->status_pembayaran = $statusBaru;
        $project->saveQuietly();
    }
}
