<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class TransaksiPembayaransRelationManager extends RelationManager
{
    protected static string $relationship = 'transaksiPembayarans';
    protected static ?string $title = 'Realisasi Pembayaran';

    public function form(Form $form): Form
    {
        $pengajuan = $this->ownerRecord;
        $totalDiajukan = $pengajuan->detailPengajuans()->sum(DB::raw('qty * harga_satuan'));
        $totalDibayar = (float) $pengajuan->transaksiPembayarans()->sum('nilai');
        $sisaPembayaran = $totalDiajukan - $totalDibayar;

        return $form
            ->schema([
                Forms\Components\Placeholder::make('sisa_tagihan')
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
                    ->prefix('Rp')
                    ->maxlength(20),
                Forms\Components\DatePicker::make('tanggal_transaksi')->required()->native(false),
                Forms\Components\Select::make('metode_pembayaran')
                    ->options(['Transfer' => 'Transfer', 'Tunai' => 'Tunai'])->required(),
                Forms\Components\FileUpload::make('bukti_pembayaran_path')
                    ->label('Bukti Pembayaran')
                    ->directory('bukti-pembayaran'),
                Forms\Components\Hidden::make('user_id')->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nilai')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_transaksi')->date('d M Y'),
                Tables\Columns\TextColumn::make('nilai')->money('IDR'),
                Tables\Columns\TextColumn::make('metode_pembayaran')->badge(),
                Tables\Columns\TextColumn::make('user.name')->label('Dibayar oleh'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
