<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectpersonelRelationManager extends RelationManager
{
    protected static string $relationship = 'personel';

    public function form(Form $form): Form
    {
        // dump($this->getOwnerRecord()); // akan tampilkan data Project saat ini
        // dump($this->getRelationship()->getRelated()); // akan tampilkan instance model Personel
        // dump($this->getOwnerRecord());
        // dump($this->getModel());
        return $form
            ->schema([

                // Forms\Components\TextInput::make('anjay')->required(),
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
                            ->required(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_personel')
            ->columns([
                // Tables\Columns\TextColumn::make('nama_personel'),
                // BadgeColumn::make('status')
                //     ->label('Status')
                //     ->colors([
                //         'success' => 'tersedia',
                //         'danger' => 'dalam projek',
                //     ])->getStateUsing(fn($record) => $record->status),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
