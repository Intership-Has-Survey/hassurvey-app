<?php

namespace App\Filament\Resources\SewaResource\Pages;

use App\Filament\Resources\SewaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model; // <-- DITAMBAHKAN
use Filament\Notifications\Notification; // <-- DITAMBAHKAN

class EditSewa extends EditRecord
{
    protected static string $resource = SewaResource::class;

    // TAMBAHKAN SELURUH METODE DI BAWAH INI
    protected function getHeaderActions(): array
    {
        // $actions = parent::getHeaderActions();
        // foreach ($actions as $action) {
        //     if ($action->getName() === 'delete') {
        //         $action->visible(
        //             !$this->getRecord()->daftarAlat()->exists()
        //         );
        //     }
        // }
        // return $actions;
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (filled($data['corporate_id'])) {
            $data['customer_flow_type'] = 'corporate';
        } else {
            $data['customer_flow_type'] = 'perorangan';
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Cek apakah toggle 'tutup_sewa' diaktifkan
        if (isset($data['tutup_sewa']) && $data['tutup_sewa']) {

            // -- LOGIKA VALIDASI DITAMBAHKAN DI SINI --
            $alatBelumKembali = $record->daftarAlat()->whereNull('tgl_masuk')->count();

            if ($alatBelumKembali > 0) {
                // Jika masih ada alat yang belum kembali, kirim notifikasi error
                Notification::make()
                    ->title('Gagal Mengunci Sewa')
                    ->body("Masih ada {$alatBelumKembali} alat yang belum dikembalikan. Mohon kembalikan semua alat terlebih dahulu.")
                    ->danger()
                    ->send();

                // Hentikan proses penyimpanan form
                throw new Halt;
            }
            // -- AKHIR LOGIKA VALIDASI --

            // Jika validasi lolos, lanjutkan proses penguncian
            $data['is_locked'] = true;

            Notification::make()
                ->title('Sewa berhasil ditutup dan dikunci')
                ->success()
                ->send();
        }

        // Hapus field sementara agar tidak coba disimpan ke database
        unset($data['tutup_sewa']);

        // Lanjutkan proses update standar
        $record->update($data);

        return $record;
    }
}
