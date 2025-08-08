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
        /** @var Project $project */
        $project = $this->getOwnerRecord();

        $sewa = $project->sewa()->first();

        if ($sewa) {
            return $sewa;
        }

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
            'corporate_id' => Corporate::where('nama', 'CV HAS Survey')->first()?->id,
            'company_id' => $project->company_id,
        ]);

        $project->update(['sewa_id' => $newSewa->id]);

        return $project->fresh()->sewa;
    }
}
