<?php

namespace App\Filament\Resources\TransaksiPembayaranResource\Pages;

use App\Filament\Pages\RingkasanTransaksi;
use App\Filament\Resources\TransaksiPembayaranResource;
use App\Models\PengajuanDana;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiPembayarans extends ListRecords
{
    protected static string $resource = TransaksiPembayaranResource::class;

    public ?PengajuanDana $pengajuan = null;

    /**
     * Modifikasi query utama untuk memfilter berdasarkan pengajuan_dana_id dari URL.
     */
    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();

        if ($pengajuanId = request()->get('pengajuan_dana_id')) {
            $this->pengajuan = PengajuanDana::find($pengajuanId);
            $query->where('pengajuan_dana_id', $pengajuanId);
        }

        return $query;
    }

    /**
     * Membuat judul halaman menjadi dinamis.
     */
    public function getTitle(): string
    {
        if ($this->pengajuan) {
            return 'Transaksi untuk: ' . $this->pengajuan->judul_pengajuan;
        }
        return parent::getTitle();
    }

    /**
     * Membuat breadcrumbs menjadi dinamis.
     */
    public function getBreadcrumbs(): array
    {
        if ($this->pengajuan) {
            return [
                RingkasanTransaksi::getUrl() => 'Ringkasan Transaksi Keluar',
                '#' => $this->getTitle(),
            ];
        }
        return parent::getBreadcrumbs();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
