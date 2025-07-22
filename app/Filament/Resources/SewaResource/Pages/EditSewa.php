<?php

namespace App\Filament\Resources\SewaResource\Pages;

use App\Filament\Resources\SewaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; // <-- DITAMBAHKAN
use Filament\Notifications\Notification; // <-- DITAMBAHKAN

class EditSewa extends EditRecord
{
    protected static string $resource = SewaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                // Tombol hapus juga nonaktif jika sudah dikunci
                ->visible(fn(Model $record): bool => !$record->is_locked),
        ];
    }

    /**
     * Menangani logika kustom sebelum data disimpan.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Cek apakah toggle 'tutup_sewa' diaktifkan
        if (isset($data['tutup_sewa']) && $data['tutup_sewa']) {
            // Jika ya, set kolom 'is_locked' menjadi true
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
