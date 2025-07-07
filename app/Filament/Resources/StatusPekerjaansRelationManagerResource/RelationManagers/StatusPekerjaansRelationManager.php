<?php

namespace App\Filament\Resources\StatusPekerjaansRelationManagerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class StatusPekerjaansRelationManager extends RelationManager
{
    protected static string $relationship = 'statusPekerjaans';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('pekerjaan_lapangan')->required(),
            TextInput::make('proses_datagambar')->required(),
            TextInput::make('laporan')->required(),
            TextInput::make('keterangan')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('pekerjaan_lapangan'),
            TextColumn::make('proses_datagambar'),
            TextColumn::make('laporan'),
            TextColumn::make('keterangan'),
        ]);
    }
}
