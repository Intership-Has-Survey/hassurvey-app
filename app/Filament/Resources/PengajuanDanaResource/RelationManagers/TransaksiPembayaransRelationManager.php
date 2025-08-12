<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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

class TransaksiPembayaransRelationManager extends RelationManager
{
    protected static string $relationship = 'statusPengeluarans';
    protected static ?string $title = 'Realisasi Pembayaran';

    public function form(Form $form): Form
    {
        $pengajuan = $this->ownerRecord;
        $totalDiajukan = $pengajuan->detailPengajuans()->sum(DB::raw('qty * harga_satuan'));
        $totalDibayar = (float) $pengajuan->statusPengeluarans()->sum('nilai');
        $sisaPembayaran = $totalDiajukan - $totalDibayar;

        return $form
            ->schema([
                Placeholder::make('sisa_tagihan')
                    ->label('Sisa Pembayaran yang Belum Dilunasi')
                    ->content(function () use ($sisaPembayaran) {
                        if ($sisaPembayaran <= 0) {
                            return 'Lunas';
                        }
                        return 'Rp ' . number_format($sisaPembayaran, 0, ',', '.');
                    })
                    ->visibleOn('create'),
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
                DatePicker::make('tanggal_transaksi')
                    ->required()
                    ->validationMessages([
                        'required' => 'Tanggal transaksi wajib diisi',
                    ])
                    ->native(false),
                Select::make('metode_pembayaran')
                    ->options(['Transfer' => 'Transfer', 'Tunai' => 'Tunai'])->required(),
                FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->directory('bukti-pembayaran'),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->maxlength(500)
                    ->nullable(),
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
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
