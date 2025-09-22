<?php

namespace App\Filament\Resources\SewaResource\RelationManagers;

use Filament\Tables;
use App\Models\Level;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Traits\GlobalForms;
use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;

class PengajuanDanasRelationManager extends RelationManager
{
    use GlobalForms;
    protected static string $relationship = 'pengajuanDanas';
    protected static ?string $title = 'Pengajuan Dana';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                self::getPengajuanDanaForm()
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_pengajuan')
            ->columns([
                Tables\Columns\TextColumn::make('judul_pengajuan'),
                Tables\Columns\TextColumn::make('bank.nama_bank'),
                Tables\Columns\TextColumn::make('bank.accounts.no_rek')->label('Nomor Rekening'),
                Tables\Columns\TextColumn::make('bank.accounts.nama_pemilik')->label('Nama Penerima'),
                Tables\Columns\TextColumn::make('nilai')->money('IDR'),
                Tables\Columns\TextColumn::make('user.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($livewire, $record) {
                        // dd($record);
                        $record->updateTotalHarga();

                        $uuid = Filament::getTenant()->id;

                        // dd($uuid);
                        $nilai = $record->nilai;
                        $level = Level::where('company_id', $uuid)
                            ->where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();

                        if ($level) {
                            $firstStep = $level->levelSteps()->orderBy('step')->first();
                            $roleName = optional($firstStep?->role)->id;

                            $record->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName,
                            ]);
                        }
                    }),

            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make()
                    ->after(function ($livewire, $record) {
                        $record->updateTotalHarga();
                        $nilai = $record->nilai;
                        $level = Level::where('max_nilai', '>=', $nilai)
                            ->orderBy('max_nilai')
                            ->first();
                        if ($level) {
                            $firstStep = $level->levelSteps()->orderBy('step')->first();
                            $roleName = optional($firstStep?->role)->id;

                            $record->update([
                                'level_id'     => $level->id,
                                'dalam_review' => $roleName,
                            ]);
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
        $pengajuan = $this->record;
        $pengajuan->updateTotalHarga();
        $nilai = $pengajuan->nilai;
        $level = Level::where('max_nilai', '>=', $nilai)
            ->orderBy('max_nilai')
            ->first();

        if ($level) {
            $firstStep = $level->levelSteps()->orderBy('step')->first();
            $roleName = optional($firstStep?->role)->id;

            $pengajuan->update([
                'level_id' => $level->id,
                'dalam_review' => $roleName,
            ]);
        }
    }

    protected function getRelations(): array
    {
        return [
            \App\Filament\Resources\PengajuanDanaResource\RelationManagers\DetailPengajuansRelationManager::class,
        ];
    }
}
