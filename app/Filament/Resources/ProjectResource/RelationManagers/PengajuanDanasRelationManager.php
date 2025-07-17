<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProjectResource\RelationManagers;

class PengajuanDanasRelationManager extends RelationManager
{
    protected static string $relationship = 'pengajuanDanas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul_pengajuan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Select::make('tipe_pengajuan')
                    ->label('Tipe Pengajuan')
                    ->options([
                        'project' => 'Untuk Proyek',
                        'inhouse' => 'In-House (Internal)',
                    ])
                    ->live()
                    ->required()
                    ->dehydrated(false),

                Forms\Components\Textarea::make('deskripsi_pengajuan')
                    ->label('Deskripsi Umum')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('nama_bank')->maxLength(255),
                Forms\Components\TextInput::make('nomor_rekening')->maxLength(255),
                Forms\Components\TextInput::make('nama_pemilik_rekening')->maxLength(255),
                Forms\Components\Hidden::make('user_id')->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_pengajuan')
            ->columns([
                Tables\Columns\TextColumn::make('judul_pengajuan'),
                Tables\Columns\TextColumn::make('deskripsi_pengajuan'),
                Tables\Columns\TextColumn::make('nama_bank'),
                Tables\Columns\TextColumn::make('nomor_rekening'),
                Tables\Columns\TextColumn::make('nama_pemilik_rekening'),
                Tables\Columns\TextColumn::make('user.name'),
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
                Tables\Actions\Action::make('editPengajuanDana')
                    ->label('Edit Pengajuan Dana')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->url(
                        fn($record) => $record->project()
                            ? route('filament.admin.resources.pengajuan-danas.edit', $record->id)
                            : route('filament.admin.resources.pengajuan-danas.create', ['project_id' => $record->id])
                    )
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getRelations(): array
    {
        return [
            \App\Filament\Resources\PengajuanDanaResource\RelationManagers\DetailPengajuansRelationManager::class,
        ];
    }
}
