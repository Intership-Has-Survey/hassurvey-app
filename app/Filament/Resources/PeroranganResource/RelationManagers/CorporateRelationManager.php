<?php

namespace App\Filament\Resources\PeroranganResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\CorporateResource;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;

class CorporateRelationManager extends RelationManager
{
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->corporates()->exists();
    }
    protected static string $relationship = 'corporates';

    protected static ?string $title = 'Perusahaan Terkait';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->required()
                    ->label('Nama Perusahaan'),
                TextInput::make('nib')
                    ->label('NIB')
                    ->unique()
                    ->nullable(),
                TextInput::make('level')
                    ->label('Level'),
                TextInput::make('email')
                    ->email()
                    ->label('Email')
                    ->unique(),
                TextInput::make('telepon')
                    ->tel()
                    ->label('Telepon'),
                TextInput::make('provinsi')
                    ->label('Provinsi'),
                TextInput::make('kota')
                    ->label('Kota'),
                TextInput::make('kecamatan')
                    ->label('Kecamatan'),
                TextInput::make('desa')
                    ->label('Desa'),
                TextInput::make('detail_alamat')
                    ->label('Detail Alamat')
                    ->textarea(),
                TextInput::make('user_id')
                    ->label('User ID'),
                Forms\Components\Placeholder::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i') ?? '-'),
                Forms\Components\Placeholder::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->content(fn ($record) => $record?->updated_at?->format('d/m/Y H:i') ?? '-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('level'),
                TextColumn::make('email'),
                TextColumn::make('telepon'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make('goToCorporate')
                    ->label('Lihat Perusahaan')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn($record): string => CorporateResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
