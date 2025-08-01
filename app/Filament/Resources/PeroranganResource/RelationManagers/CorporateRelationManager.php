<?php

namespace App\Filament\Resources\PeroranganResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
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
                    ->maxLength(255),
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
