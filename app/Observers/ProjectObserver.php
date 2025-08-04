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
        $nilaiDasar = (float) $project->nilai_project_awal;
        $nilaiBulat = floor($nilaiDasar);

        if ($project->dikenakan_ppn) {
            $project->nilai_ppn = $nilaiDasar * 0.12;
        } else {
            $project->nilai_ppn = 0;
        }
        $project->nilai_project = $nilaiBulat + $project->nilai_ppn;
    }
}
