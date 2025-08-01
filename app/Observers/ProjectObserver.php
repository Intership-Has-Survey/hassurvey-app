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
}
