<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Level;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;

class DetailPengajuansRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPengajuans';
    protected static ?string $title = 'Rincian Pengajuan';

    protected static function parseMoney($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        // ambil hanya digit (buang Rp, spasi, titik, koma, dll)
        $clean = preg_replace('/[^\d]/', '', (string) $value);

        return (int) ($clean ?: 0);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('deskripsi')
                    ->label('Nama Item')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->maxLength(4)
                    ->minValue(1)
                    ->default(1)
                    ->required()
                    ->validationMessages([
                        'required' => 'Jumlah wajib diisi',
                        'max_digits' => 'Jumlah tidak boleh lebih dari 12 digit',
                        'min_value' => 'Jumlah tidak boleh kurang dari 0',
                    ])
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $qty   = (int) $get('qty');
                        $harga = self::parseMoney($get('harga_satuan')); // harga sudah bersih
                        $set('total', $qty * $harga);
                    }),


                Forms\Components\Textinput::make('satuan')
                    ->required()
                    ->placeholder('Contoh: liter,kilogram,dll')
                    ->maxLength(50),

                Forms\Components\TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')

                    ->required()
                    ->minValue(0)
                    ->validationMessages([
                        'required' => 'Harga satuan wajib diisi',
                        'max_digits' => 'Tidak boleh lebih dari 9 digit',
                        'min_value' => 'Tidak boleh kurang dari 0',
                    ])
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $qty   = (int) $get('qty');
                        $harga = self::parseMoney($get('harga_satuan')); // harga sudah bersih
                        $set('total', $qty * $harga);
                    }),

                // ->afterStateUpdated(function (callable $set, $get) {
                //     $set('total', (float) $get('qty') * (float) $get('harga_satuan'));
                // })

                Forms\Components\Hidden::make('total')
                    ->dehydrated(true)
                    // ->reactive()
                    ->default(fn(Get $get) => (int) $get('qty') * self::parseMoney($get('harga_satuan')))
                    ->afterStateHydrated(function (Set $set, Get $get) {
                        $set('total', (int) $get('qty') * self::parseMoney($get('harga_satuan')));
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
                        // $pengajuan->updateTotalHarga();
                        $nilai = $pengajuan->updateNilai();

                        $uuid = Filament::getTenant()->id;

                        // dd($uuid);
                        // $nilai = $pengajuan->nilai;
                        $level = Level::where('company_id', $uuid)
                            ->where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            // Ambil step pertama berdasarkan urutan step
                            $firstStep = $level->levelSteps()->orderBy('step')->first();

                            // Ambil nama role dari relasi role di levelStep
                            $roleId = optional($firstStep?->role)->id;

                            $pengajuan->update([
                                'nilai' => $nilai,
                                'level_id' => $level->id,
                                'dalam_review' => $roleId, // kolom ini sekarang menyimpan Id role
                            ]);
                        }
                    })

            ])
            ->actions([
                EditAction::make()
                    ->after(function ($livewire, $record) {
                        $pengajuan = $livewire->getOwnerRecord();
                        // $pengajuan->updateTotalHarga();
                        $nilai = $pengajuan->updateNilai();

                        $uuid = Filament::getTenant()->id;

                        // dd($uuid);
                        // $nilai = $pengajuan->nilai;
                        $level = Level::where('company_id', $uuid)
                            ->where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            // Ambil step pertama berdasarkan urutan step
                            $firstStep = $level->levelSteps()->orderBy('step')->first();

                            // Ambil nama role dari relasi role di levelStep
                            $roleId = optional($firstStep?->role)->id;

                            $pengajuan->update([
                                'nilai' => $nilai,
                                'level_id' => $level->id,
                                'dalam_review' => $roleId, // kolom ini sekarang menyimpan Id role
                            ]);
                        }
                    }),
                DeleteAction::make()
                    ->after(function ($livewire, $record) {
                        $pengajuan = $livewire->getOwnerRecord();
                        // $pengajuan->updateTotalHarga();
                        $nilai = $pengajuan->updateNilai();

                        $uuid = Filament::getTenant()->id;

                        // dd($uuid);
                        // $nilai = $pengajuan->nilai;
                        $level = Level::where('company_id', $uuid)
                            ->where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            // Ambil step pertama berdasarkan urutan step
                            $firstStep = $level->levelSteps()->orderBy('step')->first();

                            // Ambil nama role dari relasi role di levelStep
                            $roleId = optional($firstStep?->role)->id;

                            $pengajuan->update([
                                'nilai' => $nilai,
                                'level_id' => $level->id,
                                'dalam_review' => $roleId, // kolom ini sekarang menyimpan Id role
                            ]);
                        }
                    })
            ]);
    }
}
