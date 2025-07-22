<?php

namespace App\Filament\Resources\PengajuanDanaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use App\Models\Level;

class DetailPengajuansRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPengajuans';
    protected static ?string $title = 'Rincian Pengajuan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('deskripsi')->required()->columnSpan(2),
                Forms\Components\TextInput::make('qty')->required()->numeric()->default(1),
                Forms\Components\TextInput::make('harga_satuan')->required()->numeric()->prefix('Rp'),
            ])->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('deskripsi')
            ->columns([
                Tables\Columns\TextColumn::make('deskripsi'),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('harga_satuan')->money('IDR'),
                Tables\Columns\TextColumn::make('total')
                    ->state(fn($record) => $record->qty * $record->harga_satuan)
                    ->money('IDR'),
            ])
            ->headerActions([
                CreateAction::make()
                    // ->after(function ($livewire, $record) {
                    //     // Update total harga (jika diperlukan)
                    //     $livewire->getOwnerRecord()->updateTotalHarga();

                    //     // Ambil nilai pengajuan
                    //     $nilai = $record->nilai;

                    //     // Cari level yang cocok berdasarkan max_nilai
                    //     $level = Level::where('max_nilai', '>=', $nilai)
                    //         ->orderBy('max_nilai') // Ambil level dengan batas paling kecil yang masih mencukupi
                    //         ->first();

                    //     // Update level_id di record pengajuan jika ditemukan
                    //     if ($level) {
                    //         $record->update(['level_id' => $level->id]);
                    //     }
                    // })
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
                        $livewire->getOwnerRecord()->updateTotalHarga();
                    }),
                DeleteAction::make()
                    ->after(function ($livewire) {
                        $livewire->getOwnerRecord()->updateTotalHarga();
                    }),
            ]);
    }
}
