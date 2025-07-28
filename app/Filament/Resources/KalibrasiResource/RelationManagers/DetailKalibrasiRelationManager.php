<?php

namespace App\Filament\Resources\KalibrasiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailKalibrasiRelationManager extends RelationManager
{
    protected static string $relationship = 'alatCustomers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select::make('alat_customer_id')
                //     ->relationship('alatcustomer', 'nomor_seri')
                //     ->searchable()
                //     ->preload()
                //     ->required(),
                DatePicker::make('tgl_masuk')
                    ->label('Tanggal Masuk')
                    ->live(onBlur: true)
                    ->required(),
                DatePicker::make('tgl_stiker_kalibarsi')
                    ->label('Tanggal Stiker Kalibrasi')
                    ->live(onBlur: true)
                    ->required(),
                DatePicker::make('tgl_keluar')
                    ->label('Tanggal Selesai')
                    ->live(onBlur: true),
                Select::make('status')
                    ->options([
                        'progress' => 'progress',
                        'selesai' => 'selesai'
                    ])
                    ->native(false),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_seri')
            ->columns([
                Tables\Columns\TextColumn::make('alat_customer_id'),
                Tables\Columns\TextColumn::make('tgl_masuk'),
                Tables\Columns\TextColumn::make('tgl_stiker_kalibrasi'),
                Tables\Columns\TextColumn::make('tgl_keluar'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    // ->preloadRecordSelect()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        Forms\Components\Placeholder::make('kalibrasi_id')
                            ->label('Pilih Alat'),
                        $action
                            ->getRecordSelect(),
                        Forms\Components\DatePicker::make('tgl_masuk')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Forms\Components\DatePicker::make('tgl_stiker_kalibrasi')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->hidden()
                            ->native(false),
                        Forms\Components\DatePicker::make('tgl_keluar')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->hidden()
                            ->native(false),
                        Forms\Components\Hidden::make('status')
                            ->label('Tanggal Mulai')
                    ])
                    ->successNotificationTitle('Kalibrasi Berhasil ditambahkan.')
                    ->label('Tambah Alat')
                    ->modalHeading('Tambah alat untuk dikalibrasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
