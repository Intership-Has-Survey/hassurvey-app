<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Personel;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class PersonelsRelationManager extends RelationManager
{
    protected static string $relationship = 'personels';

    protected static ?string $title = 'Tim Personel';

    protected static bool $isLazy = false;

    // Form ini hanya digunakan untuk MENGEDIT data pivot (peran)
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form ini tidak lagi digunakan secara langsung.
                // Logika form dipindahkan ke AttachAction dan EditAction.
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(
                        fn(string $state): string => $state === 'Tersedia' ? 'success' : 'warning',
                    ),
                Tables\Columns\TextColumn::make('jabatan')
                    ->badge(),
                // Menampilkan data 'peran' dari tabel pivot
                Tables\Columns\TextColumn::make('pivot.peran')
                    ->label('Peran di Proyek')
                    ->badge(),
                Tables\Columns\TextColumn::make('pivot.tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date(),
                Tables\Columns\TextColumn::make('pivot.tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->placeholder('Belum Berakhir'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        Forms\Components\Select::make('recordId')
                            ->label('Pilih Personel')
                            ->options(function () {
                                $alreadyAttachedIds = $this->getOwnerRecord()->personels()->pluck('personel.id');
                                return Personel::whereNotIn('id', $alreadyAttachedIds)->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('peran')
                            ->options([
                                'surveyor' => 'Surveyor',
                                'asisten surveyor' => 'Asisten Surveyor',
                                'driver' => 'Driver',
                                'drafter' => 'Drafter',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->successNotificationTitle('Personel berhasil ditambahkan.')
                    ->label('Tambah Personel')
                    ->modalHeading('Tambah Personel ke Proyek')
                    ->modalSubmitActionLabel('Tambah')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn(Tables\Actions\EditAction $action): array => [
                        Forms\Components\Select::make('peran')
                            ->options([
                                'surveyor' => 'Surveyor',
                                'asisten surveyor' => 'Asisten Surveyor',
                                'driver' => 'Driver',
                                'drafter' => 'Drafter',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->disabled()
                            ->native(false),
                        Forms\Components\DatePicker::make('tanggal_berakhir')
                            ->label('Tanggal Berakhir')
                            ->native(false),
                    ]),
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
