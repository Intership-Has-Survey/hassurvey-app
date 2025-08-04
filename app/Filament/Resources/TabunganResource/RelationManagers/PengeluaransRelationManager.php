<?php

namespace App\Filament\Resources\TabunganResource\RelationManagers;

use App\Models\Bangunan;
use App\Models\Orang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PengeluaransRelationManager extends RelationManager
{
    protected static string $relationship = 'pengeluarans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('deskripsi')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\Select::make('pengeluaranable_id')
                    ->label(fn () => $this->ownerRecord->target_tipe === 'orang' ? 'Pilih Orang' : 'Pilih Bangunan')
                    ->options(function () {
                        if ($this->ownerRecord->target_tipe === 'orang') {
                            return Orang::all()->pluck('nama', 'id');
                        } elseif ($this->ownerRecord->target_tipe === 'bangunan') {
                            return Bangunan::all()->pluck('nama', 'id');
                        }
                        return [];
                    })
                    ->required()
                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                        if ($this->getRecord()?->pengeluaranable_type === Orang::class || $this->getRecord()?->pengeluaranable_type === Bangunan::class) {
                            $component->state($this->getRecord()->pengeluaranable_id);
                        }
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('deskripsi')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi'),
                Tables\Columns\TextColumn::make('pengeluaranable.nama')
                    ->label('Ditujukan Kepada'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if ($this->ownerRecord->target_tipe === 'orang') {
                            $data['pengeluaranable_type'] = Orang::class;
                        } elseif ($this->ownerRecord->target_tipe === 'bangunan') {
                            $data['pengeluaranable_type'] = Bangunan::class;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if ($this->ownerRecord->target_tipe === 'orang') {
                            $data['pengeluaranable_type'] = Orang::class;
                        } elseif ($this->ownerRecord->target_tipe === 'bangunan') {
                            $data['pengeluaranable_type'] = Bangunan::class;
                        }
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
