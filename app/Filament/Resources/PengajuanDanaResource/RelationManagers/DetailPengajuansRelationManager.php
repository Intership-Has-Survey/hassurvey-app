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
                Forms\Components\TextInput::make('qty')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxLength(4)
                    ->required()
                    ->validationMessages([
                        'required' => 'Jumlah wajib diisi',
                        'max_digits' => 'Jumlah tidak boleh lebih dari 12 digit',
                        'min_value' => 'Jumlah tidak boleh kurang dari 0',
                    ]),

                Forms\Components\Textinput::make('satuan')->required()->maxLength(50),
                Forms\Components\TextInput::make('harga_satuan')
                    ->mask(RawJs::make('$money($input)'))
                    ->label('Harga Satuan')
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxLength(9)
                    ->prefix('Rp')
                    ->validationMessages([
                        'required' => 'Nilai wajib diisi',
                        'max_digits' => 'Tidak boleh lebih dari 9 digit',
                        'min_value' => 'Tidak boleh kurang dari Rp 0',
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
                            $roleName = optional($firstStep?->role)->id;

                            $pengajuan->update([
                                'level_id' => $level->id,
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
                            $roleName = optional($firstStep?->role)->id;

                            $pengajuan->update([
                                'level_id' => $level->id,
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
                            $roleName = optional($firstStep?->role)->id;

                            $pengajuan->update([
                                'level_id' => $level->id,
                                'dalam_review' => $roleName, // kolom ini sekarang menyimpan nama role
                            ]);
                        }
                    })
            ]);
    }
}
