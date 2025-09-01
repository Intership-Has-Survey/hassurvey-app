<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Level;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BankAccount;
use App\Traits\GlobalForms;
use Filament\Support\RawJs;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\PengajuanDanaResource\RelationManagers\DetailPengajuansRelationManager;


class PengajuanDanasRelationManager extends RelationManager
{
    use GlobalForms;
    protected static string $relationship = 'pengajuanDanas';
    protected static ?string $title = 'Pengajuan Dana';

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
                TextColumn::make('judul_pengajuan'),
                TextColumn::make('bank.nama_bank'),
                TextColumn::make('bankAccount.no_rek')->label('Nomor Rekening'),
                TextColumn::make('bankAccount.nama_pemilik')->label('Nama Pemilik'),
                TextColumn::make('nilai')->money('IDR'),
                TextColumn::make('user.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
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
            ->actions([
                ViewAction::make(),
                EditAction::make()
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
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getRelations(): array
    {
        return [
            DetailPengajuansRelationManager::class,
        ];
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
                'level_id'     => $level->id,
                'dalam_review' => $roleName,
            ]);
        }
    }
}
