<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Sewa;
use App\Models\Project;
use App\Models\Corporate;
use App\Filament\Resources\Shared\BaseAlatSewaRelationManager;

class DaftarAlatProjectRelationManager extends BaseAlatSewaRelationManager
{
    protected static string $relationship = 'daftarAlat';
    protected static ?string $title = 'Daftar Alat';

    protected function getSewaRecord(): Sewa
    {
        /** @var Projects $project */
        $project = $this->getOwnerRecord();

        // Ensure sewa record exists and is immediately visible
        $sewa = $project->Sewa()->first();

        if (!$sewa) {
            // Get corporate ID safely
            $corporate = Corporate::where('nama', 'CV HAS Survey')->first();
            $corporateId = $corporate ? $corporate->id : null;

            // Create sewa record if it doesn't exist
            $newSewa = Sewa::create([
                'judul' => ($project->nama_project ?? 'Tanpa Nama'),
                'tgl_mulai' => $project->tgl_mulai ?? now(),
                'tgl_selesai' => $project->tgl_selesai ?? now()->addDays(30),
                'provinsi' => $project->provinsi ?? '',
                'kota' => $project->kota ?? '',
                'kecamatan' => $project->kecamatan ?? '',
                'desa' => $project->desa ?? '',
                'detail_alamat' => $project->detail_alamat ?? '',
                'user_id' => auth()->id(),
                'corporate_id' => $corporateId,
                'company_id' => $project->company_id,
            ]);

            // Immediately associate the sewa with the project
            $project->update(['sewa_id' => $newSewa->id]);
            $project->refresh();

            return $newSewa;
        }

        return $sewa;
    }
}
