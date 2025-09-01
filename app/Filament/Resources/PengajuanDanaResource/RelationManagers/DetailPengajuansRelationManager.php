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
                    ->label('Nama Item')
                    ->required(),

                Forms\Components\TextInput::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->minValue(1)
                    ->maxLength(4)
                    ->default(1)
                    ->reactive()
                    ->required()
                    ->validationMessages([
                        'required' => 'Jumlah wajib diisi',
                        'max_digits' => 'Jumlah tidak boleh lebih dari 12 digit',
                        'min_value' => 'Jumlah tidak boleh kurang dari 0',
                    ])
                    ->afterStateUpdated(function (callable $set, $get) {
                        $set('total', (int) $get('qty') * (int) $get('harga_satuan'));
                    }),

                Forms\Components\Textinput::make('satuan')
                    ->required()
                    ->placeholder('Contoh: liter,kilogram,dll')
                    ->maxLength(50),

                Forms\Components\TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->maxLength(9)
                    ->minValue(0)
                    ->reactive()
                    ->required()
                    ->validationMessages([
                        'required' => 'Harga satuan wajib diisi',
                        'max_digits' => 'Tidak boleh lebih dari 9 digit',
                        'min_value' => 'Tidak boleh kurang dari 0',
                    ])
                    ->afterStateUpdated(function (callable $set, $get) {
                        $set('total', (int) $get('qty') * (int) $get('harga_satuan'));
                    }),

                Forms\Components\Hidden::make('total')
                    ->dehydrated(true)
                    ->reactive()
                    ->default(fn($get) => (int) $get('qty') * (int) $get('harga_satuan'))
                    ->afterStateHydrated(function (callable $set, $get) {
                        $set('total', (int) $get('qty') * (int) $get('harga_satuan'));
                    }),

            ])->columns(4);
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
