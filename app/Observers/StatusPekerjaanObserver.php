<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\StatusPekerjaan;

class StatusPekerjaanObserver
{
    public function saved(StatusPekerjaan $statusPekerjaan): void
    {
        $this->updateProjectWorkStatus($statusPekerjaan);
    }

    protected function updateProjectWorkStatus(StatusPekerjaan $statusPekerjaan): void
    {
        $project = $statusPekerjaan->project;
        if (!$project) {
            return;
        }

        $isSelesai = $statusPekerjaan->pekerjaan_lapangan === 'Selesai' &&
            $statusPekerjaan->proses_data_dan_gambar === 'Selesai' &&
            $statusPekerjaan->laporan === 'Selesai';

        $statusBaru = $isSelesai ? 'Selesai' : 'Belum Selesai';

        $project->status_pekerjaan = $statusBaru;
        $project->saveQuietly();
    }
}
