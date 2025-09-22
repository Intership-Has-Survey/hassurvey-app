<?php

namespace App\Filament\Resources\VisiMatiResource\RelationManagers;

use App\Filament\Resources\TabunganResource\RelationManagers\PengeluaransRelationManager as BasePengeluaransRelationManager;
use App\Models\Bangunan;
use App\Models\Orang;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class PengeluaransRelationManager extends BasePengeluaransRelationManager
{
    protected static string $relationship = 'pengeluarans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                \Filament\Forms\Components\TextInput::make('deskripsi')
                    ->maxLength(255)
                    ->nullable(),

                \Filament\Forms\Components\Select::make('pengeluaranable_id')
                    ->label(fn() => $this->ownerRecord->tabungan->target_tipe === 'orang' ? 'Pilih Orang' : 'Pilih Bangunan')
                    ->options(function () {
                        $tabungan = $this->ownerRecord->tabungan;
                        if ($tabungan->target_tipe === 'orang') {
                            return Orang::all()->pluck('nama', 'id');
                        } elseif ($tabungan->target_tipe === 'bangunan') {
                            return Bangunan::all()->pluck('nama', 'id');
                        }
                        return [];
                    })
                    ->required()
                    ->afterStateHydrated(function (\Filament\Forms\Components\Select $component, $state) {
                        if ($this->getRecord()?->pengeluaranable_type === Orang::class || $this->getRecord()?->pengeluaranable_type === Bangunan::class) {
                            $component->state($this->getRecord()->pengeluaranable_id);
                        }
                    }),
                \Filament\Forms\Components\Hidden::make('company_id')
                    ->default(fn() => \Filament\Facades\Filament::getTenant()?->getKey()),
            ]);
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $tabungan = $this->ownerRecord->tabungan;
                        if ($tabungan->target_tipe === 'orang') {
                            $data['pengeluaranable_type'] = Orang::class;
                        } elseif ($tabungan->target_tipe === 'bangunan') {
                            $data['pengeluaranable_type'] = Bangunan::class;
                        }
                        return $data;
                    })
                    ->using(function (array $data): Model {
                        return $this->ownerRecord->tabungan->pengeluarans()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $tabungan = $this->ownerRecord->tabungan;
                        if ($tabungan->target_tipe === 'orang') {
                            $data['pengeluaranable_type'] = Orang::class;
                        } elseif ($tabungan->target_tipe === 'bangunan') {
                            $data['pengeluaranable_type'] = Bangunan::class;
                        }
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
