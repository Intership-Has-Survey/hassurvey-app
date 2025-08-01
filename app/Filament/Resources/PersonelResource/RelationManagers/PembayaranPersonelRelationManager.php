<?php

namespace App\Filament\Resources\PersonelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;

class PembayaranPersonelRelationManager extends RelationManager
{
    protected static string $relationship = 'pembayaranPersonel';

    protected static ?string $title = 'Riwayat Pembayaran';
    // protected static string $relationship = 'PembayaranPersonel';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal_transaksi')
                    ->label('Tanggal transaksi')
                    ->required(),

                Forms\Components\Select::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        'transfer' => 'Transfer',
                        'tunai' => 'Tunai',
                    ]),
                Forms\Components\TextInput::make('nilai')
                    ->numeric()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),
                Forms\Components\FileUpload::make('bukti_pembayaran'),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                // Tables\Columns\TextColumn::make('personel_project_id'),
                Tables\Columns\TextColumn::make('project.nama_project')
                    ->label('Proyek'),
                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal Transaksi'),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran'),
                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai'),
                Tables\Columns\TextColumn::make('bukti_pembayaran')
                    ->label('Bukti Pembayaran'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('diupdate oleh'),
                // Forms\Components\TextInput::make('personel_project_id')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
