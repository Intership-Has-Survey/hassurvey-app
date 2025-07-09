<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class PersonelsRelationManager extends RelationManager
{
    protected static string $relationship = 'personels';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('personel_id')
                    ->relationship('personel', 'nama_personel')
                    ->searchable()
                    ->preload()
                    ->label('Personel')
                    ->required()
                    ->createOptionForm([
                        Select::make('jenis_personel')
                            ->options([
                                'surveyor' => 'surveyor',
                                'asisten surveyor' => 'asisten surveyor',
                                'driver' => 'driver',
                                'drafter' => 'drafter',
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('nama_personel')
                            ->label('Nama Personel')
                            ->required(),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('User')
                            ->required()
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_personel')
            ->columns([
                Tables\Columns\TextColumn::make('nama_personel'),
                Tables\Columns\TextColumn::make('jenis_personel'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\AttachAction::make()
                    ->label('Tambahkan Personel')
                    ->modalHeading('Tambah Personel ke Proyek')
                    ->preloadRecordSelect()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        // Select::make('peran')
                        //     ->options([
                        //         'surveyor' => 'surveyor',
                        //         'asisten surveyor' => 'asisten surveyor',
                        //         'driver' => 'driver',
                        //         'drafter' => 'drafter',
                        //     ])
                        //     ->required()
                        //     ->native(false),
                        Select::make('peran')
                            ->label('Jenis Pekerjaan')
                            ->options(\App\Models\Kategori::pluck('nama', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                    ])
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
