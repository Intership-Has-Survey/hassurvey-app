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


        // $isSelesai = in_array($statusPekerjaan->jenis_pekerjaan, ['pekerjaan_lapangan', 'data_gambar', 'laporan'])
        //     && in_array($statusPekerjaan->proses_data_dan_gambar, ['Selesai', 'Tidak Perlu'])
        //     && in_array($statusPekerjaan->laporan, ['Selesai', 'Tidak Perlu']);
        // $statusBaru = $isSelesai ? 'Selesai' : 'Belum Selesai';

        $allStatus = $project->statusPekerjaan()->pluck('status');

        // Tentukan apakah semua selesai / tidak perlu
        $isSelesai = $allStatus->every(fn($status) => in_array($status, ['Selesai', 'Tidak Perlu']));
        $project->status_pekerjaan = $isSelesai ? 'Selesai' : 'Belum Selesai';
        $project->saveQuietly();
    }
}
