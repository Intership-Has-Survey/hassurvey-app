<?php

namespace App\Filament\Resources\PenjualanResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\JenisAlat;
use App\Models\DaftarAlat;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\RelationManager;

class DetailPenjualanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPenjualan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_alat_id')
                    ->label('Jenis Alat')
                    ->relationship('jenisAlat', 'nama')
                    ->live()
                    ->afterStateUpdated(fn(Set $set) => $set('daftar_alat_id', null))
                    ->required(),
                Forms\Components\Select::make('daftar_alat_id')
                    ->label('Nomor Seri')
                    ->searchable()
                    ->getOptionLabelsUsing(function (array $values): array {
                        if (empty($values)) return [];
                        return DaftarAlat::whereIn('id', $values)->pluck('nomor_seri', 'id')->toArray();
                    })
                    ->options(function (Get $get, ?Model $record): array {
                        $jenisAlatId = $get('jenis_alat_id');
                        if (!$jenisAlatId && $record) {
                            $jenisAlatId = $record->daftarAlat?->jenis_alat_id;
                        }
                        if (!$jenisAlatId) return [];

                        $query = DaftarAlat::query()->where('jenis_alat_id', $jenisAlatId);

                        $query->where(function ($q) use ($record) {
                            $q->where('status', true)
                                ->orWhere('id', $record?->daftar_alat_id);
                        });

                        return $query->pluck('nomor_seri', 'id')->toArray();
                    })
                    ->required(),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('daftarAlat.jenisAlat.nama')->label('Jenis Alat'),
                Tables\Columns\TextColumn::make('daftarAlat.merk.nama')->label('Merk'),
                Tables\Columns\TextColumn::make('daftarAlat.nomor_seri')->label('Nomor Seri'),
                Tables\Columns\TextColumn::make('harga')->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Alat')
                    ->form([
                        Forms\Components\Select::make('jenis_alat_id')->label('Jenis Alat')->options(JenisAlat::query()->pluck('nama', 'id'))->live()->required()->searchable(),
                        Forms\Components\Select::make('daftar_alat_id')->label('Nomor Seri')->options(function (Get $get) {
                            $jenisAlatId = $get('jenis_alat_id');
                            if (!$jenisAlatId) return [];
                            return DaftarAlat::where('jenis_alat_id', $jenisAlatId)->where('status', true)->pluck('nomor_seri', 'id');
                        })->searchable()->required(),
                        Forms\Components\TextInput::make('harga')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp')
                            ->maxlength(20),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $alat = DaftarAlat::find($data['daftar_alat_id']);
                        if ($alat) {
                            $data['merk_id'] = $alat->merk_id;
                            $data['jenis_alat_id'] = $alat->jenis_alat_id;
                        }
                        return $data;
                    })
                    ->after(function (Model $record) {
                        if ($record->daftarAlat) {
                            $record->daftarAlat->status = 2; // Terjual
                            $record->daftarAlat->save();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mountUsing(function (Form $form, Model $record): void {
                        $data = $record->toArray();
                        $data['jenis_alat_id'] = $record->daftarAlat?->jenis_alat_id;
                        $form->fill($data);
                    })
                    ->using(function (Model $record, array $data): Model {

                        $oldAlatId = $record->daftar_alat_id;
                        $newAlatId = $data['daftar_alat_id'];
                        $alat = DaftarAlat::find($newAlatId);
                        if ($alat) {
                            $data['merk_id'] = $alat->merk_id;
                        }

                        $record->update($data);

                        if ($newAlatId !== $oldAlatId) {
                            if ($oldAlatId) {
                                $oldAlat = DaftarAlat::find($oldAlatId);
                                if ($oldAlat) {
                                    $oldAlat->status = true; // Tersedia
                                    $oldAlat->save();
                                }
                            }

                            $newAlat = DaftarAlat::find($newAlatId);
                            if ($newAlat) {
                                $newAlat->status = 2; // Terjual
                                $newAlat->save();
                            }
                        }

                        return $record;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Model $record) {
                        if ($record->daftarAlat) {
                            $record->daftarAlat->status = true; // Tersedia
                            $record->daftarAlat->save();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
