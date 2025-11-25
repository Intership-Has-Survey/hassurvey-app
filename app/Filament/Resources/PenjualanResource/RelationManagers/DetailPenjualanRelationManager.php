<?php

namespace App\Filament\Resources\PenjualanResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Produk;
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
                    ->afterStateUpdated(fn(Set $set) => $set('produk_id', null))
                    ->required()
                    ->disabledOn('edit')
                    ->validationMessages([
                        'required' => 'Jenis Alat wajib diisi',
                    ]),
                Forms\Components\Select::make('produk_id')
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $merk = Produk::find($get('produk_id'))?->merk_id;
                        $set('merk_id', $merk);
                    })
                    ->reactive()
                    ->label('Nomor Seri')
                    ->disabledOn('edit')
                    ->relationship('produk', 'nomor_seri')
                    ->required()
                    ->options(function (Get $get) {
                        $jenisAlatId = $get('jenis_alat_id');
                        if (!$jenisAlatId) {
                            return [];
                        }
                        return Produk::where('jenis_alat_id', $jenisAlatId)
                            ->where('status', true)
                            ->pluck('nomor_seri', 'id');
                    })
                    ->validationMessages([
                        'required' => 'Jenis Alat wajib diisi',
                    ]),
                Forms\Components\Hidden::make('merk_id')
                    ->label('MERK')
                    ->reactive(),
                Forms\Components\TextInput::make('harga')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->validationMessages([
                        'required' => 'Harga wajib diisi',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('produk.nomor_seri')->label('Nomor Seri'),
                Tables\Columns\TextColumn::make('jenisAlat.nama')->label('Jenis Alat'),
                Tables\Columns\TextColumn::make('merk.nama')->label('Merk'),
                Tables\Columns\TextColumn::make('harga')->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (Model $record) {
                        // dd($record);
                        if ($record->produk) {
                            $record->produk->status = 0; // Terjual
                            $record->produk->saveQuietly();
                        }
                    }),
                // Tables\Actions\CreateAction::make('hi')
                //     ->label('Tambah Alat')
                //     ->form([
                //         Forms\Components\Select::make('jenis_alat_id')->label('Jenis Alat')->options(JenisAlat::query()->pluck('nama', 'id'))->live()->required()->searchable()
                //             ->validationMessages([
                //                 'required' => 'Jenis Alat wajib diisi',
                //             ]),
                //         Forms\Components\Select::make('daftar_alat_id')->label('Nomor Seri')->options(function (Get $get) {
                //             $jenisAlatId = $get('jenis_alat_id');
                //             if (!$jenisAlatId) return [];
                //             return DaftarAlat::where('jenis_alat_id', $jenisAlatId)->where('status', true)->pluck('nomor_seri', 'id');
                //         })->searchable()->required()->validationMessages([
                //             'required' => 'Nomor Seri wajib diisi',
                //         ]),
                //         Forms\Components\TextInput::make('harga')
                //             ->mask(RawJs::make('$money($input)'))
                //             ->stripCharacters(',')
                //             ->numeric()
                //             ->prefix('Rp')
                //             ->required()
                //             ->validationMessages([
                //                 'required' => 'Harga wajib diisi',
                //             ]),
                //     ])
                //     ->mutateFormDataUsing(function (array $data): array {
                //         $alat = DaftarAlat::find($data['daftar_alat_id']);
                //         if ($alat) {
                //             $data['merk_id'] = $alat->merk_id;
                //             $data['jenis_alat_id'] = $alat->jenis_alat_id;
                //         }
                //         return $data;
                //     })
                //     ->after(function (Model $record) {
                //         if ($record->daftarAlat) {
                //             $record->daftarAlat->status = 2; // Terjual
                //             $record->daftarAlat->save();
                //         }
                //     }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\EditAction::make('haah')
                //     ->mountUsing(function (Form $form, Model $record): void {
                //         $data = $record->toArray();
                //         $data['jenis_alat_id'] = $record->daftarAlat?->jenis_alat_id;
                //         $form->fill($data);
                //     })
                //     ->using(function (Model $record, array $data): Model {

                //         $oldAlatId = $record->daftar_alat_id;
                //         $newAlatId = $data['daftar_alat_id'];
                //         $alat = DaftarAlat::find($newAlatId);
                //         if ($alat) {
                //             $data['merk_id'] = $alat->merk_id;
                //         }

                //         $record->update($data);

                //         if ($newAlatId !== $oldAlatId) {
                //             if ($oldAlatId) {
                //                 $oldAlat = DaftarAlat::find($oldAlatId);
                //                 if ($oldAlat) {
                //                     $oldAlat->status = true; // Tersedia
                //                     $oldAlat->save();
                //                 }
                //             }

                //             $newAlat = DaftarAlat::find($newAlatId);
                //             if ($newAlat) {
                //                 $newAlat->status = 2; // Terjual
                //                 $newAlat->save();
                //             }
                //         }

                //         return $record;
                //     }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Model $record) {
                        if ($record->produk) {
                            $record->produk->status = true; // Tersedia
                            $record->produk->save();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    protected function afterCreate(): void
    {
        dd($this->record);
        $produkId = $this->record->produk_id;
        Produk::where('id', $produkId)->update(['status' => 0]);
    }
}
