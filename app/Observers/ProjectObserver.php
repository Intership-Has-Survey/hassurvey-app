<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    public function updated(Project $project): void
    {
        if ($project->wasChanged('status')) {
            if ($project->status === 'Closing' && $project->status_pekerjaan !== 'Selesai') {
                $project->status_pekerjaan = 'Dalam Proses';
            } elseif ($project->status !== 'Closing' && $project->status !== 'Selesai') {
                $project->status_pekerjaan = 'Belum Dikerjakan';
            }
            $project->saveQuietly();
        }

        if ($project->status === 'Selesai') {
            $project->daftarAlat()->updateExistingPivot(
                $project->daftarAlat->pluck('id'),
                ['status' => 'Tersedia']
            );
        }
    }
    public function saving(Project $project): void
    {
        $nilaiDasar = (float) $project->nilai_project;

        if ($project->dikenakan_ppn) {
            $project->nilai_ppn = $nilaiDasar * 0.11;
        } else {
            $project->nilai_ppn = 0;
        }

        // Hitung total tagihan akhir
        $project->total_tagihan = $nilaiDasar + $project->nilai_ppn;
    }
}
