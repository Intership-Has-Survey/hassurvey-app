<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\StatusPekerjaan;

class StatusPekerjaanObserver
{
    public function saved(StatusPekerjaan $statusPekerjaan): void
    {
        if ($statusPekerjaan->project) {
            $this->updateProjectWorkStatus($statusPekerjaan->project);
        }
    }

    protected function updateProjectWorkStatus(Project $project): void
    {
        $requiredStages = ['pekerjaan_lapangan', 'data_gambar', 'laporan'];

        $allStages = $project->statusPekerjaan()->whereIn('jenis_pekerjaan', $requiredStages)->get();

        // Check if all required stages exist
        $existingStages = $allStages->pluck('jenis_pekerjaan')->unique()->toArray();

        $missingStages = array_diff($requiredStages, $existingStages);

        if (count($missingStages) > 0) {
            $project->status_pekerjaan = 'Belum Selesai';
            $project->saveQuietly();
            return;
        }

        $isFullyFinished = $allStages->every(function ($stage) {
            return in_array($stage->status, ['Selesai', 'Tidak Perlu']);
        });

        $statusBaru = $isFullyFinished ? 'Selesai' : 'Belum Selesai';

        $project->status_pekerjaan = $statusBaru;
        $project->saveQuietly();
    }
}
