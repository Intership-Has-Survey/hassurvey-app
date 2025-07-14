<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransaksiPembayaransRelationManager extends RelationManager
{
    protected static string $relationship = 'transaksiPembayarans';
    protected static ?string $title = 'Realisasi Pembayaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nilai')->required()->numeric()->prefix('Rp'),
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
