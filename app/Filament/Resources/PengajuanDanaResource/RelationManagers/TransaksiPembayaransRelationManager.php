<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use App\Models\PengajuanDana;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;


abstract class TransaksiPembayaransRelationManager extends RelationManager
{
    protected static string $relationship = 'statusPengeluarans';
    protected static ?string $title = 'Realisasi Pembayaran';

    abstract protected function getPengajuanDanaRecord(): PengajuanDana;

    protected function afterSave(): void
    {
        $pengajuan = $this->ownerRecord;

        $totalDiajukan = $pengajuan->detailPengajuans()->sum(DB::raw('qty * harga_satuan'));
        $totalDibayar = (float) $pengajuan->statusPengeluarans()->sum('nilai');
        $sisaPembayaran = $totalDiajukan - $totalDibayar;

        $statusBaru = null;
        $keteranganStatus = '';

        if ($sisaPembayaran == 0) {
            $statusBaru = '1';
            $keteranganStatus = '0';
        } elseif ($sisaPembayaran > 0) {
            $statusBaru = 'belum_lunas';
            $keteranganStatus = 'Belum Lunas - Sisa: Rp ' . number_format($sisaPembayaran, 0, ',', '.');
        } else {
            $statusBaru = '2';
            $keteranganStatus = 'Lebih Bayar - Rp ' . number_format(abs($sisaPembayaran), 0, ',', '.');
        }

        if ($pengajuan->status !== $statusBaru) {
            $pengajuan->update(['status' => $statusBaru]);
        }

        if ($this->getPengajuanDanaRecord()->status !== $statusBaru) {
            $this->getPengajuanDanaRecord()->update(['status' => $statusBaru]);
        }
    }

    public function form(Form $form): Form
    {
        $pengajuan = $this->ownerRecord;

        $totalDiajukan = $pengajuan->detailPengajuans()->sum(DB::raw('qty * harga_satuan'));
        $totalDibayar = (float) $pengajuan->statusPengeluarans()->sum('nilai');
        $sisaPembayaran = $totalDiajukan - $totalDibayar;

        return $form
            ->schema([
                Placeholder::make('sisa_tagihan')
                    ->content(function () use ($sisaPembayaran): string|HtmlString {
                        if ($sisaPembayaran == 0) {
                            return new HtmlString('<span style="color: green; font-weight: bold;">LUNAS</span>');
                        } elseif ($sisaPembayaran > 0) {
                            return new HtmlString('<span style="color: red; font-weight: bold;">BELUM LUNAS - Kurang: Rp' . number_format($sisaPembayaran, 0, ',', '.') . '</span>');
                        } else {
                            $nilai = number_format(abs($sisaPembayaran), 0, ',', '.');
                            return new HtmlString('<span style="color: red; font-weight: bold;">LEBIH BAYAR - Lebih: Rp' . $nilai . '</span>');
                        }
                    })
                    ->label('Status Pembayaran')
                    ->visibleOn('create'),

                Placeholder::make('total_diajukan')
                    ->content('Rp ' . number_format($totalDiajukan, 0, ',', '.'))
                    ->label('Total Pengajuan'),

                Select::make('metode_pembayaran')
                    ->options(['Transfer' => 'Transfer', 'Tunai' => 'Tunai'])->required(),

                DatePicker::make('tanggal_transaksi')
                    ->required()
                    ->validationMessages([
                        'required' => 'Tanggal transaksi wajib diisi',
                    ])
                    ->native(false),

                TextInput::make('nilai')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->validationMessages([
                        'required' => 'Nilai wajib diisi',
                    ])
                    ->prefix('Rp')
                    ->maxlength(20),

                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxlength(500)
                    ->nullable(),

                FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->directory('bukti-pembayaran'),
                Hidden::make('user_id')->default(auth()->id()),
                Hidden::make('company_id')->default(Filament::getTenant()->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nilai')
            ->columns([
                TextColumn::make('tanggal_transaksi')->date('d M Y'),
                TextColumn::make('nilai')->money('IDR'),
                TextColumn::make('metode_pembayaran')->badge(),
                ImageColumn::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->disk('public')
                    ->square()
                    ->url(fn(Model $record): ?string => $record->bukti_pembayaran_path ? Storage::disk('public')->url($record->bukti_pembayaran_path) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('user.name')->label('Dibayar oleh'),
                // Kolom status dihapus karena status yang relevan adalah di pengajuan_dana
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function ($record, $livewire) {

                        $pengajuan = $livewire->ownerRecord;
                        // dd($pengajuan);
                        $pengajuan->update([
                            'dibayar' => $pengajuan->statusPengeluarans()->sum('nilai'),
                        ]);
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
