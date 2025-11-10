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
            $p = $project->id;
            //UUID SAles ALI MUHAMMAD HASAN
            //0198a980-6b2f-7376-8ff2-6dbab5a13452
            $ali = '0198a980-6b2f-7376-8ff2-6dbab5a13452';

            // Create sewa record if it doesn't exist
            $newSewa = Sewa::create([
                'judul' => ($project->nama_project ?? 'Tanpa Nama'),
                'tgl_mulai' => $project->mulai ?? now(),
                'tgl_selesai' => $project->akhir ?? now()->addDays(30),
                'provinsi' => $project->provinsi ?? '',
                'kota' => $project->kota ?? '',
                'kecamatan' => $project->kecamatan ?? '',
                'desa' => $project->desa ?? '',
                'detail_alamat' => $project->detail_alamat ?? '',
                'user_id' => auth()->id(),
                'corporate_id' => $project->corporate_id,
                'company_id' => $project->company_id,
                'sumber' => 'Offline',
                'sales_id' => $ali,
            ]);

            if ($project->perorangan->isNotEmpty()) {
                $pivotData = [];
                foreach ($project->perorangan as $perorangan) {
                    $pivotData[$perorangan->id] = [
                        'peran' => $perorangan->peran ?? 'Perorangan'
                    ];
                }
                $newSewa->perorangan()->attach($pivotData);
            }

            // Immediately associate the sewa with the project
            $project->update(['sewa_id' => $newSewa->id]);
            $project->refresh();

            return $newSewa;
        }

        return $sewa;
    }
}
