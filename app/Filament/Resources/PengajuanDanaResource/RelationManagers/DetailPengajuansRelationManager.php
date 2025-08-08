<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Level;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;

class DetailPengajuansRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPengajuans';
    protected static ?string $title = 'Rincian Pengajuan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('deskripsi')
                    ->required()->columnSpan(2),
                Forms\Components\TextInput::make('qty')->label('Jumlah')->required()->numeric()->default(1)->minValue(1),
                Forms\Components\Textinput::make('satuan')->required(),
                Forms\Components\TextInput::make('harga_satuan')
                    ->mask(RawJs::make('$money($input)'))
                    ->label('Harga Satuan')
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->validationMessages([
                        'required' => 'Nilai wajib diisi',
                    ]),

            ])->columns(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('deskripsi')
            ->columns([
                Tables\Columns\TextColumn::make('deskripsi'),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('satuan'),
                Tables\Columns\TextColumn::make('harga_satuan')->money('IDR'),
                Tables\Columns\TextColumn::make('total')
                    ->state(fn($record) => $record->qty * $record->harga_satuan)
                    ->money('IDR'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function ($livewire, $record) {
                        $pengajuan = $livewire->getOwnerRecord();
                        $pengajuan->updateTotalHarga();

                        $nilai = $pengajuan->nilai;

                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            // Ambil step pertama berdasarkan urutan step
                            $firstStep = $level->levelSteps()->orderBy('step')->first();

                            // Ambil nama role dari relasi role di levelStep
                            $roleName = optional($firstStep?->roles)->id;

                            $pengajuan->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName, // kolom ini sekarang menyimpan nama role
                            ]);
                        }
                    })

            ])
            ->actions([
                EditAction::make()
                    ->after(function ($livewire, $record) {
                        $pengajuan = $livewire->getOwnerRecord();
                        $pengajuan->updateTotalHarga();

                        $nilai = $pengajuan->nilai;

                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            // Ambil step pertama berdasarkan urutan step
                            $firstStep = $level->levelSteps()->orderBy('step')->first();

                            // Ambil nama role dari relasi role di levelStep
                            $roleName = optional($firstStep?->roles)->id;

                            $pengajuan->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName, // kolom ini sekarang menyimpan nama role
                            ]);
                        }
                    }),
                DeleteAction::make()
                    ->after(function ($livewire, $record) {
                        $pengajuan = $livewire->getOwnerRecord();
                        $pengajuan->updateTotalHarga();

                        $nilai = $pengajuan->nilai;

                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            // Ambil step pertama berdasarkan urutan step
                            $firstStep = $level->levelSteps()->orderBy('step')->first();

                            // Ambil nama role dari relasi role di levelStep
                            $roleName = optional($firstStep?->roles)->id;

                            $pengajuan->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName, // kolom ini sekarang menyimpan nama role
                            ]);
                        }
                    })
            ]);
    }
}
